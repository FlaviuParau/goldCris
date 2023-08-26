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

abstract class Blugento_DesignCustomiser_Block_Adminhtml_Advanced_Edit_Tab_Variable_Abstract
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Collection model
     * @var Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract
     */
    protected $_collection = null;
    
    /**
     * Fields have variable children
     * @var boolean 
     */
    protected $_childrenUse = false;
    
    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_setCollection();
        if (!($this->_collection instanceof Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract)) {
            $this->_collection = null;
        }
    }
    
    /**
     * Set collection
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Variable_Abstract
     */
    abstract protected function _setCollection();

    /**
     * Prepare page form with the custom functionality
     *
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Styleing
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('styling_');

        Mage::helper('blugento_designcustomiser')->getDefinitionModel()->setSession(Mage::getSingleton('adminhtml/session'));

        /**
         * @var $collection Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract
         */
        $collection = array();
        
        if (!is_null($this->_collection)) {
            $collection = $this->_collection->load();
        }

        $data = array();

        if (count($collection)) {

            $data = $collection->getVariableValues();
            
            $this->_setupFormFields($collection, $form);
            
            $this->_setChildrenVariable($collection, $form);

        }

        $form->setValues($data);

        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    /**
     * Setup fieldsets, fields and renderer classes
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract $collection
     * @param Varien_Data_Form $form
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Variable_Abstract
     */
    protected function _setupFormFields(Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract $collection,
            Varien_Data_Form $form)
    {
        
        if (!count($collection)) {
            return $this;
        }
        
        $fieldsets = array();
        
        foreach ($collection as $variable) {
            /**
             * @var $variable Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
             */
            
            if ($variable->getParentId()) {
                continue;
            }

            $id = $variable->getId();
            $fieldsetName = $variable->getFieldset();
            $translationPrefix = $variable->getTranslationPrefix();
            $rendererClass = $variable->getRendererClass();
            $inputTypeForm = $variable->getInputTypeForm();

            $fieldsetId = 'fieldset_' . md5($fieldsetName);

            if (!isset($fieldsets[$fieldsetId])) {
                $fieldsets[$fieldsetId] = $form->addFieldset($fieldsetId, array(
                    'legend' => Mage::helper('blugento_designcustomiser')->__($translationPrefix . '/' . $fieldsetName)
                ));
            }

            $translationKey = $translationPrefix . '/title/' . $id;
            $fieldsets[$fieldsetId]->addField($id, $inputTypeForm, array(
                'name'  => $id,
                'label' => Mage::helper('blugento_designcustomiser')->__($translationKey),
                'title' => Mage::helper('blugento_designcustomiser')->__($translationKey)
            ));

            $form->getElement($id)->setRenderer(
                $this->getLayout()->createBlock($rendererClass)
            );

            $form->getElement($id)->setVariable($variable);
        }
        
        return $this;
    }
    
    /**
     * Set children variables in parent fields
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract $collection
     * @param Varien_Data_Form $form
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Variable_Abstract
     */
    protected function _setChildrenVariable(Blugento_DesignCustomiser_Model_Scss_Variable_Collection_Abstract $collection,
            Varien_Data_Form $form)
    {
        
        if (!count($collection) || !$this->_childrenUse) {
            return $this;
        }
        
        foreach ($collection as $variable) {
            /**
             * @var $variable Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
             */
            if (!$variable->getParentId()) {
                continue;
            }

            $form->getElement($variable->getParentId())->setData($variable->getType(), $variable);
        }
        
        return $this;
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
}
