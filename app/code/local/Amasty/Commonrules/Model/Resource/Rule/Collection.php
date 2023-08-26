<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */
class Amasty_Commonrules_Model_Resource_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _init($model, $resourceModel = null)
    {
        parent::_init($model, $resourceModel);
        return $this;
    }

    public function addAddressFilter($address)
    {
        $storeId = $address->getQuote()->getStoreId();
        $groupId = 0;
        if ($address->getQuote()->getCustomerId()){
            $groupId = $address->getQuote()->getCustomer()->getGroupId();
        }
        $this->addFieldToFilter('is_active', 1)
            ->addStoreFilter($storeId)
            ->addCustomerGroupFilter($groupId)
            ->addDateTimeFilter();

        return $this;
    }

    public function addStoreFilter($storeId)
    {
        $storeId = intVal($storeId);
        $this->getSelect()->where('stores="" OR stores REGEXP "(^|[,])' . $storeId . '([,]|$)"');

        return $this;
    }

    public function addCustomerGroupFilter($groupId)
    {
        $groupId = intVal($groupId);
        $this->getSelect()->where('cust_groups="" OR cust_groups REGEXP "(^|[,])' . $groupId . '([,]|$)"');

        return $this;
    }

    public function addDateTimeFilter()
    {
        $this->getSelect()->where('days="" OR days REGEXP "(^|[,])' . date("N").'([,]|$)"');

        $timeStamp = Mage::getModel('core/date')->date('H') * 100 + Mage::getModel('core/date')->date('i') + 1;

        $this->getSelect()->where('time_from="" OR time_from="0" OR time_to="" OR time_to="0" OR
            (time_from < ' . $timeStamp . ' AND time_to > ' . $timeStamp . ') OR
            (time_from < ' . $timeStamp . ' AND time_to < time_from) OR
            (time_to > ' . $timeStamp . ' AND time_to < time_from)');

        return $this;
    }

    public function addIsAdminFilter()
    {
        if ($this->_isAdmin()){
            $this->addFieldToFilter('for_admin', 1);
        }
        return $this;
    }

    protected function _isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin())
            return true;
        // for some reason isAdmin does not work here
        if (Mage::app()->getRequest()->getControllerName() == 'sales_order_create')
            return true;

        return false;
    }
    
    public function addBackorderFilter($quoteItems)
    {
        /**
         * @example of adding filter for new field to rules/restrictions modules.
         * //checking if dependent module has new field before apply filter
         */
        if (!$this->getResource()->hasField('out_of_stock')) {
            return $this;
        }

        $hasBackOrders = false;
        foreach ($quoteItems as $item){
            if ($item->getBackorders() > 0 ){
                $hasBackOrders = true;
                break;
            }
        }

        $hlp = Mage::helper('amcommonrules');
        $backOrdersCondition = array($hlp::ALL_ORDERS);
        if($hasBackOrders) {
            $backOrdersCondition[] = $hlp::BACKORDERS_ONLY;
        } else {
            $backOrdersCondition[] = $hlp::NON_BACKORDERS;
        }

        $this->addFieldToFilter('out_of_stock', array('in' => $backOrdersCondition));
        return $this;
    }
}