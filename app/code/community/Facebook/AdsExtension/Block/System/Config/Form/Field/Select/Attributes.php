<?php

class Facebook_AdsExtension_Block_System_Config_Form_Field_Select_Attributes
    extends Mage_Core_Block_Html_Select
{

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {

            /** @var Blugento_FullFeed_Helper_Data $helper */
            $helper = Mage::helper('Facebook_AdsExtension');
            $attributeOptions = $helper->getAttributeOptions();

//            $this->addOption('entity_id', 'Entity id (entity_id)');
            $this->addOption('qty', 'Qty (qty)');
            $this->addOption('is_in_stock', 'Is in stock (is_in_stock)');
            $this->addOption('media_gallery', 'Gallery Images (media_gallery)');

            foreach ($attributeOptions as $key=>$data) {
                $value = isset($data['value']) ? $data['value']: null;
                $label = isset($data['label']) ? $data['label']: null;
                if ($value && $label) {
                    $this->addOption($value, $label);
                }
            }
        }

        return parent::_toHtml();
    }

    /**
     * Get options of the element
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->_options;
        asort($options);

        return $options;
    }
}
