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
 * @package     Blugento_FormsGenerator
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FormsGenerator_Model_System_Config_Source_Cms_Page extends Mage_Core_Model_Abstract
{
    static public function getOptionArray()
    {
        $options = Mage::getSingleton('adminhtml/system_config_source_cms_page')->toOptionArray();
        $pages = [];

        $pages[0] = Mage::helper('blugento_formsgenerator')->__('Select a success page...');
        foreach($options as $option){
            $pages[$option['value']] = $option['label'];
        }

        return $pages;
    }
}