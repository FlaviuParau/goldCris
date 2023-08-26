<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Block_Adminhtml_Cms_Edit_Tab_Export
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Constructor
     *
     * Sets form urls for preview widget and loading widget editor window
     */
    public function __construct()
    {
        parent::__construct();
//        $this->setTemplate('blugento/homepagemanager/export.phtml');
    }

    /**
     * Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * Prepare page form with the custom functionality
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {

//        print_R(Mage::getConfig()->getNode('admin/routers'));
            $form = new Varien_Data_Form();
            $form = new Varien_Data_Form(array(
                'id' => 'config_form',
                'action' => $this->getUrl('*/adminhtml_homepagemanager/export'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ));
            $form->setHtmlIdPrefix('export_');

            $fieldset = $form->addFieldset('base_fieldset', array(
                'legend' => Mage::helper('blugento_homepagemanager')->__('Export'),
                'class' => 'fieldset-wide',
            ));

            $fieldset->addField('exportSubmit', 'submit', array(
                'value' => 'Export',
                'name' => 'export',
                'after_element_html' => '<small></small>',
                'class' => 'form-button',
                'tabindex' => 1
            ));

            $fieldset->addField('storeId','select',array(
                'label' => Mage::helper('blugento_homepagemanager')->__('Store'),
                'name' => 'storeId',
                'tabindex' => 1,
                'class' => 'form-select',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
            ));
            $fieldset->addField('file', 'file', array(
                'label' => Mage::helper('blugento_homepagemanager')->__('Upload'),
                'value' => 'Upload',
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
            $helper = Mage::helper('blugento_homepagemanager');
//        $data = $helper->getFinalCssDefinitionModel()->loadContent();
            $data = array();
            $form->setUseContainer(true);

            $form->addValues(array(
                'final_css' => $data
            ));

            $this->setForm($form);
            return parent::_prepareForm();

    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        if($this->canShowTab()) {
            return Mage::helper('blugento_homepagemanager')->__('Export');
        }
        return;
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blugento_homepagemanager')->__('Export');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        $id = Mage::getSingleton('admin/session')->getUser()->getId();
        $roleData = Mage::getModel('admin/user')->load($id)->getRole()->getRoleName();

        if ('Administrators'==$roleData) {
            return true;
        }
        return false;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        $id = Mage::getSingleton('admin/session')->getUser()->getId();
        $roleData = Mage::getModel('admin/user')->load($id)->getRole()->getRoleName();

        if ('Administrators'==$roleData) {
            return false;
        }
        return true;
    }
}