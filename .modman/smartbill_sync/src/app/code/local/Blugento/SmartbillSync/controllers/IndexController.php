<?php

/**
 * Class Blugento_SmartbillSync_IndexController
 */
class Blugento_SmartbillSync_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Index Action
     */
    public function indexAction()
    {
        /* @var Blugento_SmartbillSync_Model_Sync $model */
        $model = Mage::getModel('Blugento_SmartbillSync_Model_Sync');

        $productsUpdated = $model->cronSyncStock('force');

        echo $productsUpdated . " products was updated. ";
    }
}
