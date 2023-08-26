<?php

$isDevMode = Mage::getIsDeveloperMode();
Mage::setIsDeveloperMode(true);

$session = $adminuser = Mage::getSingleton('admin/session');
/* @var $adminuser Mage_Admin_Model_User */
// $adminuser = $session->getUser();
// $adminuser->setReloadAclFlag(true);
$session->refreshAcl();	

Mage::setIsDeveloperMode($isDevMode);