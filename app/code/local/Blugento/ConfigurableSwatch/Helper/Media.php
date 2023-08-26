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
 * @package     Blugento_ConfigurableSwatch
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class implementing the media fallback layer for swatches
 */
class Blugento_ConfigurableSwatch_Helper_Media extends Mage_Core_Helper_Abstract
{
    const MEDIA_GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    const SWATCH_WYSIWYG_MEDIA_DIR = 'wysiwyg/swatches';

    const SWATCH_IMAGE_DEF_EXT = '.png';

    /**
     * ID of attribute to be used for swatches on product listing
     *
     * @var string
     */
    protected $_swatchAttributeId = null;

    /**
     * Attribute model to be used for swatches on product listing
     *
     * @var Mage_Catalog_Model_Product_Type_Configurable_Attribute
     */
    protected $_swatchAttribute = null;

    /**
     * Set child_attribute_label_mapping on products with attribute label -> product mapping
     * Depends on following product data:
     * - product must have children products attached
     *
     * @param array $parentProducts
     * @param $storeId
     * @param bool $onlyListAttributes
     * @return void
     */
    public function attachProductChildrenAttributeMapping(array $parentProducts, $storeId)
    {

        $attrIds = array();
	    $arrt    = array();
	    
        /* @var $parentProduct Mage_Catalog_Model_Product */
        foreach ($parentProducts as $parentProduct) {

            if (!$parentProduct->isConfigurable()) {
                continue;
            }
            
            $_attributes = $parentProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($parentProduct);
            if (count($_attributes) > 1) {
                continue;
            }
            
            foreach ($_attributes as $_attribute) {
	            $ss = array();
	            foreach ($_attribute['values'] as $option) {
		            $label = $option['label'];
		            $value = $option['value_index'];
		
		            if ($value == '') {
			            continue;
		            }
		            $ss[$value] = $label;
	            }
	
	            $arrt[$_attribute['attribute_id']] = $ss;
                $attrIds[$parentProduct->getId()] = $_attribute['attribute_id'];
	
	            $childProducts = Mage::getModel('catalog/product_type_configurable')
		            ->getUsedProducts(null, $parentProduct);
	
	            $childMap = array();
	            foreach ($childProducts as $childProduct) {
		            $stockItem = $childProduct->getStockItem();
		            $isInStock = $stockItem->getIsInStock();
		            if (!$childProduct->hasData($_attribute['attribute_code'])
			            || (!$isInStock && !Mage::helper('cataloginventory')->isShowOutOfStock())
		            ) {
			            $ss[] = array(
				            $childProduct->getId(),
				            $_attribute['attribute_code'],
			            );
			            continue;
		            }
		
		            $optionId = $childProduct->getData($_attribute['attribute_code']);
		
		            $childMap[] = array(
			            'child_id'        => $childProduct->getId(),
			            'label'           => $arrt[$_attribute['attribute_id']][$optionId],
			            'option_id'       => $optionId,
			            'child_qty'       => $stockItem->getQty(),
			            'child_backorder' => $stockItem->getBackorders(),
			            'stock_item'      => $stockItem->getIsInStock(),
		            );
		
	            }
	
	            $parentProduct->setListSwatchesAttrValues($childMap);
	            $parentProduct->setSwatchAttrId($_attribute['attribute_id']);
            }
        }
    }

    /**
     * Return the default image width
     * @return mixed
     */
    public function getDefaultImageWidth()
    {
        return Mage::getStoreConfig('blugento_configurableswatch/general/img_width');
    }

    /**
     * Return the default image height
     * @return mixed
     */
    public function getDefaultImageHeight()
    {
        return Mage::getStoreConfig('blugento_configurableswatch/general/img_height');
    }

