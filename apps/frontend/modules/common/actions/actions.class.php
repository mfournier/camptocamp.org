<?php
/**
 * common actions.
 *
 * @package    c2corg
 * @subpackage common
 * @version    SVN: $Id: actions.class.php 2476 2007-12-05 12:46:40Z fvanderbiest $
 */

class commonActions extends c2cActions
{
    /**
     * Executes error404 action
     *
     */
    public function executeError404()
    {
      // show the c2corg 404 error
    }
    
    /**
     * Executes getInfo action (tooltips on fields)
     *
     */
    public function executeGetinfo()
    {
        $info = $this->__($this->getRequestParameter('elt') . '_info') .
                '<div id="close_info">' . $this->__('close') . '</div>';
        return $this->renderText($info);
    }

    /**
     * Executes edit in place action
     *
     */
    public function executeEdit()
    {
        $text = $this->getRequestParameter('value');
        $culture = $this->getRequestParameter('lang');
        // restricted to moderators in security.yml

        // save text in a db field
        $status = Message::doSave($text, $culture);
        if ($status)
        {
            //$this->clearHomepageCache($culture);
            if (empty($text))
            {
                return $this->renderText($this->__('No message defined. Click to edit'));
            }
            return $this->renderText($text);
        }
        return $this->renderText($this->__('Message setting failed. This message has not been saved.'));
    }
    
    // set/unset main filter switch by AJAX
    public function executeSwitchallfilters()
    {
        $referer = $this->getRequest()->getReferer();
        
        if (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            $this->getUser()->setFiltersSwitch(false);
            $message = 'Filters have been deactivated';
        }
        else
        {
            $this->getUser()->setFiltersSwitch(true);
            $message = 'Filters have been activated';
        }
    
        return $this->setNoticeAndRedirect($message, $referer);
    }
    
    // one click site customization    
    public function executeCustomize()
    {
        $referer = $this->getRequest()->getReferer();

        $alist = sfConfig::get('app_activities_list');
        array_shift($alist); // to remove 0

        if ($this->hasRequestParameter('activity'))
        {
            $activity = $this->getRequestParameter('activity', 0) - 1; // comprised between 0 and 7
            /*
            1: skitouring
            2: snow_ice_mixed
            3: mountain_climbing
            4: rock_climbing
            5: ice_climbing
            6: hiking
            7: snowshoeing
            8: paragliding
            */
        }
        else if ($this->hasRequestParameter('activity_name')) // got here by activity_name
        {
            $name = $this->getRequestParameter('activity_name');
            foreach ($alist as $a => $a_name)
            {
                if ($a_name == $name) $activity = $a;
            }
        }
        else
        {
            $activity = -1;
        }

        $user = $this->getUser();
        if ($user->isConnected())
        {
            $user_id = $user->getId();
        }
        else
        {
            $user_id = null;
        }

        if (array_key_exists($activity, $alist))
        {
            if ((c2cPersonalization::getInstance()->getActivitiesFilter() == array($activity+1))
                && ($this->hasRequestParameter('activity')))
            {
                // we disactivate the previously set quick filter on this activity
                c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_activities_name'), array(), $user_id);
                return $this->setNoticeAndRedirect("c2c is no more customized with activies", $referer);
            }
            else
            {
                // we build a simple activity filter with one activity:
                c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_activities_name'), array($activity+1), $user_id);
                // we need to activate main filter switch:
                $user->setFiltersSwitch(true);
                $activity_name = $alist[$activity];
                return $this->setNoticeAndRedirect("c2c customized for $activity_name !", $referer);
            }
        }
        else
        {
            return $this->setNoticeAndRedirect('could not understand your request', $referer);
        }
    }  
    
}
