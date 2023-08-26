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
 * @package     Blugento_FullFeed
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FullFeed_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CATEGORY_TYPE_ALL   = 1;
    const CATEGORY_TYPE_MAIN  = 2;
    const CATEGORY_TYPE_FINAL = 3;
    const CATEGORY_TYPE_INDIVIDUAL = 4;
    /**
     * Check if feed is valid to run
     *
     * @param int|null $type
     * @return bool
     */
    public function isValidToRun($type=null, $storeId=null)
    {
        $type = $type ? $type : 1;

        if (!Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/enabled', $storeId)) {
            return null;
        }

        $lastRunTime  = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/last_run_time', $storeId);
        $cacheTime    = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/cache_time', $storeId);

        if ($cacheTime == 10000) {
            return true;
        }

        $currentTime  = strtotime(now());
        $validToRunAt = strtotime($lastRunTime)  + 60*60*$cacheTime;

        if (!$lastRunTime) {
            return true;
        }

        if ($currentTime >= $validToRunAt) {
            return true;
        }

        return false;
    }

    /**
     * Return the feed file type from config
     *
     * @param int|null $type
     * @return mixed
     */
    public function getFeedFileType($type=null, $storeId=null)
    {
        $type = $type ? $type : 1;

        return Mage::getStoreConfig( 'blugento_fullfeed/feed_' . $type . '/file_type', $storeId);
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

        $exclude = array('category_ids', 'created_at', 'custom_design', 'custom_design_from', 'custom_design_to' ,
            'custom_layout_update', 'gift_message_available', 'group_price', 'has_options', 'gallery', 'image',
            'image_label', 'is_recurring', 'links_exist', 'links_purchased_separately', 'links_title', 'media_gallery',
            'minimal_price', 'msrp_display_actual_price_type', 'msrp_enabled', 'old_id', 'options_container',
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

        return $options;
    }

    /**
     * Convert xml object to array
     *
     * @param mixed $xmlObject
     * @param array $out
     * @return array
     */
    public function xml2array($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node) {
            $out[$index] = (is_object($node) || is_array($node)) ? $this->xml2array($node) : $node;
        }

        return $out;
    }

    /**
     * Replace diacritics
     *
     * @param string $text
     * @return string
     */
    public function replaceDiacritics($text)
    {
        $search = array ('ă', 'Ă', 'â', 'Â', 'î', 'Î', 'ș', 'Ș', 'ț', 'Ț');
        $replace = array ('a', 'A', 'a', 'A', 'i', 'I', 's', 'S', 't', 'T');

        for ($i = 0; $i < count($search); $i++) {
            $text = str_replace($search[$i], $replace[$i], $text);
        }

        return $text;
    }

    /**
     * Convert array to xml
     *
     * @param array $data
     * @param string $xml_data
     * @return string
     */
    public function array2xml($data, &$xml_data ) {
        foreach($data as $key => $value ) {
            if( is_numeric($key) ){
                $key = 'product_'.$key; //dealing with <0/>..<n/> issues
            }
            if( is_array($value) ) {
                $subnode = $xml_data->addChild($key);
                $this->array2xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
}