    public function getSwatchImageUrl($optionLabel, $productId = null)
    {
    	if (!Mage::helper('blugento_configurableswatch')->isSwatchImageEnabled()) {
    		return null;
	    }
    	
        /**
         * Return def magento file path
         */
        $query = "
        SELECT value, label  
        FROM `catalog_product_entity_media_gallery` AS a
        LEFT JOIN `catalog_product_entity_media_gallery_value` AS b ON a.value_id = b.value_id
        WHERE attribute_id = 88 AND label !='' AND entity_id = " . $productId;

        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $images = $readConnection->fetchAll($query);

        $imageFileDir = array(
            Mage::getBaseUrl('media'),
            Mage_ConfigurableSwatches_Helper_Productimg::SWATCH_CACHE_DIR,
            Mage::app()->getStore()->getId(),
            Mage_ConfigurableSwatches_Helper_Productimg::SWATCH_DEFAULT_WIDTH . 'x' . Mage_ConfigurableSwatches_Helper_Productimg::SWATCH_DEFAULT_HEIGHT,
            'product'
        );
        $url = str_replace('//', '/', implode('/', $imageFileDir));

        $defaultImagePath = null;
        if (count($images)) {
            foreach ($images as $image) {
                $value = isset($image['value']) ? $image['value'] : '';
                $label = isset($image['label']) ? $image['label'] : '';

                if ($label == $optionLabel) {
                    $defaultImagePath = str_replace('/', DS, Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $value);
                }
            }
        }

        /**
         * Return module file path
         */
        $defImageWidth = $this->getDefaultImageWidth();
        $defImageHeight = $this->getDefaultImageHeight();

        $mediaCatalogPath = Mage::getBaseDir('media') . DS . 'catalog';

        $catalogSwatchCacheDir = $mediaCatalogPath . DS . 'swatches' . DS . 'cache' . DS . Mage::app()->getStore()->getId() . DS . $productId  . DS . $defImageWidth . 'x' . $defImageHeight;

        if ($catalogSwatchCacheDir && !file_exists($catalogSwatchCacheDir)) {
            mkdir($catalogSwatchCacheDir, 0777, true);
        }

        $cacheFilePath = $catalogSwatchCacheDir . DS . $optionLabel . self::SWATCH_IMAGE_DEF_EXT;

        $cacheImageUrl = Mage::getBaseUrl('media') . 'catalog/swatches/cache/' . Mage::app()->getStore()->getId() . '/' . $productId . '/' . $defImageWidth . 'x' . $defImageHeight . '/' . $optionLabel . self::SWATCH_IMAGE_DEF_EXT;

        if (file_exists($cacheFilePath)) {
            return $cacheImageUrl;
        } else {

            $sourceFilePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . self::SWATCH_WYSIWYG_MEDIA_DIR . DS . $optionLabel . self::SWATCH_IMAGE_DEF_EXT;

            if (file_exists($sourceFilePath)) {
                $processor = new Varien_Image($sourceFilePath);
                $processor->resize($defImageWidth, $defImageHeight);
                $processor->save($cacheFilePath);

                if (file_exists($cacheFilePath)) {
                    return $cacheImageUrl;
                }
            }

            if (file_exists($defaultImagePath)) {
                $processor = new Varien_Image($defaultImagePath);
                $processor->resize($defImageWidth, $defImageHeight);
                $processor->save($cacheFilePath);

                if (file_exists($cacheFilePath)) {
                    return $cacheImageUrl;
                }
            }


        }

        return null;
    }

    public function getParentProductAttribute($_productId, $attributeName)
    {
        $_product = Mage::getModel('catalog/product')->load($_productId);

        $associated_products = $_product->loadByAttribute('sku', $_product->getSku())->getTypeInstance()->getUsedProducts();

        foreach ($associated_products as $assoc) {
            $dados[] = $assoc->getId().":'".($assoc->image == "no_selection" || $assoc->image == "" ?
                    Mage::helper('catalog/image')->init($_product, 'image', $_product->image)->resize(368,368) :
                    $this->helper('catalog/image')->init($assoc, 'image', $assoc->image)->resize(368,368))."'";
        }

        echo implode(',', $dados ); die();

    }
}
