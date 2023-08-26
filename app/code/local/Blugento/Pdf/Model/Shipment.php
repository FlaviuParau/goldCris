<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Shipment model rewrite.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_Shipment
{

    /**
     * The actual PDF engine responsible for rendering the file.
     *
     * @var Mage_Sales_Model_Order_Pdf_Abstract
     */
    private $_engine;

    /**
     * get pdf rendering engine
     *
     * @return Mage_Sales_Model_Order_Pdf_Abstract|Mage_Sales_Model_Order_Pdf_Shipment
     */
    protected function getEngine()
    {
        if (!$this->_engine) {
            $modelClass = Mage::getStoreConfig('sales_pdf/shipment/engine');
            $engine = Mage::getModel($modelClass);

            if (!$engine) {
                // Fallback to Magento standard shipment layout.
                $engine = new Mage_Sales_Model_Order_Pdf_Shipment();
            }

            $this->_engine = $engine;
        }

        return $this->_engine;
    }

    /**
     * get PDF object
     *
     * @param  array|Varien_Data_Collection $shipments shipments to generate pdfs for
     *
     * @return mixed
     */
    public function getPdf($shipments = array())
    {
        return $this->getEngine()->getPdf($shipments);
    }

}
