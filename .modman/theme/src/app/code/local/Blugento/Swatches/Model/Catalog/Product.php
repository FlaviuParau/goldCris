<?php

/**
 * Catalog product model
 *
 * @method Mage_Catalog_Model_Resource_Product getResource()
 * @method Mage_Catalog_Model_Resource_Product _getResource()
 *
 * @category   Blugento
 * @package    Blugento_Swatches
 */
class Blugento_Swatches_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
	/**
	 * Returns end date of the special price
	 *
	 * @param Mage_Sales_Model_Quote_Item $item
	 * @return null|int
	 */
	public function getCustomQtyIncrements($item)
	{
		if (Mage::helper('core')->isModuleEnabled('Blugento_Qtyincrements') && !Mage::helper('qtyincrements')->isEnabled()) {
			return null;
		}
		
		$customQtyItemValues = Mage::getModel('qtyincrements/cart')->getItemCustomQtyData($item);
		
		if (is_null($this->_getData('custom_qty_increments'))) {
			$this->setData('custom_qty_increments', $customQtyItemValues[1]['value'] ? $customQtyItemValues[1]['value'] : 1);
		}
		
		return $this->_getData('custom_qty_increments');
	}
	
    /**
     * Retrive media gallery images
     *
     * @return Varien_Data_Collection
     * @throws Exception
     */
    public function getMediaGalleryImages()
    {
        if(!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {

            /** @var Blugento_Swatches_Helper_Data $swHelper */
            $swHelper = Mage::helper('blugento_swatches');

            $displayChGallery = $swHelper->displayChildrenGallery();
            $images = new Varien_Data_Collection();

            foreach ($this->getMediaGallery('images') as $image) {
                if ($image['disabled']) {
                    continue;
                }
                $image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
                $image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
                $image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
                $images->addItem(new Varien_Object($image));
            }

            if ($swHelper->isExtensionEnabled() && $displayChGallery) {
                foreach ($this->_getChildrenGallery() as $img) {
                    if ($img['disabled']) {
                        continue;
                    }

                    $img['url'] = $this->getMediaConfig()->getMediaUrl($img['file']);
                    $img['id'] = isset($img['value_id']) ? $img['value_id'] : null;
                    $img['path'] = $this->getMediaConfig()->getMediaPath($img['file']);
                    $images->addItem(new Varien_Object($img));
                }
            }

            $this->setData('media_gallery_images', $images);
        }

        return $this->getData('media_gallery_images');
    }

    /**
     * Return associated products media gallery
     *
     * @return mixed
     */
    private function _getChildrenGallery()
    {
        /** @var Blugento_Swatches_Model_Swatches $swatches */
        $swatches = Mage::getModel('blugento_swatches/swatches');

        $swatches->setProductId($this->getId());

        $gallery = $swatches->getChildrenGallery();

        return $gallery;
    }
}
