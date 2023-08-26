<?php
/**
 * Class Me_Lff_Model_System_Config_Backend_Check
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Lff_Model_System_Config_Backend_Taxsuffix
 */
class Me_Lff_Model_System_Config_Backend_Taxsuffix
{
    /**
     * var int
     */
    const EXCL_TAX = 1;

    /**
     * var int
     */
    const INCL_TAX = 2;

    /**
     * var int
     */
    const AUTO_TAX = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_helper = $this->_getLffHelper();

        return array(
            array('value' => self::EXCL_TAX, 'label' => $_helper->__('Show (excl. tax) after price')),
            array('value' => self::INCL_TAX, 'label' => $_helper->__('Show (incl. tax) after price')),
            array('value' => self::AUTO_TAX, 'label' => $_helper->__('Show (excl. tax) or (incl. tax) after price automatically depends on store settings'))
        );
    }

    /**
     * Get extension helper
     *
     * @return Me_Lff_Helper_Data
     */
    protected function _getLffHelper()
    {
        return Mage::helper('me_lff');
    }
}
