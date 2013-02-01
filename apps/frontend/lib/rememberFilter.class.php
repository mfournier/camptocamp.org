<?php

// inspired from sfGuardSecurityFilter

/**
 * $Id: rememberFilter.class.php 2476 2007-12-05 12:46:40Z fvanderbiest $
 */
class rememberFilter extends sfFilter
{
    public function execute($filterChain)
    {
        $context = $this->getContext();
        $session_user = $context->getUser();
        $cookie_name = sfConfig::get('app_remember_key_cookie_name', 'c2corg_remember');
        $cookie_value = $context->getRequest()->getCookie($cookie_name);
        
        if ($this->isFirstCall() && !$session_user->isConnected() && !is_null($cookie_value))
        {
            c2cTools::log('{rememberFilter} user has a cookie, trying to auto login');
            $remember_key = Doctrine_Query::create()
                                          ->from('RememberKey rk')
                                          ->where('rk.remember_key = ?', $cookie_value)
                                          ->execute()
                                          ->getFirst();
            if ($remember_key)
            {
                c2cTools::log('{rememberFilter} user found from his cookie');
                
                $user = $remember_key->getUser();

                if ($user->exists())
                {
                    $session_user->signIn($user->get('private_data')->getLoginName(), 
                                          $user->get('private_data')->getPassword(), true, true);
                }

                // User has signed in, and is now correctly in symfony session. However, forums
                // and several personnalization functions rely on cookies, that will be sent with the request,
                // but are not yet 'available' from javascript if the value expired from previous sessions (they will be on next page)
                // easiest solution is to force the browser to reload the current page
                $request = $this->getContext()->getRequest();
                $this->getContext()->getController()->redirect($request->getUri());
            }
            else
            {
                // delete cookie value in client so that no more requests are made to the db
                sfContext::getInstance()->getResponse()->setCookie($cookie_name, '');
            }
        }
        
        $filterChain->execute();
    }
}
