<?php

class Blugento_GdprCookies_Block_Adminhtml_System_Config_Form_CookiesList extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('gdprcookies/system/config/cookieslist.phtml');
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
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/cookies/index');
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'gdprcookies_cookieslist',
                'label'     => $this->helper('adminhtml')->__('Manage Cookies List'),
                'onclick'   => "setLocation('$url')",
            ));

        return $button->toHtml();
    }
}