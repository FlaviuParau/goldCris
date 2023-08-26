<?php
/**
 * Blugento Feeds
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getGaDownloadOption()
	{
		return Mage::getStoreConfig('blugento_feeds/ga/show_option');
	}

    public function getGaCustomDownloadOption()
    {
        return Mage::getStoreConfig('blugento_feeds/gacustom/show_option');
    }

    public function getGoogleShoppingDownloadOption()
    {
        return Mage::getStoreConfig('blugento_feeds/googleshopping/show_option');
    }

    /**
     * Return the product attributes
     *
     * @return array
     */
    public function getAttributeOptions()
    {
        $options = array();

        $productAttrs = Mage::getResourceModel('catalog/product_attribute_collection');

        $exclude = array('category_ids', 'created_at', 'custom_design', 'custom_design_from', 'custom_design_to',
            'custom_layout_update', 'gift_message_available', 'group_price', 'has_options', 'gallery', 'image',
            'image_label', 'is_recurring', 'links_exist', 'links_purchased_separately', 'links_title', 'media_gallery',
            'minimal_price', 'msrp', 'msrp_display_actual_price_type', 'msrp_enabled', 'old_id', 'options_container',
            'page_layout', 'price_type', 'price_view', 'recurring_profile', 'required_options', 'shipment_type',
            'sku_type', 'small_image', 'small_image_label', 'thumbnail', 'thumbnail_label',
            'tier_price', 'updated_at', 'url_key', 'url_path', 'weight_type'
        );

        $addCust = array('category', 'image', 'small_image', 'thumbnail', 'url_path', 'type_id', 'backorders',
            'manage_stock', 'configurable_sku', 'entity_id', 'attribute_set', 'store_id', 'websites'
        );

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $productAttr */
        foreach ($productAttrs as $productAttr) {
            if (in_array($productAttr->getAttributeCode(), $exclude) || !$productAttr->getFrontendLabel()) {
                continue;
            }
            $value = $productAttr->getAttributeCode();
            $value = isset($mapLabel[$value]) ? $mapLabel[$value] : $value;
            $options[] = array(
                'value' => $value,
                'label' => $productAttr->getFrontendLabel() . " ($value)"
            );
        }

        foreach ($addCust as $value) {
            $value = isset($mapLabel[$value]) ? $mapLabel[$value] : $value;
            $label = ucfirst(str_replace('_', ' ', $value));
            $options[] = array(
                'value' => $value,
                'label' => $label . " ($value)"
            );
        }

    }
}
