<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */

class Amasty_Commonrules_Model_Observer 
{
    protected $_module = 'amcommonrules';

    /**
     * Append rule product attributes to select by quote item collection
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_SalesRule_Model_Observer
     */
    public function addProductAttributes(Varien_Event_Observer $observer)
    {
        // @var Varien_Object
        $attributesTransfer = $observer->getEvent()->getAttributes();

        $attributes = Mage::getResourceModel($this->_module . '/rule')->getAttributes();

        $result = array();
        foreach ($attributes as $code) {
            $result[$code] = true;
        }
        $attributesTransfer->addData($result);

        return $this;
    }

    public function handleNewConditions($observer)
    {
        $transport = $observer->getAdditional();
        $cond = $transport->getConditions();
        if (!is_array($cond)) {
            $cond = array();
        }

        $types = array(
            'customer' => Mage::helper('amcommonrules')->__('Customer Attributes'),
            'orders'   => Mage::helper('amcommonrules')->__('Purchases History'),
        );
        foreach ($types as $typeCode => $typeLabel) {
            $condition           = Mage::getModel('amcommonrules/rule_condition_' . $typeCode);
            $conditionAttributes = $condition->loadAttributeOptions()->getAttributeOption();

            $attributes = array();
            foreach ($conditionAttributes as $code=>$label) {
                $attributes[] = array(
                    'value' => 'amcommonrules/rule_condition_'.$typeCode.'|' . $code,
                    'label' => $label,
                );
            }
            $cond[] = array(
                'value' => $attributes,
                'label' => Mage::helper($this->_module)->__($typeLabel),
            );
        }
        $cond[] = array(
            'value' => 'amcommonrules/rule_condition_total',
            'label' => Mage::helper('amcommonrules')->__('Orders Subselection')
        );

        $transport->setConditions($cond);

        return $this;
    }
}