<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Page size source model.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_System_Config_Source_PageSize
{
    /**
     * Return array of possible sizes.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            Zend_Pdf_Page::SIZE_A4     => Mage::helper('blugento_pdf')->__('DIN A4'),
            Zend_Pdf_Page::SIZE_LETTER => Mage::helper('blugento_pdf')->__('Letter')
        );

        $options = array();
        foreach ($positions as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }

        return $options;
    }
}
