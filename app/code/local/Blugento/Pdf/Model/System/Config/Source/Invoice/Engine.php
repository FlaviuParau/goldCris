<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Pdf creation engine source model.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_System_Config_Source_Invoice_Engine
{
    /**
     * Config xpath to pdf engine node
     *
     */
    const XML_PATH_PDF_ENGINE = 'global/pdf/blugento_invoice_engines';

    /**
     * Return array of possible engines.
     *
     * @return array
     */
    public function toOptionArray()
    {
        // load default engines shipped with Mage_Sales and Blugento_Pdf
        $engines = array(
            ''                                     => Mage::helper('blugento_pdf')->__('Standard Magento'),
            'blugento_pdf/engine_invoice_default' => Mage::helper('blugento_pdf')->__('Standard Blugento')
        );

        // load additional engines provided by third party extensions
        $engineNodes = Mage::app()->getConfig()->getNode(self::XML_PATH_PDF_ENGINE);
        if ($engineNodes && $engineNodes->hasChildren()) {
            foreach ($engineNodes->children() as $engineName => $engineNode) {
                $className   = (string) $engineNode->class;
                $engineLabel = Mage::helper('blugento_pdf')->__((string) $engineNode->label);
                $engines[$className] = $engineLabel;
            }
        }

        $options = array();
        foreach ($engines as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }
        return $options;
    }
}
