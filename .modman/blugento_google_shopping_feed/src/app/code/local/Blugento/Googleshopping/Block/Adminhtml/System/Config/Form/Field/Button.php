<?php

class Blugento_Googleshopping_Block_Adminhtml_System_Config_Form_Field_Button
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @var Mage_Adminhtml_Helper_Data
     */
    public $helper;

    /**
     * @inheritdoc.
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('blugento/googleshopping/system/config/test_button.phtml');
        $this->helper = Mage::helper('adminhtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return $this->helper->getUrl('adminhtml/googleshopping/selftest');
    }

    /**
     * @return string
     */
    public function getFlatcheck()
    {
        /** @var Blugento_Googleshopping_Model_Googleshopping $gsModel */
        $gsModel = Mage::getModel("googleshopping/googleshopping");

        /** @var Blugento_Googleshopping_Helper_Data $gsHelper */
        $gsHelper = Mage::helper("googleshopping");

        try {
            $flatProduct = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
            $bypassFlat = Mage::getStoreConfig('googleshopping/generate/bypass_flat');

            if ($flatProduct && !$bypassFlat) {
                $storeId = $gsHelper->getStoreIdConfig();
                $nonFlatAttributes = $gsHelper->checkFlatCatalog($gsModel->getFeedAttributes($storeId, 'flatcheck'));
                if (!empty($nonFlatAttributes)) {
                    return sprintf(
                        '<span class="googleshopping-flat">%s</span>',
                        $gsHelper->__('Possible data issue(s) found!')
                    );
                }
            }
        } catch (\Exception $e) {
            $gsHelper->addToLog('checkFlat', $e->getMessage());
        }

        return null;
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id'      => 'test_check_button',
                    'label'   => $this->helper('adminhtml')->__('Run'),
                    'onclick' => 'javascript:testModule(); return false;'
                )
            );

        return $button->toHtml();
    }
}