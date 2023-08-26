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

class Blugento_Popup_Model_System_Config_Source_Category
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = Mage::getResourceModel('catalog/category_collection')->addAttributeToSelect('name');
        $categories = $collection->load()->toArray(array('entity_id','name'));

        $options = array();

        $options[] = array(
            'value' => 'all',
            'label' => 'All Categories'
        );
        foreach ($categories as $_category) {
            if ($_category['name'] != 'Root Catalog' && $_category['name'] != 'Default Category') {
                $options[] = array(
                    'value' => $_category['entity_id'],
                    'label' => $_category['name']
                );
            }
        }

        return $options;
    }
}
