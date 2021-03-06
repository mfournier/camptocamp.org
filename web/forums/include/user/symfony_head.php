<?php
if (c2cTools::mobileVersion())
{
    include_partial('common/mobile_header');
}
else
{
    include_partial('common/header');
}
if (sfConfig::get('app_production') != 1)
{
    include_partial('common/dev_env');
}

// FIXME: hack to retrieve symfony user, sometimes lost for some unknown raison
if (empty($sf_user))
{
    $sf_user = sfContext::getInstance()->getUser();
}

// we need to remove flash messages because the flash filter does not get executed in the forum
$userAttributeHolder = $sf_user->getAttributeHolder();
$names = $userAttributeHolder->getNames('symfony/flash');
if ($names)
{
    foreach ($names as $name)
    {
        $userAttributeHolder->remove($name, 'symfony/flash');
    }
}
