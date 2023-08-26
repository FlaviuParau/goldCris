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
class Blugento_Pdf_Model_System_Config_Source_Logo
{
    const LEFT = 'left';
    const CENTER = 'center';
    const RIGHT = 'right';
    const FULL_WIDTH = 'full_width';

    /**
     * Return array of possible positions.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            self::LEFT       => Mage::helper('blugento_pdf')->__('Left'),
            self::CENTER     => Mage::helper('blugento_pdf')->__('Center'),
            self::RIGHT      => Mage::helper('blugento_pdf')->__('Right'),
            self::FULL_WIDTH => Mage::helper('blugento_pdf')->__('Full width')
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
