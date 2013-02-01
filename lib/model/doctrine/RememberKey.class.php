<?php
/**
 * $Id: RememberKey.class.php 2469 2007-12-04 13:32:13Z fvanderbiest $
 */
class RememberKey extends BaseRememberKey
{
    public static function deleteOtherKeysForUserId($user_id)
    {
        Doctrine_Query::create()
                      ->delete('RememberKey')
                      ->from('RememberKey rk')
                      ->where('rk.user_id = ?', $user_id)
                      ->execute();
    }
    
    public static function deleteOldKeys()
    {
    	// Get a new date formatter
        $dateFormat = new sfDateFormat();
    
    	$expiration_age = sfConfig::get('app_remember_key_expiration_age', 31 * 24 * 3600 );
        $expiration_time_value = $dateFormat->format( time() - $expiration_age, 'I' );
        Doctrine_Query::create()
                      ->delete('RememberKey')
                      ->from('RememberKey rk')
                      ->where('rk.created_at < ?', $expiration_time_value )
                      ->execute();
    }
    
    public static function existsKey($key)
    {
        $res = Doctrine_Query::create()
                             ->select('COUNT(rk.user_id) nb')
                             ->from('RememberKey rk')
                             ->where('rk.remember_key = ?', $key)
                             ->execute()
                             ->getFirst()->nb;
        if ($res)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
}
