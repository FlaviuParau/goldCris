<?php

class Blugento_GdprCookies_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
    * Set template
    */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('gdprcookies/system/config/button.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $url = Mage::getBaseUrl() . Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName') .'/system_config/edit/section/gdpr_cookies/key/' . Mage::getSingleton('adminhtml/url')->getSecretKey() ;
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'gdprcookies_button',
                'label' => $this->helper('adminhtml')->__('Jump to Other Scripts'),
                'onclick'   => "setLocation('$url')",
            ));
        return $button->toHtml();
    }
}
