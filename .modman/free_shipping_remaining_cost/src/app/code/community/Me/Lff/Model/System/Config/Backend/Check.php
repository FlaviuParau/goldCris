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
 * Class Me_Lff_Model_System_Config_Backend_Check
 */
class Me_Lff_Model_System_Config_Backend_Check extends Mage_Core_Model_Config_Data
{
    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        $enabled = $this->getValue();
        if ($enabled) {

            $_helper = $this->_getLffHelper();

            if (!$_helper->isFreeShippingEnabled()) {
                Mage::throwException(
                    $_helper->__(
                        'Free Shipping is disabled. Please <a href="%s">configure properly</a> Free Shipping before enable the extension.',
                        Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/carriers')
                    )
                );
            }

            if (!$_helper->isFreeShippingHasAmount()) {
                Mage::throwException(
                    $_helper->__(
                        'Free Shipping minimum order amount is zero. Please <a href="%s">configure properly</a> Free Shipping before enable the extension.',
                        Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/carriers')
                    )
                );
            }
        }

        return parent::save();
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
