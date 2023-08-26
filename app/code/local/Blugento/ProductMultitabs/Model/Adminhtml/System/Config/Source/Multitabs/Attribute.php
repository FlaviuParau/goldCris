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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Model_Adminhtml_System_Config_Source_Multitabs_Attribute extends Mage_Core_Model_Abstract
{
    /**
     * Return all attributes type textarea and text
     *
     * @return array
     */
    public function getAllOptions()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection');

        $attr = array();
        $attr[0] = '';
        foreach ($attributes as $attribute) {
            if ($attribute['frontend_input'] == 'textarea' || $attribute['frontend_input'] == 'text') {
                if ($attribute['frontend_label'] != '') {
                    $attr[$attribute['attribute_code']] = $attribute['frontend_label'] . ' (' . $attribute['attribute_code'] . ')';
                }
            }
        }

        return $attr;
    }
}