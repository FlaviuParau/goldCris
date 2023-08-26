<?php

class Blugento_GdprCookies_Block_Adminhtml_Cookies_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gdprcookies';
        $this->_controller = 'adminhtml_cookies_list';
        $this->_headerText = Mage::helper('gdprcookies')->__('Cookies List');

        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('gdprcookies')->__('Add Cookie'));
    }
}