<?php

require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Permissions'.DS.'RoleController.php');

class Blugento_ExtendAdminhtml_Permissions_RoleController extends Mage_Adminhtml_Permissions_RoleController
{

    protected function _addUserToRole($userId, $roleId)
    {
        $user = Mage::getModel('admin/user')->load($userId);
        $user->setRoleId($roleId)->setUserId($userId);

        $user->add();
        return true;
    }
}