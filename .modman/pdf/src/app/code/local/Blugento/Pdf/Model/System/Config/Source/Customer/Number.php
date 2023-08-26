<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
/**
 * Customer number source model.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 * @copyright 2015 Blugento Team (http://www.blugento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
class Blugento_Pdf_Model_System_Config_Source_Customer_Number
{
    /**
     * Databasefield name for customers increment_id
     */
    const CUSTOMER_NUMBER_FIELD_INCREMENT_ID = 'increment_id';
    /**
     * Return array of possible positions.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $selectOptions = array(
            '' => Mage::helper('blugento_pdf')->__('Standard (entity_id)'),
            self::CUSTOMER_NUMBER_FIELD_INCREMENT_ID => Mage::helper('blugento_pdf')->__('Customer Increment ID (increment_id)')
        );
        $options = array();
        foreach ($selectOptions as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }
        return $options;
    }
}
