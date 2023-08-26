<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_Popup
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Popup_Model_System_Config_Source_Cms_Page extends Mage_Core_Model_Abstract
{
    protected $_options;

    public function getAllOptions()
    {
        $this->_options = Mage::getModel('adminhtml/system_config_source_cms_page')->toOptionArray();
        $options = $this->_options;

        array_unshift($options, array('value' => 'product', 'label' => 'Product Page'));
        array_unshift($options, array('value' => 'category', 'label' => 'Category Page'));
        array_unshift($options, array('value' => 'account', 'label' => 'Login and Register Pages'));
        array_unshift($options, array('value' => 'cart', 'label' => 'Cart Page'));
        array_unshift($options, array('value' => 'onepage', 'label' => 'Checkout Page'));
        array_unshift($options, array('value' => 'success', 'label' => 'Success Page'));

        return $options;
    }
}