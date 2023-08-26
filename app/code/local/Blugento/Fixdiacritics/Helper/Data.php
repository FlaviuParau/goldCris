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
 * @package     Blugento_Fixdiacritics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Fixdiacritics_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function sanitizeText($text)
    {
        $search  = Mage::getStoreConfig('magento_corefixes/fix_diacritics/diacritics_to_replace');
        $search = explode(',', str_replace(' ', '', $search));

        $replace  = Mage::getStoreConfig('magento_corefixes/fix_diacritics/characters_for_replacement');
        $replace = explode(',', str_replace(' ', '', $replace));

        for ($i = 0; $i < count($search); $i++) {
            $text = str_replace($search[$i], $replace[$i], $text);
        }

        return $text;
    }
}