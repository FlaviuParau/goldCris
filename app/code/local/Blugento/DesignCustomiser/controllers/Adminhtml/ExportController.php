<?php

class Blugento_DesignCustomiser_Adminhtml_ExportController extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('export_tabs');
        $this->_blockGroup         = 'blugento_designcustomiser';
        $this->_headerText= Mage::helper('blugento_designcustomiser')->__('Export/import Design');
    }
    /**
     * Prepare page form with the custom functionality
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form = new Varien_Data_Form(array(
            'id' => 'config_form',
            'action' => $this->getUrl('*/*/exportDesign'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setHtmlIdPrefix('export_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('blugento_designcustomiser')->__('Export'),
            'class'  => 'fieldset-wide',
        ));

        $fieldset->addField('exportSubmit', 'submit', array(
            'value' => 'Export',
            'name' => 'export',
            'after_element_html' => '<small></small>',
            'class' => 'form-button',
            'tabindex' => 1
        ));
        $fieldset->addField('file', 'file', array(
            'label'     => Mage::helper('blugento_designcustomiser')->__('Upload'),
            'value'  => 'Upload',
            'disabled' => false,
            'readonly' => true,
            'name' => 'filezip',
            'after_element_html' => '<small></small>',
            'tabindex' => 1
        ));
        $fieldset->addField('importSubmit', 'submit', array(
            'value' => 'Import',
            'name' => 'import',
            'after_element_html' => '<small></small>',
            'class' => 'form-button',
            'tabindex' => 1
        ));
        $helper = Mage::helper('blugento_designcustomiser');
//        $data = $helper->getFinalCssDefinitionModel()->loadContent();
        $data = array();
        $form->setUseContainer(true);

        $form->addValues(array(
            'final_css' => $data
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }
    public function getTabLabel()
    {
        if($this->canShowTab()) {
            return Mage::helper('blugento_designcustomiser')->__('Export');
        }
    }
    public function getTabTitle()
    {
        return Mage::helper('blugento_designcustomiser')->__('Export');
    }
    public function canShowTab()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/importexport');
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return !Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/importexport');
    }
}