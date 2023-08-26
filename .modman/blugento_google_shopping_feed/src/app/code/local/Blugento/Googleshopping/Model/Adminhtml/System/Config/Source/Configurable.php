<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Configurable
{

    /**
     * Options array
     *
     * @var array
     */
    public $options = null;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $storeId = Mage::helper('googleshopping')->getStoreIdConfig();
            $attributes = Mage::getModel("googleshopping/googleshopping")->getFeedAttributes($storeId, 'config');
            $attributesSkip = array('id', 'parent_id', 'price', 'availability', 'is_in_stock', 'qty', 'status', 'visibility');
            $att = array();
            foreach ($attributes as $key => $attribute) {
                if (!in_array($key, $attributesSkip) && !empty($key)) {
                    $label = !empty($attribute['label']) ? str_replace('g:', '', $attribute['label']) : $key;
                    $att[$label] = array('value' => $key, 'label' => $label);
                }
            }

            $this->options = $att;
        }

        return $this->options;
    }
}