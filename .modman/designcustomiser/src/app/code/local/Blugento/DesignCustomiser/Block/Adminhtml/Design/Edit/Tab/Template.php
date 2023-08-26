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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Template extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Styling collection model
     * @var Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract
     */
    protected $_stylingCollection = null;

    /**
     * Prepare page form with the custom functionality
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('template_');

        $collectionTemplates = Mage::getModel('blugento_designcustomiser/template_collection')->load();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('blugento_designcustomiser')->__('Template'),
            'class'  => 'fieldset-wide',
        ));

        foreach ($collectionTemplates->getItems() as $item) {
            $fieldset->addField('template-' . $item->getId(), 'radio', array(
                'name'  => 'template',
                'label' => $item->getTitle(),
                'title' => $item->getTitle(),
                'value' => $item->getId(),
                'after_element_html' => '&nbsp;' . $item->getDescription(),
                'onclick' => "if (!confirm('" . Mage::helper('blugento_designcustomiser')->__('Your layout and color settings will be overwritten by the template. Do you really want to lose your settings?') . "')) {return false;} return true;"
            ));
        }

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
        return Mage::helper('blugento_designcustomiser')->__('Template');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blugento_designcustomiser')->__('Template');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Set collection
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Variable_Abstract
     */
    protected function _setStylingCollection()
    {
        $this->_stylingCollection = Mage::getSingleton('blugento_designcustomiser/scss_variable_collection');
        return $this;
    }
}
