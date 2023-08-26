<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Creditmemo model rewrite.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Creditmemo
{

    /**
     * The actual PDF engine responsible for rendering the file.
     * @var Mage_Sales_Model_Order_Pdf_Abstract
     */
    private $_engine;

    /**
     * get pdf renderer engine
     *
     * @return Mage_Sales_Model_Order_Pdf_Abstract|Mage_Sales_Model_Order_Pdf_Creditmemo
     */
    protected function getEngine()
    {
        if (!$this->_engine) {
            $modelClass = Mage::getStoreConfig('sales_pdf/creditmemo/engine');
            $engine = Mage::getModel($modelClass);

            if (!$engine) {
                // Fallback to Magento standard creditmemo layout.
                $engine = new Mage_Sales_Model_Order_Pdf_Creditmemo();
            }

            $this->_engine = $engine;
        }

        return $this->_engine;
    }

    /**
     * get pdf object
     *
     * @param  array|Varien_Data_Collection $creditmemos creditmemos to render
     *
     * @return Zend_Pdf
     */
    public function getPdf($creditmemos = array())
    {
        return $this->getEngine()->getPdf($creditmemos);
    }

}
