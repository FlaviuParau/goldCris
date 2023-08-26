<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Mainimage
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
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('frontend_input', 'media_image');

            $this->options[] = array(
                'value' => '',
                'label' => Mage::helper('googleshopping')->__('Use default')
            );

            foreach ($attributes as $attribute) {
                $this->options[] = array(
                    'value' => $attribute->getData('attribute_code'),
                    'label' => str_replace("'", "", $attribute->getData('frontend_label'))
                );
            }

            $this->options[] = array(
                'value' => 'first',
                'label' => Mage::helper('googleshopping')->__('First Image')
            );
            $this->options[] = array(
                'value' => 'last',
                'label' => Mage::helper('googleshopping')->__('Last Image')
            );
        }

        return $this->options;
    }
}