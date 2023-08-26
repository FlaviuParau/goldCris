<?php
class Blugento_ConfigurableSwatches_Helper_Mediafallback extends Mage_ConfigurableSwatches_Helper_Mediafallback
{
    protected function _resizeProductImage($product, $type, $keepFrame, $image = null, $placeholder = false)
    {
	    $_productImageBackgroundColor = Mage::helper('blugento_theme')->getVariable('product-view__product-image_background-color', 'scss', '#fff');
	    
	    if (!preg_match('/^#[a-f0-9]{6}$/i', $_productImageBackgroundColor)) {
		    $_productImageBackgroundColor = '#fff';
	    }
	    
	    $_productImageBackgroundColorToHex = Mage::helper('blugento_theme')->hex2rgb($_productImageBackgroundColor);
	    
        $hasTypeData = $product->hasData($type) && $product->getData($type) != 'no_selection';
        if ($image == 'no_selection') {
            $image = null;
        }
        if ($hasTypeData || $placeholder || $image) {
            $helper = Mage::helper('catalog/image')
                ->init($product, $type, $image)
                ->keepFrame(($hasTypeData || $image) ? $keepFrame : false)  // don't keep frame if placeholder
            ;

            $size = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_BASE_IMAGE_WIDTH);
            $height = Mage::getStoreConfig('catalog/product_image/base_height');
            
            if ($type == 'small_image') {
                $size = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_SMALL_IMAGE_WIDTH);
            }
            if (is_numeric($size)) {
                $helper->constrainOnly(false)->resize($size,$height)->backgroundColor($_productImageBackgroundColorToHex);
            }
            return (string)$helper;
        }
        return false;
    }
}