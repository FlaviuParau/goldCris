<?php

class Blugento_Catalog_Helper_Output extends Mage_Catalog_Helper_Output
{
	/**
	 * Prepare product attribute html output
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param string $attributeHtml
	 * @param string $attributeName
	 * @return  string
	 * @throws Exception
	 */
	public function productAttribute($product, $attributeHtml, $attributeName)
	{
		$attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeName);
		if ($attribute && $attribute->getId() && ($attribute->getFrontendInput() != 'media_image')
			&& (!$attribute->getIsHtmlAllowedOnFront() && !$attribute->getIsWysiwygEnabled())) {
			if ($attribute->getFrontendInput() != 'price') {
				$attributeHtml = $this->escapeHtml($attributeHtml);
			}
			if ($attribute->getFrontendInput() == 'textarea') {
				$attributeHtml = nl2br($attributeHtml);
			}
		}
		if ($attribute->getIsHtmlAllowedOnFront() && $attribute->getIsWysiwygEnabled()) {
			if (Mage::helper('catalog')->isUrlDirectivesParsingAllowed()) {
				$attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
			}
		}
		
		if ($attribute->getFrontendInput() == 'multiselect') {
			if ($product->getAttributeText($attribute->getAttributeCode())) {
				$attributeHtml = $product->getAttributeText($attribute->getAttributeCode());
			}
		}
		
		$attributeHtml = $this->process('productAttribute', $attributeHtml, array(
			'product' => $product,
			'attribute' => $attributeName
		));
		
		return $attributeHtml;
	}
}
