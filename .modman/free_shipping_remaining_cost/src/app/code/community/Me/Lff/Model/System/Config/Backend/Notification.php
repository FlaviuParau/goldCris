<?php
/**
 * Class Me_Lff_Model_System_Config_Backend_Notification
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Lff_Model_System_Config_Backend_Notification
 */
class Me_Lff_Model_System_Config_Backend_Notification extends Mage_Core_Model_Config_Data
{
    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        $value = $this->getValue();
        if ($value) {
            if (strpos($value, '%s') === false) {
                Mage::throwException($this->_getLffHelper()->__('The notification text must include %s. Please correct it!'));
            }

        } else {
            Mage::throwException($this->_getLffHelper()->__('The notification text can not be empty. Please correct it!'));
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
