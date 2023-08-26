<?php

class Blugento_GdprCookies_Block_Adminhtml_Cookies_List_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cookies_list_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Cookie Infos');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => 'About the cookie',
            'title' => 'About the cookie',
            'content' => $this->getLayout()
                ->createBlock('gdprcookies/adminhtml_cookies_list_edit_tab_form')
                ->toHtml()
        ));

        return parent::_beforeToHtml();
    }
}