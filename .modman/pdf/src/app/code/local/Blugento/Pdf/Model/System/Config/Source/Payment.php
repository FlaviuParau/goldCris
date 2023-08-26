<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Payment method position source model.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_System_Config_Source_Payment
{
    const POSITION_HEADER = 'header';
    const POSITION_NOTE = 'note';

    /**
     * Return array of possible positions.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            '' => Mage::helper('blugento_pdf')->__('Hide payment method'),
            self::POSITION_HEADER => Mage::helper('blugento_pdf')->__('Header'),
            self::POSITION_NOTE => Mage::helper('blugento_pdf')->__('Notes area')
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
