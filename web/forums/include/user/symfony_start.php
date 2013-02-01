<?php
// include config vars that are needed for symfony
if (!defined('SF_ROOT_DIR'))
{
    define('SF_ROOT_DIR',    realpath(dirname(__file__).'/../../../..'));
    define('SF_APP',         'frontend');
    define('SF_ENVIRONMENT', 'prod');
    define('SF_DEBUG',       false);

    require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . 
             DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
}

$context = sfContext::getInstance();

// set the relative root to null, then the links will be OK.
$request = $context->getRequest();
$request->setRelativeUrlRoot('');

// We need to execute a fake remember_filter here ...
$remember_cookie = sfConfig::get('app_remember_key_cookie_name', 'c2corg_remember');
$cookie = $request->getCookie($remember_cookie);
$sf_user = $context->getUser();

if (!$sf_user->isConnected() && !is_null($cookie))
{
    // see rememberFilter from symfony to understand what happens here
    c2cTools::log('{fake rememberFilter in forum} user has a cookie, trying to auto login');
    $remember_key = Doctrine_Query::create()
                                          ->from('RememberKey rk')
                                          ->where('rk.remember_key = ?', $cookie)
                                          ->execute()
                                          ->getFirst();
    if ($remember_key)
    {
        c2cTools::log('{fake rememberFilter in forum} user found from his cookie');
                
        $dbuser = $remember_key->getUser();

        if ($dbuser->exists())
        {
            $private_data = $dbuser->get('private_data');
            $sf_user->signIn($private_data->getLoginName(), $private_data->getPassword(), true, true);
            $context->getController()->redirect($request->getUri());
        }
    }
    else
    {
        // delete cookie value in client so that no more requests are made to the db
        $response->setCookie($remember_cookie, '');
    }
}

// we include Partial helper to be able to use include_partial in forum
sfLoader::loadHelpers(array('I18N', 'Tag', 'Url', 'Partial'));

// we have to do this, else we get a PHP Fatal error:  
// Call to a member function formatExists() on a non-object in /usr/share/php/symfony/i18n/sfI18N.class.php on line 132
// cf. http://www.symfony-project.com/forum/index.php/m/32891/
$dir = sfConfig::get('sf_app_i18n_dir');
$i18n = $context->getI18N();
$i18n->setMessageSourceDir($dir, $sf_user->getCulture());

define('PUN_STATIC_URL', sfConfig::get('app_static_url'));
