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

class Blugento_DesignCustomiser_Block_Adminhtml_Layout_Edit_Tab_Layout
    extends Blugento_DesignCustomiser_Block_Adminhtml_Layout_Edit_Tab_Abstract
{
    /**
     * Fields have variable children
     *
     * @var boolean
     */
    protected $_childrenUse = true;

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('blugento_designcustomiser')->__('Layout Options');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blugento_designcustomiser')->__('Layout Options');
    }

    /**
     * Set collection
     *
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Layout_Edit_Tab_Layout
     */
    protected function _setCollection()
    {
        $this->_collection = Mage::getSingleton('blugento_designcustomiser/layout_variable_collection');
        return $this;
    }

    /**
     * Setup fieldsets, fields and renderer classes
     *
     * @param Blugento_DesignCustomiser_Model_Layout_Variable_Collection_Abstract $collection
     * @param Varien_Data_Form $form
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Layout_Edit_Tab_Layout
     */
    protected function _setupFormFields(Blugento_DesignCustomiser_Model_Layout_Variable_Collection_Abstract $collection,
                                        Varien_Data_Form $form)
    {
        if (!count($collection)) {
            return $this;
        }

        $tabs = array();
        $lastTabId = '^';

        foreach ($collection as $variable) {
            /**
             * @var $variable Blugento_DesignCustomiser_Model_Layout_Variable_Abstract
             */

            if ($variable->getParentId()) {
                continue;
            }

            $id = $variable->getId();
            $tab = $variable->getTab();
            $fieldsetName = $variable->getFieldset();
            $translationPrefix = $variable->getTranslationPrefix();
            $rendererClass = $variable->getRendererClass();
            $inputTypeForm = $variable->getInputTypeForm();

            $tabId = 'tab_' . md5($tab);
            $fieldsetId = $tabId . '_fieldset_' . md5($fieldsetName);
            $translationKey = $translationPrefix . '/title/' . $id;

            if (!isset($tabs[$tabId])) {
                $tabs[$tabId] = array();

                $after = ($tab == 'Other') ? false : $lastTabId;

                $tabs[$tabId]['element'] = $form->addFieldset($tabId, array(
                    'legend' => Mage::helper('blugento_designcustomiser')->__($translationPrefix . '/' . $tab)
                ), $after);

                $tabs[$tabId]['element']->setFieldsetContainerId('container_layout_' . $tabId);

                $tabs[$tabId]['fieldsets'] = array();

                $form->getElement($tabId)->setRenderer(
                    $this->getLayout()->createBlock(Mage::getConfig()->getBlockClassName('blugento_designcustomiser/adminhtml_form_renderer_fieldset_default'))
                );

                $lastTabId = ($tab == 'Other') ? '^' : $tabId;
            }

            if (!isset($tabs[$tabId]['fieldsets'][$fieldsetId])) {
                $tabs[$tabId]['fieldsets'][$fieldsetId] = $tabs[$tabId]['element']->addFieldset($fieldsetId, array(
                    'legend' => Mage::helper('blugento_designcustomiser')->__($translationPrefix . '/' . $fieldsetName)
                ));
            }

            $tabs[$tabId]['fieldsets'][$fieldsetId]->addField($id, $inputTypeForm, array(
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
}
