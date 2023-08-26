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
 * @package     Blugento_RichSnippets
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Richsnippets_Model_Attributes extends Mage_Core_Model_Abstract
{
	/**
	 * Get Product Attribute Value by Attribute Type
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param $attr
	 * @return mixed|string|null
	 */
	public function getAttributeValue($product, $attr)
	{
		$value = '';
		
		if ($product) {
			$type = $product->getResource()->getAttribute($attr)->getFrontendInput();
			
			if ($type == 'text' || $type == 'textarea') {
				$value = $product->getData($attr) ?: '';
			} elseif ($type == 'select') {
				$value = $product->getAttributeText($attr) ?: '';
			}
		}
		
		return $value;
	}
}
