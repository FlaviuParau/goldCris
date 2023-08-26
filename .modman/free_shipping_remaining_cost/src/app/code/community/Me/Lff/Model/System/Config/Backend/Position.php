<?php
/**
 * Class Me_Lff_Model_System_Config_Backend_Position
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Lff_Model_System_Config_Backend_Position
 */
class Me_Lff_Model_System_Config_Backend_Position
{
    /**
     * var int
     */
    const LEFT_SIDEBAR = 'left';

    /**
     * var int
     */
    const RIGHT_SIDEBAR = 'right';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_helper = $this->_getLffHelper();

        return array(
            array('value' => self::LEFT_SIDEBAR, 'label' => $_helper->__('Left Sidebar')),
            array('value' => self::RIGHT_SIDEBAR, 'label' => $_helper->__('Right Sidebar'))
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
