<?php

class Blugento_GdprCookies_Block_Adminhtml_Cookies_List_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        //you will notice that assigns the same blockGroup the Grid Container
        $this->_blockGroup = 'gdprcookies';
        // and the same container
        $this->_controller = 'adminhtml_cookies_list';
        //we define the labels for the buttons save and delete
        $this->_updateButton('save', 'label','Save Cookie');
        $this->_updateButton('delete', 'label', 'Delete Cookie');
    }

    /* Here, we look at whether it was transmitted item to form
     * to put the right text in the header (Add or Edit)
     */

    public function getHeaderText()
    {
        if( Mage::registry('cookies_list_data') && Mage::registry('cookies_list_data')->getId() )
        {
            return 'Edit a cookie '.$this->htmlEscape( Mage::registry('cookies_list_data')->getTitle() );
        }
        else
        {
            return 'Add a cookie';
        }
    }
}