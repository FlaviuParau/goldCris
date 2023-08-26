<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Logo position source model.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Model_System_Config_Source_Headerblocks
{
    const LEFT = 'left';
    const RIGHT = 'right';

    /**
     * Return array of possible positions.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            self::LEFT       => Mage::helper('blugento_pdf')->__('Left'),
            self::RIGHT      => Mage::helper('blugento_pdf')->__('Right'),
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
