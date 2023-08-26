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
 * @package     Blugento_Importer
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Importer_Model_Importer extends Mage_Core_Model_Abstract
{
    protected $_exceptions = array();
    protected $_multiplyData;
    protected $_transformData;
    protected $_functionData;
    protected $_missingCatNames;

    private $_defaultStoreId;
    private $_storeId;
    private $_websiteId;
    private $_attributes;
    private $_alternativValues;

    protected $_prodRequiredAttr = array(
        'store', 'websites', 'type', 'attribute_set', 'sku', 'name', 'description', 'short_description',
        'price', 'tax_class_id', 'visibility', 'status', 'category_ids', 'weight', 'qty', 'is_in_stock'
    );

    protected $_prodDecimalAttr = array(
        'price', 'cost', 'group_price', 'minimal_price', 'msrp', 'tier_price'
    );

    protected $_prodIntAttr = array(
        'status', 'weight', 'qty', 'is_in_stock', 'visibility', 'min_qty', 'is_qty_decimal', 'is_decimal_divided',
        'backorders', 'use_config_backorders', 'min_sale_qty', 'use_config_min_sale_qty', 'max_sale_qty',
        'use_config_max_sale_qty', 'notify_stock_qty', 'use_config_notify_stock_qty', 'manage_stock', 'use_config_manage_stock'
    );

    protected $_mapTable = array(
        'varchar'  => 'catalog_product_entity_varchar',
        'datetime' => 'catalog_product_entity_datetime',
        'int'      => 'catalog_product_entity_int',
        'text'     => 'catalog_product_entity_text',
        'decimal'  => 'catalog_product_entity_decimal',
    );

    protected $_mapQty5 = array('În stoc');

    private $_helper;
    private $_catNameAttrId;
    /**
     * Run the profile import
     *
     * @param int|null $profileId
     * @return $this|void
     */
    public function run($profile)
    {
        $exceptionCollection = array();
        $result = array();

        /** @var Blugento_Importer_Helper_Data $helper */
        $helper = Mage::helper('blugento_importer');

        $this->_helper = $helper;

        $this->_catNameAttrId = $this->_getCategoryNameAttributeId();

        if (!$this->getProfileData() && $this) {
            $this->setProfileData($profile->getData());
        }

        if (!$this->getProfileData()) {
            $e = new Mage_Dataflow_Model_Convert_Exception("Could not find the profile to run");
            $e->setLevel(Mage_Dataflow_Model_Convert_Exception::FATAL);
            $this->addException($e);
            return;
        }

        if (!$this->_defaultStoreId) {
            if ($profile->getStoreId() && $profile->getStoreId() != 9999) {
                $this->_defaultStoreId = $profile->getStoreId();
            } else {
                $this->_defaultStoreId = 0;
            }
        }

        if (!$this->_attributes) {
            $this->_attributes = $this->_getAttributes();
        }

        if (!$this->_alternativValues) {
            $booleanTrue  = explode(',', $profile->getBoleanTrue());
            $booleanFalse = explode(',', $profile->getBoleanFalse());

            $alternativValues = array();
            foreach ($booleanTrue as $trueAlternative) {
                $alternativValues[$trueAlternative] = 1;
            }
            foreach ($booleanFalse as $falseAlternative) {
                $alternativValues[$falseAlternative] = 0;
            }
            $this->_alternativValues = $alternativValues;
        }

        /** @var Blugento_Importer_Helper_Data $helper */
        $helper = Mage::helper('blugento_importer');

        try {
            $profileData = $helper->getProfileFileData($profile, false);

            if ($profileData->getError()) {
                $exception = new Varien_Object();
                $exception->setLevel('ERROR');
                $exception->setMessage($profileData->getError());

                $exceptionCollection[] = $exception;
                $result = new Varien_Object();
                $result->setExceptions($exceptionCollection);

                $this->_saveProfileRunHistory($profile, $result);

                return $result;
            }

            $map = $this->_getCsvHeaders();
            $i = 1;
            $j = 1;
            $updated = 0;
            $created = 0;
            $skippedRows = array();
            foreach ($profileData->getItems() as $itemData) {

                foreach ($itemData->getData() as $attr=>$val) {
                    if (isset($map[$attr]) && $map[$attr] == 'sku') {
                        $sku = $val;
                    }
                }

                if (!isset($sku) || (isset($sku) && !$sku)) {
                    $message = $helper->__('Missing SKU on item %s', $i);
                    $this->addException(
                        $message,
                        Blugento_Importer_Model_Exception::ERROR
                    );
                    $skippedRows[] = $j;
                    $i++;
                    $j++;
                    continue;
                }

                $itemExist = $this->_skuExist($sku);
                $behavior  = $profile->getBehavior();

                if ($itemExist && ($behavior == 'update' || $behavior == 'createupdate')) {
                    $updated++;
                }

                if (!$itemExist && ($behavior == 'create' || $behavior == 'createupdate')) {
                    $created++;
                }

                if ($behavior == 'update' && !$itemExist) {
                    $message = $helper->__('Item with SKU "%s" can not be updated - missing item', $sku);
                    $this->addException(
                        $message,
                        Blugento_Importer_Model_Exception::NOTICE
                    );
                    $skippedRows[] = $j;
                    $i++;
                    $j++;
                    continue;
                }
                if ($behavior == 'create' && $itemExist) {
                    $message = $helper->__('Item with SKU "%s" can not be created - item exist', $sku);
                    $this->addException(
                        $message,
                        Blugento_Importer_Model_Exception::NOTICE
                    );
                    $skippedRows[] = $j;
                    $i++;
                    $j++;
                    continue;
                }

                $importResult = $this->_importProduct($itemData, $profile->getBehavior(), $profile->getProcessimages(), $profile);
                $result[] = $importResult;
                $i++;
                $j++;

                /**
                 * Dispatch an event after the product is imported
                 * Set product_id as event data
                 */
                if ($behavior == 'create' || $behavior == 'createupdate') {
                    Mage::dispatchEvent('blugento_product_import', array('product_id' => $importResult['entity_id']));
                }
            }

            /** Process Remote Images */
            $imagesResult = Mage::getModel('blugento_importer/images')->processRemoteImages($profile->getId(), $profile->getIsDuplicateImages());

            /**
             * reindex all
             */
            for ($i = 1; $i <= 9; $i++) {
                $process = Mage::getModel('index/process')->load($i);
                $process->reindexAll();
            }
             $result['skipped_rows'] = implode(', ', $skippedRows);
             $this->_saveProfileRunHistory($profile, $result, $updated, $created, $imagesResult);
        }
        catch (Exception $e) {
            $message = $e->getMessage();
            $this->addException(
                $message,
                Blugento_Importer_Model_Exception::FATAL
            );
            return false;
        }

        foreach ($this->_exceptions as $message) {
            $exception = new Varien_Object();
            $exception->setLevel('WARNING');
            $exception->setMessage($message);

            $exceptionCollection[] = $exception;
        }
        $result = new Varien_Object();
        $result->setExceptions($exceptionCollection);

        $this->setProfileData(null);

        return $result;
    }

    /**
     * Return the products attribute_id & backend_type.
     *
     * @return array
     */
    private function _getAttributes()
    {
        $excludedAttributes = "
            'category_ids', 'created_at', 'custom_design', 'custom_design_from', 'custom_design_to',
            'custom_layout_update', 'gallery', 'gift_message_available', 'group_price', 'tier_price', 'has_options', 'image', 'image_label',
            'media_gallery', 'options_container', 'page_layout',
            'price_type', 'price_view', 'required_options', 'sku_type', 'small_image', 'small_image_label', 'thumbnail', 'image_hover', 
            'thumbnail_label', 'updated_at', 'weight_type'
        ";

        $tableName = $this->_getTableName('eav_attribute');
        $query = "SELECT attribute_code,attribute_id,backend_type,frontend_input,frontend_label FROM $tableName WHERE entity_type_id=4 AND attribute_code NOT IN($excludedAttributes)";
        $result = $this->_getWriteConnection()->fetchAll($query);

        $attrDetails = array();
        foreach ($result as $attribute) {
            $code       = isset($attribute['attribute_code']) ? $attribute['attribute_code'] : '';
            $id         = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';
            $type       = isset($attribute['backend_type']) ? $attribute['backend_type'] : '';
            $frontInput = isset($attribute['frontend_input']) ? $attribute['frontend_input'] : '';
            $frontLabel = isset($attribute['frontend_label']) ? $attribute['frontend_label'] : '';
            $attrDetails[$code] = array(
                'attribute_id'   => $id,
                'backend_type'   => $type,
                'frontend_input' => $frontInput,
                'frontend_label' => $frontLabel,
            );
        }

        return $attrDetails;
    }

    /**
     * Import product.
     *
     * @param obj $item
     * @param string $behavior
     * @param int $processImages
     */
    private function _importProduct($item, $behavior, $processImages, $profile)
    {
        $missingAttrValues = array();
        $impResponse       = array();

        try {
//            $product = Mage::getModel('catalog/product');
//            $product->setData($item->getData())->save();

            $multiplyData  = $this->_getMultiplyData();
            $transformData = $this->_getTransformData();

            $fieldMap = $this->_getCsvHeaders();

            $mapExclude = array (
                'configurable_attributes', 'simples_skus', 'configurable_pricing', 'related', 'upsells', 'crosssells',
                'group_options', 'bundle_sku', 'bundle_weight', 'bundle_price', 'bundle_price_view', 'bundle_ship',
                'bundle_options', 'store', 'website', 'type', 'attribute_set', 'use_config_min_sale_qty',
                'min_sale_qty', 'is_qty_decimal', 'use_config_qty_increments', 'qty_increments',
                'use_config_enable_qty_inc', 'enable_qty_increments'
            );

            if ($profile->getNewFileStructure()) {
                $mapExclude = array (
                    'configurable_attributes', 'configurable_sku', 'related', 'upsells', 'crosssells',
                    'group_options', 'bundle_sku', 'bundle_weight', 'bundle_price', 'bundle_price_view', 'bundle_ship',
                    'bundle_options', 'store', 'website', 'type', 'attribute_set', 'use_config_min_sale_qty',
                    'min_sale_qty', 'is_qty_decimal', 'use_config_qty_increments', 'qty_increments',
                    'use_config_enable_qty_inc', 'enable_qty_increments'
                );
            }


            // TODO:: refine all this in a single array()
            $customOptionsDropdown = array();
            $customOptionsCheckbox = array();
            $customOptionsRadio    = array();
            $customOptionsMultiple = array();
            $customOptionsField    = array();
            $customOptionsArea     = array();
            $customOptionsDate     = array();
            $customOptionsDateTime = array();
            $customOptionsTime     = array();
            $customOptionsFile     = array();

            $downloadableSample = array();
            $downloadableLinks  = array();

            $hasOptions = 0;
            $data = array();

            foreach ($item->getData() as $attribute=>$val) {
                $attribute = trim($attribute);
                if (!$attribute) {
                    continue;
                }
                // TODO:: refine all this in a single entity
                /*
                 * custom option dropdown
                 */
                if (strpos($attribute, '-drop_down-')) {
                    if (trim($val) != '') {
                        $customOptionsDropdown[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }
                /*
                 * custom option checkbox
                 */
                if (strpos($attribute, '-checkbox-')) {
                    if (trim($val) != '') {
                        $customOptionsCheckbox[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }
                /*
                 * custom option radio
                 */
                if (strpos($attribute, '-radio-')) {
                    if (trim($val) != '') {
                        $customOptionsRadio[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }
                /*
                 * custom option multiple
                 */
                if (strpos($attribute, '-multiple-')) {
                    if (trim($val) != '') {
                        $customOptionsMultiple[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }

                /*
                 * custom option field
                 */
                if (strpos($attribute, '-field-')) {
                    if (trim($val) != '') {
                        $customOptionsField[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }

                /*
                 * custom option area
                 */
                if (strpos($attribute, '-area-')) {
                    if (trim($val) != '') {
                        $customOptionsArea[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }
                /*
                 * custom option date
                 */
                if (strpos($attribute, '-date-')) {
                    if (trim($val) != '') {
                        $customOptionsDate[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }
                /*
                 * custom option date
                 */
                if (strpos($attribute, '-date_time-')) {
                    if (trim($val) != '') {
                        $customOptionsDateTime[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }
                /*
                 * custom option date
                 */
                if (strpos($attribute, '-time-')) {
                    if (trim($val) != '') {
                        $customOptionsTime[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }

                /*
                 * custom option date
                 */
                if (strpos($attribute, '-file-')) {
                    if (trim($val) != '') {
                        $customOptionsFile[$attribute] = $val;
                        $hasOptions = 1;
                    }
                }
                if (strpos($attribute, 'downloadable_sample-') !== false) {
                    $downloadableSample[$attribute] = $val;
                }
                if (strpos($attribute, 'downloadable_links-') !== false) {
                    $downloadableLinks[$attribute] = $val;
                }

                if (!isset($fieldMap[$attribute]) && !in_array($attribute, $mapExclude)) {
                    continue;
                } else if (isset($fieldMap[$attribute])){
                    $attribute = $fieldMap[$attribute];
                } else {
                    $attribute = $attribute;
                }

                if (isset($multiplyData[$attribute]) && $multiplyData[$attribute] !='' && $multiplyData[$attribute] !=0) {
                    $multiplier = (float)$multiplyData[$attribute];
                    $val = $val * $multiplier + $val;
                }

                if (isset($transformData[$attribute]) && $transformData[$attribute] !='') {
                    $transform = $transformData[$attribute];
                    switch($transform) {
                        case 'strotoupper' :
                            $val = strtoupper($val);
                            break;
                        case 'camelcase' :
                            $val = $this->_camelize($val);
                            break;
                    }
                }

                if ($attribute == 'attribute_set') {
                    $val = $this->_getAttributeSetId($val);
                    $data['attribute_set_id'] = $val;
                    continue;
                }
                if ($attribute == 'website') {
                    $val = $this->_getWebsitesId($val);
                    $data['website_id'] = $val;
                    $this->_websiteId = $val;
                    continue;
                }
                if ($attribute == 'store') {
                    $val = $this->_getStoreId($val);

                    if ($val) {
                        $storeId = $val;
                    } else {
                        $storeId = $this->_defaultStoreId;
                    }

                    $data['store_id'] = $storeId;
                    $this->_storeId = $storeId;

                    continue;
                }

                if ($attribute == 'category_ids') {
                    $categorySeparator = $profile->getCategorySeparator() && $profile->getCategorySeparator() !=''
                        ? $profile->getCategorySeparator() : '~';

                    $childCategorySeparator = $profile->getChildCategorySeparator() && $profile->getChildCategorySeparator() !=''
                        ? $profile->getChildCategorySeparator() : '/';

                    $val = $this->_getCategoryIds($val, $categorySeparator, $profile->getProcesscategories(), $childCategorySeparator, $profile->getRootcategory());

                    $data['category_ids'] = $val;

                    continue;
                }

                if ($attribute == 'status' && $val == 0) {
                    $val = 2;
                }

                $data[$attribute] = $val;
            }

            if (isset($data[0])) {
                unset($data[0]);
            }
            $sku = isset($data['sku']) ? $data['sku'] : null;

            /*
             * set default values if missing
             */
            if ($profile->getDefaultValues()) {

                $skipDefaultValues = array_map('trim', explode(',', $profile->getSkipDefaultValues()));

                if((!isset($data['attribute_set_id']) || !$data['attribute_set_id']) && !in_array('attribute_set_id', $skipDefaultValues)) {
                    $defaultAttributeSetId = $profile->getDefaultAttributeSetId();
                    if ($defaultAttributeSetId != 9999) {
                        $val = $this->_getAttributeSetId($defaultAttributeSetId);
                        $data['attribute_set_id'] = $val;
                    }

                }

                if((!isset($data['website']) || !$data['website']) && !in_array('website', $skipDefaultValues)) {
                    $defaultWebsite = $profile->getDefaultWebsite();
                    if ($defaultWebsite != 9999) {
                        $val = $this->_getWebsitesId($defaultWebsite);
                        $data['website'] = $val;
                        $this->_websiteId = $val;
                    }
                }

                if((!isset($data['store_id']) || !$data['store_id']) && !in_array('store_id', $skipDefaultValues)) {
                    $data['store_id'] = $this->_defaultStoreId;
                    $this->_storeId = $this->_defaultStoreId;
                }

                if((!isset($data['type']) || !$data['type']) && !in_array('type', $skipDefaultValues)) {
                    $defaultProductType = $profile->getDefaultProductType();
                    if ($defaultProductType != 9999) {
                        $data['type'] = $defaultProductType;
                    }
                }

                if((!isset($data['weight']) || !$data['weight']) && !in_array('weight', $skipDefaultValues)) {
                    $defaultWeight = $profile->getDefaultWeight();
                    if ($defaultWeight != '') {
                        $data['weight'] = $defaultWeight;
                    }
                }

                if((!isset($data['status']) || !$data['status']) && !in_array('status', $skipDefaultValues)) {
                    $defaultStatus = $profile->getDefaultStatus();
                    if ($defaultStatus != 9999) {
                        $data['status'] = $defaultStatus;
                    }
                }

                if((!isset($data['visibility']) || !$data['visibility']) && !in_array('visibility', $skipDefaultValues)) {
                    $defaultVisibility = $profile->getDefaultVisibility();
                    if ($defaultVisibility != 9999) {
                        $data['visibility'] = $defaultVisibility;
                    }
                }

                if((!isset($data['tax_class_id']) || !$data['tax_class_id']) && !in_array('tax_class_id', $skipDefaultValues)) {
                    $defaultTaxClassId = $profile->getDefaultTaxClassId();
                    if ($defaultTaxClassId != 9999) {
                        $data['tax_class_id'] = $defaultTaxClassId;
                    }
                }

                if((!isset($data['is_in_stock']) || !$data['is_in_stock']) && !in_array('is_in_stock', $skipDefaultValues)) {
                    $defaultIsInStock = $profile->getDefaultIsInStock();
                    if ($defaultIsInStock != 9999) {
                        $data['is_in_stock'] = $defaultIsInStock;
                    }
                }
            }

            $skuExist = $this->_skuExist($sku);
            if ($skuExist && ($behavior == 'update' || $behavior == 'createupdate')) {
                $entityId = $skuExist;
            } else {
                $entityId = $this->_createBaseProduct($data, $hasOptions); // attribute_set_id, type_id, sku, has_options, required_options, created_at
            }

            if (!$entityId) {
                $impResponse['error'] .= 'Missing Entity Id';
            } else {
                $impResponse['entity_id'] = $entityId;
            }

            $this->_setProductWebsite($entityId, $data); // website
            $this->_setProductCategories($entityId, $data, $profile->getRemoveproductsfromcategories()); // category_ids

            $skipAttributes = array('store_id', 'website_id', 'type', 'attribute_set_id', 'sku', 'category_ids',
                'is_in_stock', 'qty', 'image', 'small_image', 'thumbnail', 'image_hover', 'media_gallery');

            $emptyAttrValues = array('msrp', 'special_price');

            foreach ($emptyAttrValues as $emptyAttrValue) {
                if (isset($data[$emptyAttrValue]) && (float)$data[$emptyAttrValue] == '0.00') {
                    unset($data[$emptyAttrValue]);
                }
            }

            foreach ($data as $attribute=>$val) {

                if (in_array($attribute, $skipAttributes)) {
                    continue;
                }

                if ($attribute == 'name' || $attribute == 'url_key') {
                    $table = 'catalog_product_entity_varchar';
                    $attributes = $this->_attributes;
                    $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';

                    if ($attribute == 'url_key') {
                        $productName = isset($data['name']) ? $data['name'] : null;
                        $this->_setProductUrl($productName, $entityId);
                    }

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                    continue;
                }
                if ($attribute == 'country_of_manufacture') {
                    $table = 'catalog_product_entity_varchar';
                    $attributes = $this->_attributes;
                    $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';

                    $countryName = $val;
                    $countries = Mage::app()->getLocale()->getCountryTranslationList();
                    $countryCode = array_search($countryName, $countries);

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $countryCode);
                    continue;
                }
                if ($attribute == 'description') {
                    $table = 'catalog_product_entity_text';
                    $attributes = $this->_attributes;
                    $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                    continue;
                }
                if ($attribute == 'short_description') {
                    $table = 'catalog_product_entity_text';
                    $attributes = $this->_attributes;
                    $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                    continue;
                }
                if ($attribute == 'recurring_profile' && $val != '') {
                    $table = 'catalog_product_entity_text';
                    $attributes = $this->_attributes;
                    $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';

                    $recurringAttributes = array();

                    $val = explode(',', $val);
                    foreach ($val as $v){
                        $element = explode('=', $v);
                        $value = isset($element[1]) ? $element[1] : '';
                        $recurringAttributes[trim($element[0])] = trim($value);
                    }
                    $val = serialize($recurringAttributes);

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                    continue;
                }
                if ($attribute == 'weight') {
                    $table = 'catalog_product_entity_decimal';
                    $attributes = $this->_attributes;
                    $attributeData = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attributeData['attribute_id']) ? $attributeData['attribute_id'] : '';

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                    continue;
                }

                $skipZeroPrices = $profile->getSkipZeroPrices();
                if (($attribute == 'price' || $attribute == 'special_price')) {
                    if ($skipZeroPrices && $val == 0) {
                        continue;
                    } else {
                        $table = 'catalog_product_entity_decimal';
                        $attributes = $this->_attributes;
                        $attributeData = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                        $attributeId = isset($attributeData['attribute_id']) ? $attributeData['attribute_id'] : '';

                        $val = ($attribute == 'price' || $attribute == 'special_price') && $profile->getRemoveComma()
                            ? str_replace(',', '', $val) + $profile->getExtraPrice() + (float)$val * ($profile->getExtraPricePercent() / 100)
                            : $val + $profile->getExtraPrice() + (float)$val * ($profile->getExtraPricePercent() / 100);

                        $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                        continue;
                    }
                }
                if ($attribute == 'group_price') {
                    if ($skipZeroPrices && $val == 0) {
                        continue;
                    } else {
                        $val = $profile->getRemoveComma() ? str_replace(',', '', $val) : $val;
                        $this->_setProductGroupPrice($entityId, $val);
                        continue;
                    }
                }
                if ($attribute == 'tier_price') {
                    if ($skipZeroPrices && $val == 0) {
                        continue;
                    } else {
                        $val = $profile->getRemoveComma() ? str_replace(',', '', $val) : $val;
                        $this->_setProductTierPrice($entityId, $val);
                        continue;
                    }
                }
                $intAttributes = array('status', 'visibility', 'tax_class_id', 'is_recurring', 'blugento_cart_custom',
                    'emkp_visible_to_marketplace', 'payment_restriction'
                );

                if (in_array($attribute, $intAttributes)) {
                    $table = 'catalog_product_entity_int';
                    $attributes = $this->_attributes;
                    $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                    continue;
                }
                if ($attribute == 'special_from_date' || $attribute == 'special_to_date'
                    || $attribute == 'news_from_date' || $attribute == 'news_to_date') {
                    $table = 'catalog_product_entity_datetime';
                    $attributes = $this->_attributes;
                    $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';

                    $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $val);
                    continue;
                }

                /**
                 * import custom attributes.
                 */
                $attrDetails = isset($this->_attributes[$attribute]) ? $this->_attributes[$attribute] : null;
                $attrBackendType = isset($attrDetails['backend_type']) ? $attrDetails['backend_type'] : null;
                $attrFrontInput  = isset($attrDetails['frontend_input']) ? $attrDetails['frontend_input'] : null;
                if (!$attrBackendType) {
                    continue;
                }


                if ($profile->getNewFileStructure()) {
                    if (in_array($attribute, explode(',', $data['configurable_attributes']))) {
                        $arrayVal = explode(':', $val);
                        if (is_array($arrayVal)) {
                            $val = $arrayVal[0];
                        }
                    }
                }

                if ($attrFrontInput == 'multiselect') {
                    $attrBackendType = 'text';
                    $val = $this->_getSelectValueIds($attribute, $val);
                }

                if ($attrBackendType == 'int' && $attrFrontInput == 'select') {
                    $val = $this->_getSelectValueIds($attribute, $val);
                }

                if ($attrBackendType == 'int' && $attrFrontInput == 'boolean') {
                    if (isset($this->_alternativValues[$val])) {
                        $val = $this->_alternativValues[$val] ? $this->_alternativValues[$val] : 0;
                    }
                }

                $mapTable = $this->_mapTable;
                $attrSaveTable = isset($mapTable[$attrBackendType]) ? $mapTable[$attrBackendType] : null;
                if (!$attrSaveTable) {
                    continue;
                }

                if (isset($val)) {
                    $attributes = $this->_attributes;
                    $attributeDet = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
                    $attributeId = isset($attributeDet['attribute_id']) ? $attributeDet['attribute_id'] : '';
                    $this->_setProductData($attrSaveTable, $entityId, $attributeId, $this->_storeId, $val);
                }
            }

            if (!isset($data['url_key'])) {
                $productName = isset($data['name']) ? $data['name'] : null;
                $this->_setProductUrl($productName, $entityId);
            }

            try {
                $this->_setProductStockData($data, $entityId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
            
            if ($processImages && $behavior == 'createupdate') {
                try {
                    $duplicateImages = $profile->getIsDuplicateImages();
                    $gallerySeparator = $profile->getGallerySeparator() && $profile->getGallerySeparator() !=''
                        ? $profile->getGallerySeparator() : '~';
                    $data['profile_id'] = $profile->getId();
                    $this->_setProductImages($data, $entityId, $gallerySeparator, $duplicateImages);
                } catch (Exception $e) {
                    $impResponse['error'] = $e->getMessage();
                }
            } elseif ($processImages && $behavior == 'update') {
                try {
                    $duplicateImages = $profile->getIsDuplicateImages();
                    $gallerySeparator = $profile->getGallerySeparator() && $profile->getGallerySeparator() !=''
                        ? $profile->getGallerySeparator() : '~';
                    $data['profile_id'] = $profile->getId();
                    $this->_setProductImages($data, $entityId, $gallerySeparator, $duplicateImages, true);
                } catch (Exception $e) {
                    $impResponse['error'] = $e->getMessage();
                }
            }

            if (count($missingAttrValues)) {
                $impResponse['product_id'] = $entityId;
                $impResponse['missing_attr_values'] = $missingAttrValues;
            }


            if (isset($data['related']) && $data['related'] !='') {
                $this->_setRelatedProduct($entityId, $data['related'], 'relation');
            }

            if (isset($data['upsells']) && $data['upsells'] !='') {
                $this->_setRelatedProduct($entityId, $data['upsells'], 'up_sell');
            }

            if (isset($data['crosssells']) && $data['crosssells'] !='') {
                $this->_setRelatedProduct($entityId, $data['crosssells'], 'cross_sell');
            }

            if (isset($data['type']) && $data['type'] == 'grouped') {
                $this->_setGroupedProduct($entityId, $data);
            }


            if (!$profile->getNewFileStructure()) {
                if (isset($data['type']) && $data['type'] == 'configurable') {
                    $this->_setConfigurableProduct($entityId, $data);
                }
            }

            if ($profile->getNewFileStructure()) {
                if (isset($data['type']) && $data['type'] == 'configurable') {
                    $this->_cleanLinkSimple($entityId);
                    $productSupperAttributeIds = $this->_cleanSuperAttribute($entityId);
                    if ($productSupperAttributeIds) {
                        $this->_cleanSuperAttributePricing($productSupperAttributeIds);
                    }
                }
            }

//            if ($data['type'] == 'bundle') {
//                $this->_setBundleProduct($entityId, $data); // TODO finish this!
//            }

            // TODO:: refine all this in a single entity
            if (count($customOptionsDropdown)) {
                $type = 'drop_down';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsDropdown as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomDetails($details, $optionId, $entityId);
                }
            }
            if (count($customOptionsCheckbox)) {
                $type = 'checkbox';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsCheckbox as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomDetails($details, $optionId, $entityId);
                }
            }
            if (count($customOptionsRadio)) {
                $type = 'radio';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsRadio as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomDetails($details, $optionId, $entityId);
                }
            }
            if (count($customOptionsMultiple)) {
                $type = 'multiple';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsMultiple as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomDetails($details, $optionId, $entityId);
                }
            }
            if (count($customOptionsField)) {
                $type = 'field';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsField as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomPrice($details, $optionId, $entityId);
                }
            }
            if (count($customOptionsArea)) {
                $type = 'area';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsArea as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomPrice($details, $optionId, $entityId);
                }
            }
            if (count($customOptionsDate)) {
                $type = 'date';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsDate as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomPrice($details, $optionId, $entityId);
                }
            }

            if (count($customOptionsDateTime)) {
                $type = 'date_time';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsDateTime as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomPrice($details, $optionId, $entityId);
                }
            }

            if (count($customOptionsTime)) {
                $type = 'time';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsTime as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomPrice($details, $optionId, $entityId);
                }
            }

            if (count($customOptionsFile)) {
                $type = 'file';
                $this->_clearCustomOption($entityId, $type);
                foreach ($customOptionsFile as $option=>$details) {
                    $optionId = $this->_addCustomOption($option, $entityId, $type, $details);
                    $this->_addCustomPrice($details, $optionId, $entityId);
                }
            }

            if (count($downloadableSample)) {
                $this->_cleanDownloadableSample($entityId);
                $this->_processDownloadableSample($entityId, $downloadableSample);
            }

            if (count($downloadableLinks)) {
                $this->_cleanDownloadableLinks($entityId);
                $this->_processDownloadableLinks($entityId, $downloadableLinks);
            }

            if ($hasOptions) {
                $requiredOpt = 0;
                $tableName = $this->_getTableName('catalog_product_option');
                $query = "SELECT * FROM $tableName WHERE product_id = $entityId AND is_require = 1";
                if (count($this->_getReadConnection()->fetchAll($query)) > 0) {
                    $requiredOpt = 1;
                }

                $tableName = $this->_getTableName('catalog_product_entity');
                $query = "UPDATE $tableName SET has_options=1, required_options = $requiredOpt WHERE entity_id = $entityId";
                $this->_getWriteConnection()->query($query);

                $tableName = $this->_getTableName('eav_attribute');
                $query = "SELECT attribute_id from $tableName WHERE attribute_code='options_container'";
                $attributeId = $this->_getWriteConnection()->fetchOne($query);

                $tableName = $this->_getTableName('catalog_product_entity_varchar');
                $query = "SELECT value_id from $tableName WHERE entity_id=$entityId and attribute_id=$attributeId and store_id=$this->_storeId";
                $valueId = $this->_getWriteConnection()->fetchOne($query);

                if (!$valueId) {
                    $query = "INSERT INTO $tableName 
                      (`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) 
                      VALUES (4,$attributeId,$this->_storeId,$entityId,'container1')";

                    $this->_getWriteConnection()->query($query);
                }
            }

            if ($profile->getNewFileStructure()) {
                foreach ($data as $attribute => $val) {
                    if (isset($data['configurable_sku']) && $data['configurable_sku'] != '') {
                        $this->_setConfigurableProductNew($entityId, $data);
                    }
                }
            }

        } catch (Exception $e) {
            if (isset($impResponse['error']) && $impResponse['error']){
                $impResponse['error'] .= $e->getMessage();
            } else {
                $impResponse['error'] = $e->getMessage();
            }
            Mage::logException($e) ;
        }

        return $impResponse;
    }

    private function _cleanDownloadableSample($entityId)
    {
        $sampleTitleAttrId = $this->_attributes['samples_title']['attribute_id'];

        $query  = "DELETE FROM catalog_product_entity_varchar WHERE entity_type_id=4 AND attribute_id=$sampleTitleAttrId AND entity_id=$entityId;";
        $query .= "DELETE FROM downloadable_sample WHERE product_id = $entityId;";

        $this->_getReadConnection()->query($query);
    }

    /**
     * Process Downloadable Sample.
     *
     * @param int $entityId
     * @param array $downloadableSample
     */
    private function _processDownloadableSample($entityId, $downloadableSample)
    {
        $key = key($downloadableSample);
        $keyDet = explode('-', $key);
        $sampleTitle = isset($keyDet[1]) ? $keyDet[1]: '';

        try {
            $sampleTitleAttrId = isset($this->_attributes['samples_title']['attribute_id'])
                ? $this->_attributes['samples_title']['attribute_id'] : null;

            if (!$sampleTitleAttrId) {
                return null;
            }

            $query = "INSERT INTO catalog_product_entity_varchar (entity_type_id, attribute_id, store_id, entity_id, value) 
                      VALUES (4, $sampleTitleAttrId, $this->_storeId, $entityId, '$sampleTitle')";
            $this->_getWriteConnection()->query($query);

            $rows = explode('~', $downloadableSample[$key]);
            foreach ($rows as $row) {
                $rowDetails = explode(';', $row);
                $rowTitle = isset($rowDetails[0]) ? $rowDetails[0] : null;
                $rowFile  = isset($rowDetails[1]) ? $rowDetails[1] : '(NULL)';
                $rowUrl   = isset($rowDetails[2]) ? $rowDetails[2] : '(NULL)';
                $rowSort  = isset($rowDetails[3]) ? $rowDetails[3] : '(NULL)';

                if (!$rowTitle && (!$rowFile || !$rowUrl)) {
                    continue;
                }

                $sampleType = $rowFile && $rowFile != '(NULL)' ? 'file' : 'url';

                if ($sampleType == 'file' && $rowFile) {
                    $type = 'samples';
                    $rowFile = str_replace('\\', '/', $this->_getCatalogDestination($rowFile, $type));
                }

                $query = "INSERT INTO downloadable_sample (product_id, sample_url, sample_file, sample_type, sort_order) 
                      VALUES ($entityId, '$rowUrl', '$rowFile', '$sampleType', $rowSort)";
                $this->_getWriteConnection()->query($query);

                $sampleId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');

                $query = "INSERT INTO downloadable_sample_title (sample_id, store_id, title) 
                      VALUES ($sampleId, $this->_storeId, '$rowTitle')";
                $this->_getWriteConnection()->query($query);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    private function _cleanDownloadableLinks($entityId)
    {
        $linksTitleAttrId = $this->_attributes['links_title']['attribute_id'];
        $purchasedSeparatelyAttrId = $this->_attributes['links_purchased_separately']['attribute_id'];

        $query  = "DELETE FROM catalog_product_entity_varchar WHERE entity_type_id=4 AND attribute_id=$linksTitleAttrId AND entity_id=$entityId;";
        $query .= "DELETE FROM catalog_product_entity_int WHERE entity_type_id=4 AND attribute_id=$purchasedSeparatelyAttrId AND entity_id=$entityId;";
        $query .= "DELETE FROM downloadable_link WHERE product_id = $entityId;";

        $this->_getReadConnection()->query($query);
    }

    /**
     * Process Downloadable Links.
     *
     * @param int $entityId
     * @param array $downloadableLinks
     */
    private function _processDownloadableLinks($entityId, $downloadableLinks)
    {
        $key = key($downloadableLinks);
        $keyDet = explode('-', $key);
        $sampleTitle = isset($keyDet[1]) ? $keyDet[1]: '';
        $rowLinksPurchasedSeparately = isset($keyDet[2]) ? $keyDet[2]: 0;

        try {
            $linksTitleAttrId = isset($this->_attributes['links_title']['attribute_id'])
                ? $this->_attributes['links_title']['attribute_id'] : null;

            if (!$linksTitleAttrId) {
                return false;
            }
            $query = "INSERT INTO catalog_product_entity_varchar (entity_type_id, attribute_id, store_id, entity_id, value) 
                      VALUES (4, $linksTitleAttrId, $this->_storeId, $entityId, '$sampleTitle')";
            $this->_getWriteConnection()->query($query);

            $rowLinksPurchSeparaAttrId = isset($this->_attributes['links_purchased_separately']['attribute_id'])
                ? $this->_attributes['links_purchased_separately']['attribute_id'] : null;

            $query = "INSERT INTO catalog_product_entity_int (entity_type_id, attribute_id, store_id, entity_id, value) 
                      VALUES (4, $rowLinksPurchSeparaAttrId, $this->_storeId, $entityId, $rowLinksPurchasedSeparately)";
            $this->_getWriteConnection()->query($query);

            $rows = explode('~', $downloadableLinks[$key]);
            foreach ($rows as $row) {
                $rowDetails = explode(';', $row);

                $rowTitle   = isset($rowDetails[0]) ? $rowDetails[0] : null;
                $rowPrice   = isset($rowDetails[1]) ? $rowDetails[1] : '(NULL)';
                $rowMaxDown = isset($rowDetails[2]) ? $rowDetails[2] : '(NULL)';
                $rowIsShareable = isset($rowDetails[3]) ? $rowDetails[3] : 2;
                $rowSampleFile  = isset($rowDetails[4]) ? $rowDetails[4] : '(NULL)';
                $rowSampleUrl   = isset($rowDetails[5]) ? $rowDetails[5] : '(NULL)';
                $rowLinkFile    = isset($rowDetails[6]) ? $rowDetails[6] : '(NULL)';
                $rowLinkUrl     = isset($rowDetails[7]) ? $rowDetails[7] : '(NULL)';
                $rowSort        = isset($rowDetails[8]) ? $rowDetails[8] : '(NULL)';

                if (!$rowTitle) {
                    continue;
                }

                $linkType   = $rowLinkFile && $rowLinkFile !='(NULL)' ? 'file' : 'url';
                $sampleType = $rowSampleFile && $rowSampleFile !='(NULL)' ? 'file' : 'url';

                if ($linkType == 'file' && $rowLinkFile) {
                    $type = 'links';
                    $rowLinkFile = str_replace('\\', '/', $this->_getCatalogDestination($rowLinkFile, $type));
                }
                if ($sampleType == 'file' && $rowSampleFile) {
                    $type = 'link_samples';
                    $rowSampleFile = str_replace('\\', '/', $this->_getCatalogDestination($rowSampleFile, $type));
                }

                $query = "INSERT INTO downloadable_link 
                      (product_id, sort_order, number_of_downloads, is_shareable, link_url, link_file, link_type, sample_url, sample_file, sample_type) 
                      VALUES ($entityId, $rowSort, $rowMaxDown, $rowIsShareable, '$rowLinkUrl', '$rowLinkFile','$linkType', '$rowSampleUrl', '$rowSampleFile', '$sampleType')";
                $this->_getWriteConnection()->query($query);

                $linkId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');

                $query = "INSERT INTO downloadable_link_price (link_id, website_id, price) 
                      VALUES ($linkId, 0, $rowPrice)";
                $this->_getWriteConnection()->query($query);

                $query = "INSERT INTO downloadable_link_title (link_id, store_id, title) 
                      VALUES ($linkId, $this->_storeId, '$rowTitle')";
                $this->_getWriteConnection()->query($query);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Clear product custom options.
     *
     * @param int $entityId
     */
    private function _clearCustomOption($entityId, $type)
    {
        $query = "DELETE FROM catalog_product_option WHERE product_id = $entityId AND type='$type'";

        $this->_getWriteConnection()->query($query);
    }

    /**
     * Add product custom options.
     *
     * @param array $entityId
     * @param int $entityId
     */
    private function _addCustomOption($option, $entityId, $type, $details)
    {
        $sku           = '(NULL)';
        $maxCharacters = '(NULL)';
        $fileExtension = '(NULL)';
        $imageSizeX    = '(NULL)';
        $imageSizeY    = '(NULL)';
        if ($type == 'field' || $type == 'area') {
            $itemDetail = explode(':', $details);
            $sku           = isset($itemDetail[2]) ? $itemDetail[2] : '';
            $maxCharacters = isset($itemDetail[3]) ? $itemDetail[3] : '';
        }
        if ($type == 'date' || $type == 'date_time' || $type == 'time') {
            $itemDetail = explode(':', $details);
            $sku = isset($itemDetail[2]) ? $itemDetail[2] : '';
        }
        if ($type == 'file') {
            $itemDetail = explode(':', $details);
            $sku           = isset($itemDetail[2]) ? $itemDetail[2] : '';
            $fileExtension = isset($itemDetail[3]) ? $itemDetail[3] : '';
            $imageSizeX    = isset($itemDetail[4]) ? $itemDetail[4] : '';
            $imageSizeY    = isset($itemDetail[5]) ? $itemDetail[5] : '';
        }

        $optionDetails = explode('-', $option);

        try {
            $title     = isset($optionDetails[0]) ? $optionDetails[0] : '';
            $type      = isset($optionDetails[1]) ? $optionDetails[1] : $type;
            $required  = isset($optionDetails[2]) ? $optionDetails[2] : 0;
            $sortOrder = isset($optionDetails[3]) ? $optionDetails[3] : '';

            if (!$type) {
                return null;
            }
            $query = "INSERT INTO catalog_product_option (product_id, type, is_require, sku, max_characters, file_extension, image_size_x, image_size_y, sort_order) 
                  VALUES ($entityId, '$type', $required, '$sku', $maxCharacters, '$fileExtension', $imageSizeX, $imageSizeY, $sortOrder)";
            $this->_getWriteConnection()->query($query);

            $optionId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');

            $query = "INSERT INTO catalog_product_option_title (option_id, store_id, title) 
                  VALUES ($optionId, $this->_storeId, '$title')";
            $this->_getWriteConnection()->query($query);

            return $optionId;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    private function _addCustomTextDetails($details)
    {
        $itemDetail = explode(':', $details);

        try {
            $price         = isset($itemDetail[0]) ? $itemDetail[0] : '';
            $priceType     = isset($itemDetail[1]) ? $itemDetail[1] : 'fixed';

            $optionTypeId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');

            $query = "INSERT INTO catalog_product_option_price (option_type_id, store_id, price, price_type) 
              VALUES ($optionTypeId, $this->_storeId, $price, '$priceType')";
            $this->_getWriteConnection()->query($query);


        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    private function _addCustomPrice($details, $optionId)
    {
        $itemDetail = explode(':', $details);

        try {
            $price         = isset($itemDetail[0]) ? $itemDetail[0] : '';
            $priceType     = isset($itemDetail[1]) ? $itemDetail[1] : 'fixed';

            $query = "INSERT INTO catalog_product_option_price (option_id, store_id, price, price_type) 
              VALUES ($optionId, $this->_storeId, $price, '$priceType')";
            $this->_getWriteConnection()->query($query);

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Add product custom options details.
     *
     * @param array $entityId
     * @param int $entityId
     */
    private function _addCustomDetails($details, $optionId, $entityId)
    {
        $itemDetails = explode('|', $details);

        try {
            foreach ($itemDetails as $item) {
                $itemDetail = explode(':', $item);

                $itemTitle = isset($itemDetail[0]) ? $itemDetail[0] : '';
                $price     = isset($itemDetail[1]) ? $itemDetail[1] : 0;
                $priceType = isset($itemDetail[2]) ? $itemDetail[2] : 'fixed';
                $sku       = isset($itemDetail[3]) ? $itemDetail[3] : null;
                $itemSort  = isset($itemDetail[4]) ? $itemDetail[4] : 0;

                if (!$itemTitle) {
                    continue;
                }

                $query = "INSERT INTO catalog_product_option_type_value (option_id, sku, sort_order) 
                  VALUES ($optionId, '$sku', $itemSort)";
                $this->_getWriteConnection()->query($query);

                $optionTypeId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');

                $query = "INSERT INTO catalog_product_option_type_title (option_type_id, store_id, title) 
                  VALUES ($optionTypeId, $this->_storeId, '$itemTitle')";
                $this->_getWriteConnection()->query($query);

                $query = "INSERT INTO catalog_product_option_type_price (option_type_id, store_id, price, price_type) 
                  VALUES ($optionTypeId, $this->_storeId, $price, '$priceType')";
                $this->_getWriteConnection()->query($query);
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Set related product.
     *
     * @param int $entityId
     * @param string $relatedSku
     * @param string $type
     */
    private function _setRelatedProduct($entityId, $relatedSku, $type)
    {
        $query = "SELECT link_type_id FROM catalog_product_link_type WHERE code='$type'";
        $linkType = $this->_getWriteConnection()->fetchOne($query);

        try {
            $this->_clearRelatedProducts($entityId, $linkType);
            
            $related = explode(',', $relatedSku);
            if (count($related)) {
                foreach ($related as $relatedSku) {
                    $relatedId = $this->_skuExist($relatedSku);
                    if ($relatedId) {
                        $query = "INSERT INTO catalog_product_link (product_id, linked_product_id, link_type_id) VALUES ($entityId, $relatedId, $linkType)";
                        $this->_getWriteConnection()->query($query);
                    } else {
	                    Mage::log('Product with the following sku: ' . $relatedSku . ' not found!', null, 'related-error.log');
                        // TODO:: add notice that related product don't exist
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Clear related product.
     *
     * @param int $entityId
     * @param int $linkType
     */
    private function _clearRelatedProducts($entityId, $linkType)
    {
        $query = "DELETE FROM catalog_product_link WHERE product_id=$entityId AND link_type_id=$linkType";
        $this->_getWriteConnection()->query($query);
    }

    /**
     * Return the select product attribute value ids.
     *
     * @param array $attribute
     * @param string $val
     * @return string
     */
    private function _getSelectValueIds($attribute, $val)
    {
        $attributeDet = isset($this->_attributes[$attribute]) ? $this->_attributes[$attribute] : '';
        $attributeId = isset($attributeDet['attribute_id']) ? $attributeDet['attribute_id'] : '';

        if (!$attribute || !trim($val)) {
            return;
        }

        $storeId = $this->_storeId ? $this->_storeId : 0;

        $table = 'eav_attribute_option';
        $query = "SELECT option_id FROM $table WHERE attribute_id=$attributeId";
        $result = $this->_getWriteConnection()->fetchAll($query);

        $optionIds = array();
        foreach ($result as $optionId) {
            $optionIds[] = $optionId['option_id'];
        }
        $optionIds = implode(',', $optionIds);

        $val = trim($val);
        $val = str_replace("'", "`", $val); //$val = addslashes($val);
        $values = str_replace('~', "','", $val);

        $optionIds = $optionIds ? $optionIds : "''";
        $table = 'eav_attribute_option_value';
        $query = "SELECT option_id, value FROM $table WHERE value IN ('$values') AND store_id=$storeId AND option_id IN ($optionIds)";
        $result = $this->_getWriteConnection()->fetchAll($query);

        $newVal = explode('~', $val);

        if (!count($result) || count($result) != count($newVal)) {
            $existingVal = array();
            foreach ($result as $item) {
                $existingVal[] = $item['value'];
            }
            $newValues = array_diff($newVal, $existingVal);
            $newValues = implode('~', $newValues);

            $attr_model = Mage::getModel('catalog/resource_eav_attribute');
            $attr = $attr_model->loadByCode('catalog_product', $attribute);
            $attr_id = $attr->getAttributeId();

            $option['attribute_id'] = $attr_id;
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

            if (count($newVal) > 1 && $newValues != '') {
                $vals = explode('~', $newValues);

                $i = 0;
                foreach ($vals as $v) {
                    $option['value']['any_option_name' . $i][0] = $v;
                    $i++;
                }

                $setup->addAttributeOption($option);
                $sql = "SELECT eaov.option_id FROM eav_attribute_option_value eaov
                        INNER JOIN eav_attribute_option eao
                        ON eaov.option_id = eao.option_id
                        WHERE eaov.value IN ('$values') AND eaov.store_id=$storeId AND eao.attribute_id=$attr_id";

                $result = $this->_getWriteConnection()->fetchAll($sql);
            } else {
                if ($newValues != ''){
                    $option['value']['any_option_name'][0] = $newValues;
                    $setup->addAttributeOption($option);

                    $valueId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');
                    $query = "SELECT option_id FROM $table WHERE value IN ('$values') AND store_id=$storeId AND value_id =$valueId;";

                } else {
                    $query = "SELECT option_id FROM $table WHERE value IN ('$values') AND store_id=$storeId LIMIT 1;";
                }

                $result = $this->_getWriteConnection()->fetchAll($query);
            }
        }

        $optionIds = array();
        foreach ($result as $optionId) {
            $optionIds[] = $optionId['option_id'];
        }
        if (count($optionIds) < 2){
            return $optionIds[0];
        } else {
            return implode(',', $optionIds);
        }
    }

    /**
     * Set the product url_key & url_path
     *
     * @param string $productName
     * @param int $entityId
     */
    private function _setProductUrl($productName, $entityId)
    {
        // TODO:: refine, add all in a single transaction!

        if (!$productName) {
            return;
        }

        $urlKey = $this->_formatUrlKey($productName);
        $productUrlPrefix = Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_SUFFIX);

        $attribute = 'url_key';
        $table = 'catalog_product_entity_varchar';
        $attributes = $this->_attributes;
        $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
        $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';
        $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $urlKey);

        $attribute = 'url_path';
        $table = 'catalog_product_entity_varchar';
        $attributes = $this->_attributes;
        $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
        $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';
        $this->_setProductData($table, $entityId, $attributeId, $this->_storeId, $urlKey . $productUrlPrefix);

        $attribute = 'url_path';
        $table = 'catalog_product_entity_varchar';
        $attributes = $this->_attributes;
        $attribute = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
        $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : '';
        $this->_setProductData($table, $entityId, $attributeId, 1, $urlKey . $productUrlPrefix);
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    private function _formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * Set the product data.
     *
     * @param string $table
     * @param int $entityId
     * @param int $attributeId
     * @param int $storeId
     * @param string $value
     * @return string
     */
    private function _setProductData($table, $entityId, $attributeId, $storeId, $value)
    {
        $dataExist = $this->_dataExist($table, $attributeId, $entityId, $storeId);
        //$value = '\'' . addslashes($value) . '\'';
        $value = '\'' . str_replace("'","\'",$value) . '\'';

        if (!$dataExist) {
            $query = "INSERT INTO $table (entity_type_id, attribute_id, store_id, entity_id, value) VALUES (4, $attributeId, $storeId, $entityId, $value)";
        } else {
            $query = "UPDATE $table SET value = $value WHERE entity_id = $entityId AND attribute_id = $attributeId AND store_id = $storeId";
        }
        $this->_getWriteConnection()->query($query);

        return $query;
    }

    /**
     * Set product group price.
     *
     * @param int $entityId
     * @param decimal $val
     * @return bool
     */
    private function _setProductGroupPrice($entityId, $val)
    {
        $table = $this->_getTableName('catalog_product_entity_group_price');

        $sql = "DELETE FROM $table 
                WHERE entity_id=$entityId";
        $this->_getWriteConnection()->query($sql);

        $query = "SELECT customer_group_id as id, customer_group_code as code FROM customer_group WHERE customer_group_code !=''";
        $result = $this->_getWriteConnection()->fetchAll($query);

        $customerGroupIds = array();
        foreach ($result as $cg) {
            $id  = isset($cg['id']) ? $cg['id'] : null;
            $cgc = isset($cg['code']) ? $cg['code'] : null;

            if (!$id || !$cgc) {
                continue;
            }
            $customerGroupIds[$cgc] = $id;
        }

        $groups = explode('~', $val);
        foreach ($groups as $group) {
            $groupDetails = explode('::', $group);
            $groupName  = isset($groupDetails[0]) ? $groupDetails[0] : null;
            $groupPrice = isset($groupDetails[1]) ? $groupDetails[1] : null;

            if (!$groupName || !$groupPrice) {
                continue;
            }
            $customerGroupId = $customerGroupIds[$groupName];

            $query = "INSERT INTO $table (entity_id, all_groups, customer_group_id, value, website_id, is_percent) 
                      VALUES ($entityId, 0, $customerGroupId, $groupPrice, 0, 0)";
            $this->_getWriteConnection()->query($query);
        }

       return true;
    }

    /**
     * Set product tier price.
     *
     * @param int $entityId
     * @param decimal $val
     * @return bool
     */
    private function _setProductTierPrice($entityId, $val)
    {
        $table = $this->_getTableName('catalog_product_entity_tier_price');

        $sql = "DELETE FROM $table 
                WHERE entity_id=$entityId";
        $this->_getWriteConnection()->query($sql);

        $query = "SELECT customer_group_id as id, customer_group_code as code FROM customer_group WHERE customer_group_code !=''";
        $result = $this->_getWriteConnection()->fetchAll($query);

        $customerGroupIds = array();
        foreach ($result as $cg) {
            $id  = isset($cg['id']) ? $cg['id'] : null;
            $cgc = isset($cg['code']) ? $cg['code'] : null;

            if (!$id || !$cgc) {
                continue;
            }
            $customerGroupIds[$cgc] = $id;
        }

        $rows = explode('~', $val);
        foreach ($rows as $row) {
            $rowDetails = explode('::', $row);
            $groupName  = isset($rowDetails[0]) ? $rowDetails[0] : null;
            $rowQty  = isset($rowDetails[1]) ? $rowDetails[1] : null;
            $rowPrice = isset($rowDetails[2]) ? $rowDetails[2] : null;

            if (!$groupName || !$rowPrice || !$rowQty) {
                continue;
            }
            $customerGroupId = $customerGroupIds[$groupName];

            $query = "INSERT INTO $table (entity_id, all_groups, customer_group_id, qty,  value, website_id) 
                      VALUES ($entityId, 0, $customerGroupId, $rowQty ,$rowPrice, 0)";

            $this->_getWriteConnection()->query($query);
        }

        return true;
    }

    /**
     * Set the product inventory data.
     *
     * @param array $data
     * @param int $entityId
     * @return bool
     */
    private function _setProductStockData($data, $entityId)
    {
        $inventoryAttributes = array(
            'stock_id', 'qty', 'min_qty', 'use_config_min_qty', 'is_qty_decimal', 'backorders', 'use_config_backorders',
            'min_sale_qty', 'use_config_min_sale_qty', 'max_sale_qty', 'use_config_max_sale_qty', 'is_in_stock', 'low_stock_date',
            'notify_stock_qty', 'use_config_notify_stock_qty', 'manage_stock', 'use_config_manage_stock', 'stock_status_changed_auto',
            'use_config_qty_increments', 'qty_increments', 'use_config_enable_qty_inc', 'enable_qty_increments', 'is_decimal_divided'
        );

        $toSet = array();
        foreach ($data as $attribute=>$val) {
            if ($attribute && in_array($attribute, $inventoryAttributes)) {
                if (isset($this->_alternativValues[$val])) {
                    $val = $this->_alternativValues[$val] ? $this->_alternativValues[$val] : 0;
                }

                if ($val == '') {
                    $val = 0;
                }

                $toSet[$attribute] = $val;
            }
        }

        if (isset($toSet['qty'])) {
            foreach ($this->_mapQty5 as $map) {
                if (strpos($toSet['qty'], $map) !== false) {
                    $toSet['qty'] = 5;
                }
            }

            if ($toSet['qty'] > 0 && !isset($toSet['manage_stock'])) {
                $toSet['manage_stock'] = 1;
            }

            if ($toSet['qty'] > 0 && !isset($toSet['is_in_stock'])) {
                $toSet['is_in_stock'] = 1;
            }

            if ($toSet['qty'] <= 0 && !isset($toSet['is_in_stock'])) {
                $toSet['is_in_stock'] = 0;
            }
        }

        if (!count($toSet)) {
            return true;
        }

        $defStockId = $this->_getStockId();
        $atr = array();
        $vax = array();
        $upd = array();
        foreach ($toSet as $attribute=>$val) {
            if (isset($this->_alternativValues[$val])) {
                $val = $this->_alternativValues[$val] ? $this->_alternativValues[$val] : 0;
            }
            $atr[] = $attribute;
            $vax[] = $val;
            $upd[] = $attribute . '=' . $val;
        }
        $atr = 'stock_id,product_id,' . implode(',', $atr);
        $val = "$defStockId,$entityId," . implode(',', $vax);
        $upd = implode(',', $upd);

        $table = 'cataloginventory_stock_item';

        $dataExist = $this->_stockDataExist($table, $entityId);

        if (!$dataExist) {
            $query = "INSERT INTO $table ($atr) VALUES ($val)";
        } else {
            $query = "UPDATE $table SET $upd WHERE product_id = $entityId";
        }
        $this->_getWriteConnection()->query($query);


        $websiteId = isset($data['website_id']) ? $data['website_id'] : 1;
        $table = 'cataloginventory_stock_status';
        $atr = 'product_id,website_id,stock_id,qty,stock_status';
        $qty = isset($toSet['qty']) ? $toSet['qty'] : 0;
        $stockStatus = isset($toSet['is_in_stock']) ? '"' . $toSet['is_in_stock'] . '"' : 0;

        $dataExist = $this->_stockDataExist($table, $entityId);
        if (!$dataExist) {
            $query = "INSERT INTO $table 
                      (product_id,website_id,stock_id,qty,stock_status) 
                      VALUES ($entityId,$websiteId,$defStockId,$qty,$stockStatus)";
        } else {
            $query = "UPDATE $table SET website_id=$websiteId,stock_id=$defStockId,qty=$qty,stock_status=$stockStatus WHERE product_id = $entityId";
        }
        $this->_getWriteConnection()->query($query);

        return true;
    }

    /**
     * Return the product image attribute ids.
     *
     * @param array $type
     * @return array
     */
    private function _getImageAttributeIds($type)
    {
        $codes = implode("','", $type);
        $query = "SELECT attribute_id, attribute_code FROM eav_attribute WHERE attribute_code IN ('$codes') AND entity_type_id=4";

        $result = $this->_getWriteConnection()->fetchAll($query);

        $ids = array();
        foreach ($result as $attribute) {
            if (isset($attribute['attribute_id'])) {
                $attrCode = $attribute['attribute_code'];
                $ids[$attrCode] = $attribute['attribute_id'];
            }
        }

        return $ids;
    }

    /**
     * Set the product images.
     *
     * @param array $data
     * @param int $entityId
     * @param string $gallerySeparator
     * @param int $duplicateImages
     * @param bool $onlyUpdate
     * @return null
     */
    private function _setProductImages($data, $entityId, $gallerySeparator, $duplicateImages, $onlyUpdate = false)
    {
        $profileId = isset($data['profile_id']) ? $data['profile_id'] : null;
        if (!$profileId) {
            return null;
        }

        if (!$onlyUpdate) {
            $galleryAttrId = $this->_getImageAttributeIds(array('media_gallery'));
            $galleryAttrId = isset($galleryAttrId['media_gallery']) ? $galleryAttrId['media_gallery'] : null;

            $imgAttrIds = $this->_getImageAttributeIds(array('image', 'small_image', 'thumbnail', 'image_hover'));

            $this->_cleanMediaDbRecords($entityId, $imgAttrIds, $galleryAttrId);
        } else {

            $singleImgAttr = array('image', 'small_image', 'thumbnail', 'image_hover');

            $importImgAttr = [];
            foreach ($singleImgAttr as $imgAttr) {
                if (isset($data[$imgAttr])) {
                    $importImgAttr[] = $imgAttr;
                }
            }

            $imgAttrIds = null;
            if (count($importImgAttr)) {
                $imgAttrIds = $this->_getImageAttributeIds($importImgAttr);
            }

            $galleryAttrId = $this->_getImageAttributeIds(array('media_gallery'));
            $galleryAttrId = isset($galleryAttrId['media_gallery']) ? $galleryAttrId['media_gallery'] : null;

            $this->_cleanMediaDbRecords($entityId, $imgAttrIds, null);
        }

        $imageAttributes = array('image', 'small_image', 'thumbnail', 'image_hover', 'gallery');
        $toSet = array();
        foreach ($data as $attribute=>$val) {
            if (in_array($attribute, $imageAttributes)) {
                $toSet[$attribute] = $val;
            }
        }

        /**
        *  Relocate the images into Magento format
        */
        $mainImage  = isset($toSet['image']) && $toSet['image'] !='' ? $toSet['image'] : null;
        $smallImage = isset($toSet['small_image']) && $toSet['small_image'] !='' ? $toSet['small_image'] : null;
        $thumbnail  = isset($toSet['thumbnail']) && $toSet['thumbnail'] !='' ? $toSet['thumbnail'] : null;
        $imageHover  = isset($toSet['image_hover']) && $toSet['image_hover'] !='' ? $toSet['image_hover'] : null;
        $missingImg = 0;
        if ($mainImage) {
            if (!$smallImage) {
                $toSet['small_image'] = $mainImage;
                $missingImg++;
            }
            if (!$thumbnail) {
                $toSet['thumbnail'] = $mainImage;
                $missingImg++;
            }
            if (!$imageHover) {
                $toSet['image_hover'] = $mainImage;
                $missingImg++;
            }
        }

        $images  = array();
        $gallery = array();
        $i = 1;

        foreach($toSet as $type=>$imageName) {
            $imgLabel = null;
            if (strpos($imageName, '::') && $type != 'gallery') {
                $imgDetails = explode('::', $imageName);
                $imageNamS  = isset($imgDetails[0]) ? $imgDetails[0] : null;
                $imageName  = $imageNamS ? $imageNamS : $imageName;
                $imgLabel = isset($imgDetails[1]) ? $imgDetails[1] : null;
            }

            if (!$imageName || $imageName == '') {
                continue;
            }

            if (strpos($imageName, 'http') !== false && $type != 'gallery') {
                if ($entityId) {
                    $result = $this->_saveImageToDownload($profileId, $entityId, $imageName, $imgLabel, $type);
                    if (isset($result['error'])) {
                        Mage::log($result['error']);
                    }
                    $imageName = explode('/', $imageName);
                    $imageName = end($imageName);
                    $images[$type] = array(
                        'name' => $this->_getCatalogDestination($imageName, $type, $entityId, $duplicateImages),
                        'label'=> $imgLabel,
                        'sort' => $i
                    );
                    continue;
                }
            } else {
                $images[$type] = array(
                    'name' => $this->_getCatalogDestination($imageName, $type, $entityId, $duplicateImages),
                    'label'=> $imgLabel,
                    'sort' => $i
                );
            }

            /** set gallery images */
            if ($type == 'gallery') {
                if (strpos($imageName, $gallerySeparator)) { // >1 image in gallery
                    $galleryImages = explode($gallerySeparator, $imageName);
                    foreach ($galleryImages as $imageName) {
                        if (strpos($imageName, '::')) { // image label exist
                            $imgDetails = explode('::', $imageName);
                            $imageNamS  = isset($imgDetails[0]) ? $imgDetails[0] : null;
                            $imageName  = $imageNamS ? $imageNamS : $imageName;
                            $imgLabel = isset($imgDetails[1]) ? $imgDetails[1] : null;
                        } else {
                            $imgLabel = '';
                        }
                        if (strpos($imageName, 'http') !== false) {
                            if ($entityId) {
                                $result = $this->_saveImageToDownload($profileId, $entityId, $imageName, $imgLabel, $type);
                                if (isset($result['error'])) {
                                    Mage::log($result['error']);
                                }
                                $imageNameArr = explode('/', $imageName);
                                $imageName = end($imageNameArr);
                                $catDestination = $this->_getCatalogDestination($imageName, $type, $entityId, $duplicateImages);
                                $gallery[$catDestination] = $imgLabel;
                                continue;
                            }
                        } else {
                            $catDestination = $this->_getCatalogDestination($imageName, $type, $entityId, $duplicateImages);
                            $gallery[$catDestination] = $imgLabel;
                        }
                    }
                } else { // 1 image in gallery
                    if (strpos($imageName, '::')) { // image label exist
                        $imgDetails = explode('::', $imageName);
                        $imageNamS  = isset($imgDetails[0]) ? $imgDetails[0] : null;
                        $imageName  = $imageNamS ? $imageNamS : $imageName;
                        $imgLabel = isset($imgDetails[1]) ? $imgDetails[1] : null;
                    } else {
                        $imgLabel = '';
                    }
                    if (strpos($imageName, 'http') !== false) { Mage::log($imageName);
                        if ($entityId) {
                            $result = $this->_saveImageToDownload($profileId, $entityId, $imageName, $imgLabel, $type);
                            if (isset($result['error'])) {
                                Mage::log($result['error']);
                            }
                            $imageName = explode('/', $imageName);
                            $imageName = end($imageName);
                            $catDestination = $this->_getCatalogDestination($imageName, $type, $entityId, $duplicateImages);
                            $gallery[$catDestination] = $imgLabel;
                            continue;
                        }
                    } else {
                        $catDestination = $this->_getCatalogDestination($imageName, $type, $entityId, $duplicateImages);
                        $gallery[$catDestination] = $imgLabel;
                    }
                }
                continue;
            }

            $i++;
        }
        
        if (isset($images['gallery'])) {
            unset($images['gallery']);
        }

        /**
         * Add image, thumbnail, small_image, image_hover
         */
        foreach ($images as $type=>$details) {
            $name  = isset($details['name']) ? $details['name'] : null;
            $label = isset($details['label']) ? $details['label'] : null;
            $sort  = isset($details['sort']) ? $details['sort'] : null;

            if (!$name) {
                continue;
            }

            $imageAttributeId = $imgAttrIds[$type];
            $nameVarchar = str_replace('\\', '/', $name);

            /*
             * INSERT image in catalog_product_entity_varchar
             */
            $sql = "INSERT INTO catalog_product_entity_varchar 
                (entity_type_id,attribute_id,store_id,entity_id,value) 
                VALUES  (4,$imageAttributeId,$this->_storeId,$entityId,'$nameVarchar') ";
            $this->_getWriteConnection()->query($sql);

            /*
             * DELETE AND INSERT image in catalog_product_entity_media_gallery
             */
            $nameGallery = str_replace('\\', '/', $name);
            $sql = "DELETE FROM catalog_product_entity_media_gallery 
                WHERE attribute_id=$galleryAttrId AND entity_id=$entityId AND value = '$nameGallery' ";
            $this->_getWriteConnection()->query($sql);

            $sql = "INSERT INTO catalog_product_entity_media_gallery 
                (attribute_id,entity_id,value) 
                VALUES  ($galleryAttrId,$entityId,'$nameGallery') ";
            $this->_getWriteConnection()->query($sql);

            if ($label || $sort !='') {
                $valueId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');

                $sql = "INSERT INTO catalog_product_entity_media_gallery_value 
                (value_id,store_id,label,position) 
                VALUES  ($valueId,$this->_storeId,'$label',$sort) ";
                $this->_getWriteConnection()->query($sql);
            }
        }

        /**
         * Add media_gallery images
         */
        $sort = $i - $missingImg;
        foreach ($gallery as $imageName=>$imgLabel) {
            $imageName = str_replace('\\', '/', $imageName);

            $sql = "DELETE FROM catalog_product_entity_media_gallery 
                WHERE attribute_id=$galleryAttrId AND entity_id=$entityId AND value = '$imageName' ";
            $this->_getWriteConnection()->query($sql);

            $sql = "INSERT INTO catalog_product_entity_media_gallery
                (attribute_id,entity_id,value)
                VALUES  ($galleryAttrId,$entityId,'$imageName') ";
            $this->_getWriteConnection()->query($sql);

            if ($imgLabel || $sort) {
                $valueId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');
                $sql = "INSERT INTO catalog_product_entity_media_gallery_value 
                (value_id,store_id,label,position) 
                VALUES  ($valueId,$this->_storeId,'$imgLabel',$sort) ";
                $this->_getWriteConnection()->query($sql);
            }
            $sort++;
        }
        // TODO:: refine this method
    }

    /**
     * Save the remote images that need to be processed later.
     *
     * @param int $profileId
     * @param int $entityId
     * @param varchar $imageName
     * @param varchar $imgLabel
     * @param varchar $type
     * @return array
     */
    private function _saveImageToDownload($profileId, $entityId, $imageName, $imgLabel, $type)
    {
        $result = array();

        $tableName = $this->_getTableName('blugento_importer_images');
        try {
            $query = "SELECT id FROM $tableName WHERE entity_id ='$entityId' AND image_path = '$imageName' AND image_type = '$type'";
            $imageExist = $this->_getWriteConnection()->fetchOne($query);

            if(!$imageExist) {
                $sql = "INSERT INTO $tableName
                (profile_id,entity_id,image_path,image_label,image_type,store_id)
                VALUES  ('$profileId','$entityId','$imageName','$imgLabel','$type',$this->_storeId) ";
                $this->_getWriteConnection()->query($sql);

                $result['success'] = true;
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Clean the product db images.
     *
     * @param int $entityId
     * @param array $imgAttrIds
     * @param int $galleryAttrId
     */
    private function _cleanMediaDbRecords($entityId, $imgAttrIds, $galleryAttrId)
    {
        try {
            if ($imgAttrIds) {
                $imgIds = array();
                foreach ($imgAttrIds as $id) {
                    $imgIds[] = $id;
                }
                $imgIds = implode(',', $imgIds);

                /*
                 * DELETE image from catalog_product_entity_varchar
                 */
                $sql = "DELETE FROM catalog_product_entity_varchar 
                WHERE  entity_type_id=4 AND attribute_id IN ($imgIds) AND store_id=$this->_storeId AND entity_id=$entityId";

                $this->_getWriteConnection()->query($sql);
            }

            if ($galleryAttrId) {
                /*
                 * DELETE image from catalog_product_entity_media_gallery
                 */
                $sql = "DELETE FROM catalog_product_entity_media_gallery 
                WHERE attribute_id=$galleryAttrId AND entity_id=$entityId";
                $this->_getWriteConnection()->query($sql);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return the product image catalog path.
     *
     * @param string $image
     * @param null $type
     * @param null|int $entityId
     * @param null|int $duplicateImages
     * @return string
     */
    private function _getCatalogDestination($image, $type=null, $entityId=null, $duplicateImages=null)
    {
        $downloadableType = array('links', 'link_samples');

        $_mediaBase = $type && in_array($type, $downloadableType)? Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'files' . DS . $type . DS
            : Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS;

        $_imagesSrc = Mage::getBaseDir() . DS .'media' . DS . 'import';

        $firstDir  = strtolower(substr($image, 0, 1));
        $secondDir = strtolower(substr($image, 1, 1));
        $catalogDir = strtolower($_mediaBase . $firstDir . DS . $secondDir);

        $imageSrc = $_imagesSrc . DS . $image;

        if ($entityId && $duplicateImages) {
            $image = explode('.', $image);
            $image[0] = $image[0] . '_' . $entityId;
            $image = implode('.', $image);
        }

        $catalogDestination = $catalogDir . DS . $image;

        if (!file_exists($catalogDestination)) {
            if (!file_exists($catalogDir)) {
                mkdir($catalogDir, 0775, true);
            }
            if (file_exists($imageSrc)) {
                copy($imageSrc, $catalogDestination);
            } else {
                return '';
            }
        }

        return DS . $firstDir . DS . $secondDir . DS . $image;
    }

    /**
     * Return the product stock id.
     *
     * @return mixed
     */
    private function _getStockId()
    {
        $query = "SELECT stock_id FROM cataloginventory_stock WHERE stock_name ='Default'";
        return $this->_getWriteConnection()->fetchOne($query);
    }

    /**
     * Check if data exist.
     *
     * @param string $table
     * @param int $attributeId
     * @param int $entityId
     * @param int $storeId
     * @return mixed
     */
    private function _dataExist($table, $attributeId, $entityId, $storeId)
    {
        $query = "SELECT value_id FROM $table WHERE attribute_id = $attributeId AND entity_id = $entityId AND store_id = $storeId";
        return $this->_getWriteConnection()->fetchOne($query);
    }

    /**
     * Check if product stock data exist.
     *
     * @param string $table
     * @param int $entityId
     * @return mixed
     */
    private function _stockDataExist($table, $entityId)
    {
        $query = "SELECT stock_id FROM $table WHERE product_id = $entityId";
        return $this->_getWriteConnection()->fetchOne($query);
    }

    /**
     * SET base | Insert into 'catalog_product_entity' table.
     *
     * @param array $data
     * @return null|int
     */
    private function _createBaseProduct($data, $hasOptions=null)
    {
        $entityId = null;

        $entityTypeId = 4;
        $attributeSetId = isset($data['attribute_set_id']) ? $data['attribute_set_id'] : 4;
        $typeId = strtolower($data['type']);
        $sku = $data['sku'];
        $hasOptions = $data['type'] != 'simple' || $hasOptions == 1 ? 1 : 0;
        $requiredOptions = $data['type'] == 'configurable' ? 1 : 0;
        $createdAt = date('Y-m-d H:m:s');

        $tableName = $this->_getTableName('catalog_product_entity');
        $skuExist = $this->_skuExist($sku);

        if ($sku && !$skuExist) {
            $query = "INSERT INTO $tableName 
                (entity_type_id, attribute_set_id, type_id, sku, has_options, required_options, created_at) 
                VALUES ($entityTypeId, $attributeSetId, '$typeId', '$sku', $hasOptions, $requiredOptions, '$createdAt');";
            try {
                $this->_getWriteConnection()->query($query);
                $entityId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');
            } catch (Exception $e) {
                Mage::logException($e);
            }

            return $entityId;
        } else {
            $query = "UPDATE $tableName 
                SET entity_type_id = $entityTypeId, attribute_set_id = $attributeSetId, type_id = '$typeId', has_options = $hasOptions, required_options = $requiredOptions, created_at = '$createdAt' 
                WHERE sku='$sku';";

            $this->_getWriteConnection()->query($query);
            return $skuExist;
        }
    }

    /**
     * Check if product sku exist.
     *
     * @param string $sku
     * @return mixed
     */
    private function _skuExist($sku)
    {
        $tableName = $this->_getTableName('catalog_product_entity');
        $query = "SELECT entity_id from $tableName WHERE sku = '$sku'";

        return $this->_getWriteConnection()->fetchOne($query);
    }

    /**
     * SET website | Insert into 'catalog_product_website' table.
     *
     * @param int $entityId
     * @param array $data
     */
    private function _setProductWebsite($entityId, $data)
    {
        $tableName = $this->_getTableName('catalog_product_website');

        $websiteId = isset($data['website_id']) ? $data['website_id'] : 1;

        $queryDelete = "DELETE FROM $tableName WHERE product_id=$entityId AND website_id=$websiteId";

        $query = "INSERT INTO $tableName (product_id, website_id) VALUES ($entityId, $websiteId);";
        try {
            $this->_getWriteConnection()->query($queryDelete);
            $this->_getWriteConnection()->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * SET categories | Insert into 'catalog_category_product' table.
     *
     * @param int $entityId
     * @param int $flag
     * @param array $data
     */
    private function _setProductCategories($entityId, $data, $flag)
    {
        $tableName = $this->_getTableName('catalog_category_product');

        $categoryIds = isset($data['category_ids']) ? array_unique($data['category_ids']) : array();

        $position = 1;
        try {
        if (count($categoryIds)) {
            if ($flag){
                $queryDelete = "DELETE FROM $tableName WHERE product_id=$entityId";
                $this->_getWriteConnection()->query($queryDelete);
            }
            foreach ($categoryIds as $categoryId) {
                $query = "REPLACE INTO $tableName (category_id, product_id, position) VALUES ($categoryId, $entityId, $position);";
                    $this->_getWriteConnection()->query($query);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Set group product details.
     *
     * @param int $groupProdId
     * @param array $data
     */
    private function _setGroupedProduct($groupProdId, $data)
    {
        $groupOptions = $data['group_options'];

        $items = explode('~', $groupOptions);

        $query = "SELECT link_type_id FROM catalog_product_link_type WHERE code='super'";
        $linkType = $this->_getWriteConnection()->fetchOne($query);
        $this->_clearRelatedProducts($groupProdId, $linkType);

        foreach ($items as $item) {
            $details = explode('::', $item);
            $sku = isset($details[0]) ? $details[0] : null;
            if (!$sku) {
                continue;
            }
            $attr = isset($details[1]) ? $details[1] : null;
            $attributes = explode(';', $attr);
            $qty = isset($attributes[0]) ? $attributes[0] : 0;
            $pos = isset($attributes[1]) ? $attributes[1] : 0;

            try {
                $relatedId = $this->_skuExist($sku);
                if ($relatedId && $groupProdId && $linkType) {
                    $query = "INSERT INTO catalog_product_link (product_id, linked_product_id, link_type_id) VALUES ($groupProdId, $relatedId, $linkType)";
                    $this->_getWriteConnection()->query($query);
                    $linkId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');
                    if ($qty) {
                        $query = "SELECT product_link_attribute_id FROM catalog_product_link_attribute 
                                  WHERE link_type_id=$linkType AND product_link_attribute_code='qty'";
                        $productLinkAttributeId = $this->_getWriteConnection()->fetchOne($query);

                        $query = "INSERT INTO catalog_product_link_attribute_decimal (product_link_attribute_id, link_id, value) 
                                  VALUES ($productLinkAttributeId, $linkId, $qty)";
                        $this->_getWriteConnection()->query($query);
                    }
                    if ($pos) {
                        $query = "SELECT product_link_attribute_id FROM catalog_product_link_attribute 
                                  WHERE link_type_id=$linkType AND product_link_attribute_code='position'";
                        $productLinkAttributeId = $this->_getWriteConnection()->fetchOne($query);

                        $query = "INSERT INTO catalog_product_link_attribute_int (product_link_attribute_id, link_id, value) 
                                  VALUES ($productLinkAttributeId, $linkId, $pos)";
                        $this->_getWriteConnection()->query($query);
                    }
                } else {
                    // TODO:: add notice that related group product don't exist
                }

            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        try {
            $tableName = $this->_getTableName('eav_attribute');
            $query = "SELECT attribute_id from $tableName WHERE attribute_code='options_container'";
            $attributeId = $this->_getWriteConnection()->fetchOne($query);

            $tableName = $this->_getTableName('catalog_product_entity_varchar');
            $query = "INSERT INTO $tableName 
                  (`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) 
                  VALUES (4,$attributeId,$this->_storeId,$groupProdId,'container1')";

            $this->_getWriteConnection()->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Set bundle product details.
     *
     * @param int $bundleProdId
     * @param array $data
     */
    private function _setBundleProduct($bundleProdId, $data)
    {
        $bundleOptions = $data['bundle_options'];
        $options = explode('||', $bundleOptions);
        foreach ($options as $option) {
            $sections = explode('::', $bundleOptions);
            $details =  isset($sections[0]) ? $sections[0] : null;
            $details = explode(';', $details);

            $title    = isset($details[0]) ? $details[0] : null;
            $type     = isset($details[1]) ? $details[1] : null;
            $required = isset($details[2]) ? $details[2] : null;
            $position = isset($details[3]) ? $details[3] : null;

            $items   =  isset($sections[1]) ? $sections[1] : null;
            $items = explode(';', $items);

            foreach ($items as $item) {
                $itemDetails = explode(',', $item);
                $sku = isset($itemDetails[0]) ? $itemDetails[0] : null;
                $qty = isset($itemDetails[1]) ? $itemDetails[1] : null;
                $udq = isset($itemDetails[2]) ? $itemDetails[2] : null; // user_def_qty
                $pos = isset($itemDetails[3]) ? $itemDetails[3] : null; // position
                $def = isset($itemDetails[4]) ? $itemDetails[4] : null; // default

                echo "<pre>"; var_dump($title, $type, $required, $position, $sku, $qty, $udq, $pos, $def); die();
            }
        }
    }

    /**
     * Set the configurable simple products.
     *
     * @param int $configProdId
     * @param array $data
     * @return array
     */
    private function _setConfigurableProduct($configProdId, $data)
    {
        $configurableAttributes = isset($data['configurable_attributes']) ? explode(',', $data['configurable_attributes']) : null;
        $simplesSkus            = isset($data['simples_skus']) ? explode(',', $data['simples_skus']) : null;
        $configurablePricing    = isset($data['configurable_pricing']) ? $data['configurable_pricing'] : null;

        $priceItems = explode('~', $configurablePricing);

        $configPrices = array();
        foreach ($priceItems as $item) {
            $det = explode('::', $item);
            $attribute = isset($det[0]) ? $det[0] : null;
            $values    = isset($det[1]) ? $det[1] : null;
            $valPrice = explode(';', $values);

            $options = array();
            foreach ($valPrice as $priceDet) {
                $attrD = explode(':', $priceDet);
                $option = isset($attrD[0]) ? $attrD[0] : null;
                $price  = isset($attrD[1]) ? $attrD[1] : null;

                $isPercent = 0;
                if (strpos($price, '%')) {
                    $isPercent = 1;
                    $price = substr($price,  0, -1);
                }

                $ss = $this->_getSelectValueIds($attribute, $option);
                $options[$ss] = array(
                    'option'      => $option,
                    'price'       => $price,
                    'is_percent'  => $isPercent,
                );
            }

            $attributes = $this->_attributes;
            $attributeDetails = isset($attributes[$attribute]) ? $attributes[$attribute] : null;
            $attributeId = isset($attributeDetails['attribute_id']) ? $attributeDetails['attribute_id'] : null;

            if ($attributeId) {
                $configPrices[$attributeId] = $options;
            }
        }

        /**
         * clean data first
         */
        $this->_cleanLinkSimple($configProdId);
        $productSupperAttributeIds = $this->_cleanSuperAttribute($configProdId);
        if ($productSupperAttributeIds) {
            $this->_cleanSuperAttributePricing($productSupperAttributeIds);
        }

        $validProducts = array();
        if (count($simplesSkus)) {
            foreach ($simplesSkus as $simpleSku) {
                $productId = $this->_skuExist($simpleSku);

                if ($productId) {
                    $validProducts[] = $productId;
                } else {
                    // TODO:: add notice of invalid attribute
                }
            }
        }

        $validAttributeIds = array();
        foreach ($configurableAttributes as $attribute) {
            $attributes = $this->_attributes;
            $attributeDetails = isset($attributes[$attribute]) ? $attributes[$attribute] : null;

            if (!$attributeDetails) {
                // TODO:: add notice of invalid attribute
                continue;
            }

            $attributeId = isset($attributeDetails['attribute_id']) ? $attributeDetails['attribute_id'] : null;
            $attrBackendType = isset($attributeDetails['backend_type']) ? $attributeDetails['backend_type'] : null;
            $attrFrontInput = isset($attributeDetails['frontend_input']) ? $attributeDetails['frontend_input'] : null;
            $attrFrontLabel = isset($attributeDetails['frontend_label']) ? $attributeDetails['frontend_label'] : null;

            /**
             * check if attribute is valid
             */
            if ($attributeId && $attrBackendType == 'int' && $attrFrontInput == 'select') {
                $validAttributeIds[] = $attributeId;
            } else {
                // TODO:: add notice of invalid attribute
            }
        }

        $supperAttributeIds = array();
        foreach ($validProducts as $simpleProdId) {

            $this->_linkSimple($simpleProdId, $configProdId);

            $attributeIds = array();
            foreach ($configurableAttributes as $attribute) {
                $attributes = $this->_attributes;
                $attributeDetails = isset($attributes[$attribute]) ? $attributes[$attribute] : null;

                if (!$attributeDetails) {
                    // TODO:: add notice of invalid attribute
                    continue;
                }

                $attributeId = isset($attributeDetails['attribute_id']) ? $attributeDetails['attribute_id'] : null;
                $attrBackendType = isset($attributeDetails['backend_type']) ? $attributeDetails['backend_type'] : null;
                $attrFrontInput = isset($attributeDetails['frontend_input']) ? $attributeDetails['frontend_input'] : null;
                $attrFrontLabel = isset($attributeDetails['frontend_label']) ? $attributeDetails['frontend_label'] : null;

                /**
                 * check if attribute is valid
                 */
                if ($attributeId && $attrBackendType == 'int' && $attrFrontInput == 'select') {
                    $validAttributeIds[] = $attributeId;
                } else {
                    // TODO:: add notice of invalid attribute
                }

                $tableName = $this->_getTableName('catalog_product_entity_int');
                $query = "SELECT value from $tableName WHERE entity_type_id=4 AND entity_id=$simpleProdId AND attribute_id=$attributeId";

                $productSelectedValue = $this->_getWriteConnection()->fetchOne($query);

                $attributeIds[$attributeId] = $productSelectedValue;
            }
            $supperAttributeIds[] = $attributeIds;
        }

        $superFlag = false;
        foreach ($supperAttributeIds as $idf) {
            foreach ($idf as $attributeId=>$productSelectedValue) {
                if ($productSelectedValue) {
                    $productSuperAttributeId = $this->_addSuperAttribute($configProdId, $attributeId);
                    if ($productSuperAttributeId && !$superFlag) {
                        $this->_addSuperAttributeLabel($productSuperAttributeId, $attributeId, $attrFrontLabel);
                        $this->_addSuperAttributePricing($productSuperAttributeId, $attributeId, $productSelectedValue, $configPrices);
                        $superFlag = true;
                    } else if ($productSuperAttributeId) {
                        $this->_addSuperAttributePricing($productSuperAttributeId, $attributeId, $productSelectedValue, $configPrices);
                    }
                }
            }
        }

        $tableName = $this->_getTableName('eav_attribute');
        $query = "SELECT attribute_id from $tableName WHERE attribute_code='options_container'";
        $attributeId = $this->_getWriteConnection()->fetchOne($query);

        $tableName = $this->_getTableName('catalog_product_entity_varchar');
        $query = "INSERT INTO $tableName 
                  (`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) 
                  VALUES (4,$attributeId,$this->_storeId,$configProdId,'container1')";

        $this->_getWriteConnection()->query($query);
    }

    /**
     * Set the configurable simple products.
     *
     * @param int $configProdId
     * @param array $data
     * @return array
     */
    private function _setConfigurableProductNew($prodId, $data)
    {
        $configurableAttributes = isset($data['configurable_attributes']) ? explode(',', $data['configurable_attributes']) : null;
        $sconfigSku            = isset($data['configurable_sku']) ?  $data['configurable_sku'] : null;

        $configPrices = array();

        foreach ($configurableAttributes as $configurableAttribute){

            $options = array();
            $attrD = explode(':', $data[$configurableAttribute]);

            if (array($attrD)){
                $option = isset($attrD[0]) ? $attrD[0] : null;
                $price  = isset($attrD[1]) ? $attrD[1] : null;

                $isPercent = 0;
                if (strpos($price, '%')) {
                    $isPercent = 1;
                    $price = substr($price,  0, -1);
                }
            } else {
                $option = $data[$configurableAttribute];
                $price = 0;
                $isPercent = 0;
            }

            $ss = $this->_getSelectValueIds($configurableAttribute, $option);

            $options[$ss] = array(
                'option'      => $option,
                'price'       => $price,
                'is_percent'  => $isPercent,
            );

            $attributes = $this->_attributes;
            $attributeDetails = isset($attributes[$configurableAttribute]) ? $attributes[$configurableAttribute] : null;
            $attributeId = isset($attributeDetails['attribute_id']) ? $attributeDetails['attribute_id'] : null;

            if ($attributeId) {
                $configPrices[$attributeId] = $options;
            }
        }

        $validProducts = array();
        if ($sconfigSku) {
            $productId = $this->_skuExist($sconfigSku);

            if ($productId) {
                $validProducts[] = $productId;
            } else {
                // TODO:: add notice of invalid attribute
            }
        }

        $validAttributeIds = array();
        foreach ($configurableAttributes as $attribute) {
            $attributes = $this->_attributes;
            $attributeDetails = isset($attributes[$attribute]) ? $attributes[$attribute] : null;

            if (!$attributeDetails) {
                // TODO:: add notice of invalid attribute
                continue;
            }

            $attributeId = isset($attributeDetails['attribute_id']) ? $attributeDetails['attribute_id'] : null;
            $attrBackendType = isset($attributeDetails['backend_type']) ? $attributeDetails['backend_type'] : null;
            $attrFrontInput = isset($attributeDetails['frontend_input']) ? $attributeDetails['frontend_input'] : null;
            $attrFrontLabel = isset($attributeDetails['frontend_label']) ? $attributeDetails['frontend_label'] : null;

            /**
             * check if attribute is valid
             */
            if ($attributeId && $attrBackendType == 'int' && $attrFrontInput == 'select') {
                $validAttributeIds[] = $attributeId;
            } else {
                // TODO:: add notice of invalid attribute
            }
        }

        $supperAttributeIds = array();
        foreach ($validProducts as $simpleProdId) {

            $this->_linkSimple($prodId, $simpleProdId);

            $attributeIds = array();
            foreach ($configurableAttributes as $attribute) {
                $attributes = $this->_attributes;
                $attributeDetails = isset($attributes[$attribute]) ? $attributes[$attribute] : null;

                if (!$attributeDetails) {
                    // TODO:: add notice of invalid attribute
                    continue;
                }

                $attributeId = isset($attributeDetails['attribute_id']) ? $attributeDetails['attribute_id'] : null;
                $attrBackendType = isset($attributeDetails['backend_type']) ? $attributeDetails['backend_type'] : null;
                $attrFrontInput = isset($attributeDetails['frontend_input']) ? $attributeDetails['frontend_input'] : null;
                $attrFrontLabel = isset($attributeDetails['frontend_label']) ? $attributeDetails['frontend_label'] : null;

                /**
                 * check if attribute is valid
                 */
                if ($attributeId && $attrBackendType == 'int' && $attrFrontInput == 'select') {
                    $validAttributeIds[] = $attributeId;
                } else {
                    // TODO:: add notice of invalid attribute
                }

                $tableName = $this->_getTableName('catalog_product_entity_int');
                $query = "SELECT value from $tableName WHERE entity_type_id=4 AND entity_id=$prodId AND attribute_id=$attributeId";

                $productSelectedValue = $this->_getWriteConnection()->fetchOne($query);

                $attributeIds[$attributeId] = $productSelectedValue;
            }
            $supperAttributeIds[] = $attributeIds;
        }

        $superFlag = false;
        foreach ($supperAttributeIds as $idf) {
            foreach ($idf as $attributeId=>$productSelectedValue) {
                if ($productSelectedValue) {
                    $prodId = $this->_skuExist($sconfigSku);
                    $productSuperAttributeId = $this->_addSuperAttribute($prodId, $attributeId);
                    if ($productSuperAttributeId && !$superFlag) {
                        $this->_addSuperAttributeLabel($productSuperAttributeId, $attributeId, $attrFrontLabel);
                        $this->_addSuperAttributePricing($productSuperAttributeId, $attributeId, $productSelectedValue, $configPrices);
                        $superFlag = true;
                    } else if ($productSuperAttributeId) {
                        $this->_addSuperAttributePricing($productSuperAttributeId, $attributeId, $productSelectedValue, $configPrices);
                    }
                }
            }
        }

        $tableName = $this->_getTableName('eav_attribute');
        $query = "SELECT attribute_id from $tableName WHERE attribute_code='options_container'";
        $attributeId = $this->_getWriteConnection()->fetchOne($query);

        $tableName = $this->_getTableName('catalog_product_entity_varchar');
        $prodId = $this->_skuExist($sconfigSku);
        $query = "REPLACE INTO $tableName 
                  (`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) 
                  VALUES (4,$attributeId,$this->_storeId,$prodId,'container1')";

        $this->_getWriteConnection()->query($query);
    }

    /**
     * Clear prices.
     *
     * @param string $productSupperAttributeIds
     */
    private function _cleanSuperAttributePricing($productSupperAttributeIds)
    {
        if ($productSupperAttributeIds) {
            $query = "DELETE FROM catalog_product_super_attribute_pricing WHERE product_super_attribute_id IN($productSupperAttributeIds)";
            $this->_getWriteConnection()->query($query);
        }
    }

    /**
     * Add prices.
     *
     * @param int $productSuperAttributeId
     * @param int $attributeId
     * @param int $productSelectedValue
     * @param array $configPrices
     * @return null
     */
    private function _addSuperAttributePricing($productSuperAttributeId, $attributeId, $productSelectedValue, $configPrices)
    {
        $details = $configPrices[$attributeId];
        $attrDet = $details[$productSelectedValue];
        
        $priceValue = $attrDet['price'] ? $attrDet['price'] : 0;
        $isPercent = $attrDet['is_percent'] ? $attrDet['is_percent'] : 0;

        $query = "REPLACE INTO catalog_product_super_attribute_pricing 
                  (`product_super_attribute_id`,`value_index`,`is_percent`,`pricing_value`,`website_id`) 
                  VALUES ($productSuperAttributeId,$productSelectedValue,$isPercent,$priceValue,0)";

        try {
            $this->_getWriteConnection()->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return null;
    }

    /**
     * Clean super attribute.
     *
     * @param int $configProdId
     * @return string
     */
    private function _cleanSuperAttribute($configProdId)
    {
        $sql = "SELECT product_super_attribute_id as 'id' FROM catalog_product_super_attribute WHERE product_id = $configProdId";

        $result = $this->_getReadConnection()->fetchAll($sql);

        $ids = array();
        if ($result) {
            foreach ($result as $id) {
                $ids[] = isset($id['id']) ? $id['id'] : '';
            }
        }
        if (count($ids)) {
            $result = implode(',', $ids);
        }


        $query = "DELETE FROM catalog_product_super_attribute WHERE product_id=$configProdId";
        $this->_getWriteConnection()->query($query);

        return $result;
    }

    /**
     * Add super attribute.
     *
     * @param $configProdId
     * @param $attributeId
     * @return null
     */
    private function _addSuperAttribute($configProdId, $attributeId)
    {
        $sql = "SELECT product_super_attribute_id as 'id' FROM catalog_product_super_attribute WHERE product_id = $configProdId AND attribute_id=$attributeId";

        $productSuperAttributeId =  $this->_getReadConnection()->fetchOne($sql);
        if ($productSuperAttributeId) {
            return $productSuperAttributeId;
        }

        $query = "INSERT INTO catalog_product_super_attribute (`product_id`,`attribute_id`) VALUES ($configProdId,$attributeId)";

        try {
            $this->_getWriteConnection()->query($query);
            return $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return null;
    }

    /**
     * Add super attribute label.
     *
     * @param int $productSuperAttributeId
     * @param int $attributeId
     * @param string $attrFrontLabel
     * @return null
     */
    private function _addSuperAttributeLabel($productSuperAttributeId, $attributeId, $attrFrontLabel)
    {
        $query = "REPLACE INTO catalog_product_super_attribute_label (`product_super_attribute_id`,`store_id`,`use_default`,`value`) 
                  VALUES ($productSuperAttributeId,$this->_storeId,1,'$attrFrontLabel')";

        try {
            $this->_getWriteConnection()->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return null;
    }

    /**
     * Clean link simple.
     *
     * @param int $configProdId
     */
    private function _cleanLinkSimple($configProdId)
    {
        $query = "DELETE cpsl.*,cpsr.* FROM catalog_product_super_link as cpsl
				JOIN catalog_product_relation as cpsr ON cpsr.parent_id=cpsl.parent_id
				WHERE cpsl.parent_id=$configProdId";
        $this->_getWriteConnection()->query($query);
    }

    /**
     * Add link simple.
     *
     * @param int $simpleProdId
     * @param int $configProdId
     */
    private function _linkSimple($simpleProdId, $configProdId)
    {
        $query1 = "REPLACE INTO catalog_product_super_link (`parent_id`,`product_id`) VALUES ($configProdId,$simpleProdId) ";
        $query2 = "REPLACE INTO catalog_product_relation (`parent_id`,`child_id`) VALUES ($configProdId,$simpleProdId) ";
        try {
            $this->_getWriteConnection()->query($query1);
            $this->_getWriteConnection()->query($query2);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return the table name.
     *
     * @param string $tableName
     * @return mixed
     */
    private function _getTableName($tableName)
    {
        $resource = Mage::getSingleton('core/resource');
        return $resource->getTableName($tableName);
    }

    /**
     * Return the attribute set id.
     *
     * @param string $attributeSetName
     * @return mixed
     */
    private function _getAttributeSetId($attributeSetName)
    {
        $sql = "SELECT attribute_set_id FROM eav_attribute_set WHERE entity_type_id = 4 AND attribute_set_name = '$attributeSetName'";

        return $this->_getReadConnection()->fetchOne($sql);
    }

    /**
     * Return the website id.
     *
     * @param string $val
     * @return mixed
     */
    private function _getWebsitesId($val)
    {
        if (is_numeric($val)) {
            return $val;
        }

        $sql = "SELECT website_id FROM core_website WHERE code = '$val'";

        return $this->_getReadConnection()->fetchOne($sql);
    }

    /**
     * Return the store id.
     *
     * @param string $val
     * @return mixed
     */
    private function _getStoreId($val)
    {
        $val = (int)$val;
        if (is_numeric($val)) {
            return $val;
        }

        $sql = "SELECT store_id FROM core_store WHERE code = '$val'";

        return $this->_getReadConnection()->fetchOne($sql);
    }

    /**
     * Return the category ids.
     *
     * @param string $val
     * @return array|null
     */
    private function _getCategoryIds($val, $categorySeparator, $processCategories, $childCategorySeparator, $rootCategory)
    {
        if ($val) {
            $val = trim($val);
            $allCategories = array();
            if (strpos($val, $categorySeparator)) {
                $allCategories = explode($categorySeparator, $val);
                //unset empty values
                $allCategories = array_filter($allCategories, 'strlen');
                //trim subcategories
                $trimmedSubCategories = [];
                foreach ($allCategories as $category) {
                    $trimmedSubCategories[] = trim($category);
                }
                $val = implode($categorySeparator, $trimmedSubCategories);

                $allCategories = explode($categorySeparator, $val);
            } else {
                $allCategories[] = $val;
            }

            if (!count($allCategories)) {
                return null;
            }

            $categories = array();
            $catIds = array();
            $missingCategories = array();

           $rootCategory = isset($rootCategory) ? $rootCategory : 'Default Category';

            foreach ($allCategories as $category) {

                if (substr($category, -1) == $childCategorySeparator) {
                    $category = substr($category, 0, -1);
                }

                $category = $rootCategory . $childCategorySeparator . $category;
                if (strpos($category, $childCategorySeparator)) {
                    $names = explode($childCategorySeparator, $category);
                    $ic = 0;
                    foreach ($names as $catName) {
                        $paren = isset($names[$ic - 1]) ? $names[$ic - 1] : null;
                        if ($ic > 0) {
                            $categories[] = array(
                                'cat_name' => trim($catName),
                                'parent_name' => $paren,
                                'level' => $ic + 1,
                            );
                        } else {
                            $categories[] = array(
                                'cat_name' => trim($catName),
                                'parent_name' => null,
                                'level' => $ic + 1,
                            );
                        }
                        $ic++;
                    }
                }

                $parentPath = '';
                foreach ($categories as $category) {
                    $catName = isset($category['cat_name']) ? $category['cat_name'] : '';
                    $parentName = isset($category['parent_name']) ? $category['parent_name'] : '';
                    $catLevel = isset($category['level']) ? $category['level'] : '';

                    if ($catLevel == 1) {
                        $catLevel = 1;
                        $catPath = '1/';
                        $catId = $this->_getCategoryId($catName, $catLevel, $catPath);
                        if ($catId) {
                            $catIds[] = $catId;
                            $parentPath = '1/' . $catId;
                        } else if ($processCategories) {
                            $parentId = 1;
                            $catId = $this->_createCategory($parentId, $catName);
                            $catIds[] = $catId;
                            $parentPath = '1/' . $catId;
                        } else {
                            $missingCategories[] = $catName;
                            continue;
                        }
                        continue;
                    }
                    $catName = str_replace('&', '&amp;', $catName);
                    $categoryId = $this->_getCategoryId($catName, $catLevel, $parentPath);

                    if ($categoryId) {
                        $catId = $categoryId;
                        $catIds[] = $catId;
                        $parentPath = $parentPath . '/' . $categoryId;
                    } else {
                        if (!$processCategories) {
                            $missingCategories[] = $catName;
                            continue;
                        }
                        if ($parentName) { // child category
                            $parentLevel = $catLevel - 1;
                            $parentId = $this->_getCategoryId($parentName, $parentLevel, $parentPath);

                            if ($parentId) {
                                $catId = $this->_createCategory($parentId, $catName);
                                $catIds[] = $catId;
                                $parentPath = $parentPath . '/' . $catId;
                            }
                        } else { // root category
                            $parentId = Mage::app()->getDefaultStoreView()->getRootCategoryId();
                            $categoryId = $this->_getCategoryId($catName, $parentId, '1/2');
                            if (!$categoryId) {
                                $categoryId = $this->_createCategory($parentId, $catName, $parentPath);
                            }

                            $catIds[] = $categoryId;
                            $parentPath = $parentPath . '/' . $categoryId;
                        }
                    }
                }
            }
            //        if (count($missingCategories)) {
            //            $message = $this->_helper->__('Missing categories: %s', implode(', ', array_unique($missingCategories)));
            //            $this->addException($message);
            //        }
        } else {
            $catIds = array();
        }
        return $catIds;
    }

    /**
     * Return the category id.
     *
     * @param string $catName
     * @param int $catLevel
     * @return null
     */
    private function _getCategoryId($catName, $catLevel, $parentPath)
    {
        $catNameAttrId = $this->_catNameAttrId;
        $catName = str_replace('"', "'", $catName);
        $sql = '
            SELECT ccev.entity_id AS cat_id FROM catalog_category_entity_varchar AS ccev
            LEFT JOIN catalog_category_entity AS cce ON ccev.entity_id = cce.entity_id
            WHERE ccev.attribute_id =' . $catNameAttrId . ' AND ccev.value ="' . $catName . '" AND cce.level=' . $catLevel .' AND cce.path LIKE "' . $parentPath . '%"';

        $result = $this->_getReadConnection()->fetchRow($sql);

        return isset($result['cat_id']) ? $result['cat_id'] : null;
    }

    /**
     * Creates new category.
     *
     * @param int    $parentId
     * @param string $name
     * @param int    $storeId
     * @return Mage_Catalog_Model_Category
     */
    protected function _createCategory($parentId, $catName)
    {
        $storeId = $this->_storeId;

        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        $data = $this->_prepareCategoryData($catName);
        $parent = Mage::getModel('catalog/category')->load($parentId);
        $category->setData($data)
            ->setAttributeSetId($category->getDefaultAttributeSetId())
            ->setStoreId($storeId)
            ->setPath(implode('/', $parent->getPathIds()))
            ->setParentId($parentId)
            ->save();
        return $category->getId();
    }
    /**
     * Return the category name attribute id.
     *
     * @return null
     */
    private function _getCategoryNameAttributeId()
    {
        try {
            $sql = "SELECT attribute_id FROM eav_attribute WHERE entity_type_id = 3 AND attribute_code='name'";

            return $this->_getReadConnection()->fetchOne($sql);
        } catch(Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Prepare default data of new category.
     *
     * @param string $catName
     * @return array
     */
    protected function _prepareCategoryData($catName)
    {
        return array(
            'name'              => trim($catName),
            'is_active'         => 1,
            'include_in_menu'   => 1,
            'is_anchor'         => 1,
            'url_key'           => '',
            'description'       => '',
        );
    }

    /**
     * Return the read connection
     *
     * @return mixed
     */
    private function _getReadConnection()
    {
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_read');
    }

    /**
     * Return the write connection
     *
     * @return mixed
     */
    private function _getWriteConnection()
    {
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_write');
    }

    /**
     * Run the test profile
     *
     * @param Blugento_Importer_Model_Profile $profile
     * @return $this
     */
    public function test($profile)
    {
        // Sample Data
        $sampleData = new Varien_Data_Collection();

        $sampleD = '<tr>';

        $csvHeaders = $this->_getCsvHeaders();
        /** @var Blugento_Importer_Helper_Data $helper */
        $helper = Mage::helper('blugento_importer');

        $limit = Blugento_Importer_Helper_Data::DATA_TEST_LIMIT;
        $profileData = $helper->getProfileFileData($profile, false, $limit);

        $exceptions = $this->_validateFileData($profile, $profileData);



        foreach ($profileData->getColumns() as $colName) {
            if ($colName == '0' || !isset($csvHeaders[$colName])) {
                continue;
            }
            $colName = $csvHeaders[$colName];
            $sampleD .= '<th style="padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #4CAF50;color: white;border: 1px solid #ddd;padding: 8px;">' . $colName . '</th>';
        }
        $sampleD .= '</tr>';

        $multiplyData  = $this->_getMultiplyData();
        $transformData = $this->_getTransformData();
        $functionExec  = $this->_getFunctionData();

        foreach ($profileData->getItems() as $itemData) {
            $sampleD .= '</tr>';
            foreach ($itemData->getData() as $attribute=>$value) {

                if (!isset($csvHeaders[$attribute])) {
                    continue;
                }

                if ($attribute == 'store' && $value == '') {
                    $value = $profile->getStoreId();
                }

                if (isset($multiplyData[$attribute]) && $multiplyData[$attribute] !='' && $multiplyData[$attribute] !=0) {
                    $multiplier = (float)$multiplyData[$attribute];
                    $value = $value * $multiplier + $value;
                }

                 if (isset($transformData[$attribute]) && $transformData[$attribute] !='') {
                     $transform = $transformData[$attribute];
                     switch($transform) {
                         case 'strotoupper' :
                             $value = strtoupper($value);
                             break;
                         case 'camelcase' :
                             $value = $this->_camelize($value);
                             break;
                     }
                }
                $sampleD .= '<td style="border: 1px solid #ddd;padding: 8px;">' . $value . '</td>';
            }
            $sampleD .= '</tr>';
        }

        $sample = new Varien_Object();
        $sample->setMessage($sampleD);
        $sampleData->addItem($sample);

        $this->setSampleData($sampleData);
        $this->setExceptionsx($exceptions);

        return $this;
    }

    /**
     * Return the profile transform data
     *
     * @return array|mixed
     */
    private function _getTransformData()
    {
        if ($this->_transformData) {
            return $this->_transformData;
        }
        $profileData = $this->getProfileData();

        $dbProductMapAttributes = isset($profileData['map_attributes_data']['product']['file'])
            ?$profileData['map_attributes_data']['product']['file'] : array();
        $fileProductMapTransform = isset($profileData['map_attributes_data']['product']['transform'])
            ?$profileData['map_attributes_data']['product']['transform'] : array();

        $mapAttributes = array();
        foreach ($dbProductMapAttributes as $key=>$valFile) {
            $valDb = $fileProductMapTransform[$key];
            $mapAttributes[$valFile] = $valDb;
        }

        $this->_transformData = $mapAttributes;

        return $this->_transformData;
    }

    /**
     * Return the profile multiplier data
     *
     * @return array|mixed
     */
    private function _getMultiplyData()
    {
        if ($this->_multiplyData) {
            return $this->_multiplyData;
        }

        $profileData = $this->getProfileData();

        $dbProductMapAttributes = isset($profileData['map_attributes_data']['product']['db'])
            ?$profileData['map_attributes_data']['product']['db'] : array();
        $fileProductMapMultiplier = isset($profileData['map_attributes_data']['product']['multiplier'])
            ?$profileData['map_attributes_data']['product']['multiplier'] : array();

        $mapAttributes = array();
        foreach ($dbProductMapAttributes as $key=>$valFile) {
            $valDb = $fileProductMapMultiplier[$key];
            $mapAttributes[$valFile] = $valDb;
        }

        $this->_multiplyData = $mapAttributes;

        return $this->_multiplyData;
    }

    /**
     * Return the profile multiplier data
     *
     * @return array|mixed
     */
    private function _getFunctionData()
    {
        if ($this->_functionData) {
            return $this->_functionData;
        }

        $profileData = $this->getProfileData();

        $dbProductMapAttributes = isset($profileData['map_attributes_data']['product']['db'])
            ?$profileData['map_attributes_data']['product']['db'] : array();
        $fileProductMapMultiplier = isset($profileData['map_attributes_data']['product']['function'])
            ?$profileData['map_attributes_data']['product']['function'] : array();

        $mapAttributes = array();
        foreach ($dbProductMapAttributes as $key=>$valFile) {
            $valDb = isset($fileProductMapMultiplier[$key]) ? $fileProductMapMultiplier[$key] : '';
            $mapAttributes[$valFile] = $valDb;
        }

        $this->_functionData = $mapAttributes;

        return $this->_multiplyData;
    }

    /**
     * Validate the profile data
     *
     * @param Varien_Object $fileData
     */
    private function _validateFileData($profile, $fileData)
    {
        /** @var Blugento_Importer_Helper_Data $helper */
        $helper = Mage::helper('blugento_importer');

        $behavior = $profile->getBehavior(); //update
        $defStore = $profile->getStoreId();

        $exceptions = new Varien_Data_Collection();

        $columns = $fileData->getColumns();
        $items   = $fileData->getItems();
        $csvHeaders = $this->_getCsvHeaders();

        $diff = array_diff($this->_prodRequiredAttr, $csvHeaders);

        if (count($diff) && ($behavior == 'create' || $behavior == 'createupdate')) {
            $missingAttributes = implode(', ', $diff);

            $exception = new Varien_Object();
            $exception->setMessage('Missing required Magento product attributes: '. $missingAttributes);
            $exception->setLevel(Mage_Dataflow_Model_Convert_Exception::FATAL);
            $exception->setPosition('general');
            $exceptions->addItem($exception);
        }

        $itemsNew    = 0;
        $itemsUpdate = 0;

        foreach ($items as $key=>$item) {

            $id = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
            if ($id) {
                $itemsUpdate++;
            } else {
                if ($item->getSku()) {
                    $itemsNew++;
                }
            }

            if ($behavior == 'update' && $item->getSku()) {
                if (!$id) {
                    $exception = new Varien_Object();
                    $exception->setMessage($helper->__('Can not update product. Sku "%s" do not exist.', $item->getSku()));
                    $exception->setLevel(Mage_Dataflow_Model_Convert_Exception::FATAL);
                    $exception->setPosition('round: '. ++$key);

                    $exceptions->addItem($exception);
                }
            }

            if ($behavior == 'create') {
                if ($id) {
                    $exception = new Varien_Object();
                    $exception->setMessage($helper->__('Can not create product. Sku "%s" already exist.', $item->getSku()));
                    $exception->setLevel(Mage_Dataflow_Model_Convert_Exception::FATAL);
                    $exception->setPosition('round: '. ++$key);

                    $exceptions->addItem($exception);
                }
            }

            foreach ($item->getData() as $attribute=>$value) {

                if (!isset($csvHeaders[$attribute]) || $attribute=='store') {
                    continue;
                }

                if (in_array($attribute, $this->_prodRequiredAttr) && ((!isset($value) || $value == '') && $value != 0)) {
                    $exception = new Varien_Object();
                    $exception->setMessage($helper->__('No %s specified', $attribute));
                    $exception->setLevel(Mage_Dataflow_Model_Convert_Exception::FATAL);
                    $exception->setPosition('round: '. ++$key);

                    $exceptions->addItem($exception);
                }

                if (!in_array($attribute, $this->_prodRequiredAttr) && (!isset($value) || $value == '')) {
                    $exception = new Varien_Object();
                    $exception->setMessage($helper->__('No %s specified', $csvHeaders[$attribute]));
                    $exception->setLevel(Mage_Dataflow_Model_Convert_Exception::WARNING);
                    $exception->setPosition('round: '. ++$key);

                    $exceptions->addItem($exception);
                }
            }
        }

        $message = $helper->__('Total Items pocessed: %s | NEW Items: %s | UPDATE Items: %s', count($items), $itemsNew, $itemsUpdate);
        $level = (count($items) == $itemsNew + $itemsUpdate) ? Mage_Dataflow_Model_Convert_Exception::NOTICE : Mage_Dataflow_Model_Convert_Exception::WARNING;
        if ($behavior == 'update') {
            $message = $helper->__('Total Items pocessed: %s | UPDATE Items: %s', count($items), $itemsUpdate);
            $level = (count($items) == $itemsUpdate) ? Mage_Dataflow_Model_Convert_Exception::NOTICE : Mage_Dataflow_Model_Convert_Exception::WARNING;
        }
        if ($behavior == 'create') {
            $message = $helper->__('Total Items pocessed: %s | NEW Items: %s', count($items), $itemsNew);
            $level = (count($items) == $itemsNew) ? Mage_Dataflow_Model_Convert_Exception::NOTICE : Mage_Dataflow_Model_Convert_Exception::WARNING;
        }
        $exception = new Varien_Object();
        $exception->setMessage($message);
        $exception->setLevel($level);
        $exception->setPosition();
        $exceptions->addItem($exception);

        $this->setExceptions($exceptions);

        return $exceptions;
    }

    /**
     * Return the CSV headers
     *
     * @return array
     */
    private function _getCsvHeaders()
    {
        $profileData = $this->getProfileData();
        if (!is_array($profileData['map_attributes_data'])) {
            $profileData['map_attributes_data'] = unserialize($profileData['map_attributes_data']);
        }
        $dbProductMapAttributes = isset($profileData['map_attributes_data']['product']['db'])
            ?$profileData['map_attributes_data']['product']['db'] : array();
        $fileProductMapAttributes = isset($profileData['map_attributes_data']['product']['file'])
            ?$profileData['map_attributes_data']['product']['file'] : array();

        $mapAttributes = array();
        $mapAttributes['store'] = 'store';
        foreach ($fileProductMapAttributes as $key=>$valFile) {
            $valDb = $dbProductMapAttributes[$key];
            $mapAttributes[trim($valFile)] = $valDb;
        }

        return $mapAttributes;
    }

    /**
     * Return operation result messages
     *
     * @param bool $validationResult
     * @return array
     */
    public function getOperationResultMessages($validationResult)
    {
        $messages = array();
        if ($this->getProcessedRowsCount()) {
            if (!$validationResult) {
                if ($this->getProcessedRowsCount() == $this->getInvalidRowsCount()) {
                    $messages[] = Mage::helper('importexport')->__('File is totally invalid. Please fix errors and re-upload file');
                } elseif ($this->getErrorsCount() >= $this->getErrorsLimit()) {
                    $messages[] = Mage::helper('importexport')->__('Errors limit (%d) reached. Please fix errors and re-upload file', $this->getErrorsLimit());
                } else {
                    if ($this->isImportAllowed()) {
                        $messages[] = Mage::helper('importexport')->__('Please fix errors and re-upload file');
                    } else {
                        $messages[] = Mage::helper('importexport')->__('File is partially valid, but import is not possible');
                    }
                }
                // errors info
                foreach ($this->getErrors() as $errorCode => $rows) {
                    $error = $errorCode . ' '
                        . Mage::helper('importexport')->__('in rows') . ': '
                        . implode(', ', $rows);
                    $messages[] = $error;
                }
            } else {
                if ($this->isImportAllowed()) {
                    $messages[] = Mage::helper('importexport')->__('Validation finished successfully');
                } else {
                    $messages[] = Mage::helper('importexport')->__('File is valid, but import is not possible');
                }
            }
            $notices = $this->getNotices();
            if (is_array($notices)) {
                $messages = array_merge($messages, $notices);
            }
            $messages[] = Mage::helper('importexport')->__('Checked rows: %d, checked entities: %d, invalid rows: %d, total errors: %d', $this->getProcessedRowsCount(), $this->getProcessedEntitiesCount(), $this->getInvalidRowsCount(), $this->getErrorsCount());
        } else {
            $messages[] = Mage::helper('importexport')->__('File does not contain data.');
        }
        return $messages;
    }

    /**
     * Invalidate indexes by process codes.
     *
     * @return Mage_ImportExport_Model_Import
     */
    public function invalidateIndex()
    {
        if (!isset(self::$_entityInvalidatedIndexes[$this->getEntity()])) {
            return $this;
        }

        $indexers = self::$_entityInvalidatedIndexes[$this->getEntity()];
        foreach ($indexers as $indexer) {
            $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode($indexer);
            if ($indexProcess) {
                $indexProcess->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            }
        }

        return $this;
    }

    /**
     *  Save the profile result
     *
     * @param Blugento_Importer_Model_Profile $profile
     * @param string $result
     * @return bool
     */
    private function _saveProfileRunHistory($profile, $result, $updated = NULL, $created = NULL, $imageResult = NULL)
    {
//        return true; // TODO:: finish this.

        $logMessage = '';
        foreach ($this->_exceptions as $message) {
            $logMessage = $message . ' | ' . $logMessage;
        }

        if (is_array($logMessage)) {
            foreach ($logMessage as $message) {
            }
        }

        if (is_object($result) && $result->getExceptions()) {
            foreach ($result->getExceptions() as $exception) {
                $message = isset($exception['message']) ? $exception['message'] : null;
                if ($message) {
                    $logMessage .=  ' ' . $message;
                }
            }
            $this->setExceptions();
        }

        try {
            Mage::getModel('blugento_importer/history')
                ->setProfileId($profile->getId())
                ->setProfileName($profile->getName())
                ->setEntityType($profile->getEntityType())
                ->setResult($logMessage)
                ->setImported($created)
                ->setUpdated($updated)
                ->setMissingImages( isset($imageResult['success']) ? $imageResult['success'] : $imageResult['error'])
                ->setSkippedRows($result['skipped_rows'])
                ->save();
        } catch (Exception $e) {
            Mage::throwException($e);
        }

        return true;
    }

    /**
     * @param str $e
     * @return $this
     */
    public function addException($e)
    {
        $this->_exceptions[] = $e;
        return $this;
    }

    public function getExceptions()
    {
        return $this->_exceptions;
    }

    /**
     * Run profile by cron.
     *
     * @return null
     */
    public function cronRunProfile()
    {
        $profiles = Mage::getModel('blugento_importer/profile')->getCollection();

        try {
            foreach ($profiles as $profile) {
                $cronRunFrequency = $profile->getCronRunFrequency();
                $lastRunTime      = $profile->getLastRunTime();

                if ($this->_isValidToRun($lastRunTime, $cronRunFrequency)) {
                    $this->run($profile);

                    $profile->setLastRunTime(date('Y-m-d H:i:s'));
                    $profile->save();
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    private function _sendMail($email, $profileName)
    {
        // This is the name that you gave to the template in System -> Transactional Emails
        $emailTemplateId = Mage::getModel('core/email_template')->loadByCode('Importer Profile Finished')->getTemplateId();

        // I'm using the Store Name as sender name here.
        $sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
        // I'm using the general store contact here as the sender email.
        $sender['email'] = Mage::getStoreConfig('trans_email/ident_general/email');

        // These variables can be used in the template file by doing {{ var some_custom_variable }}
        $emailTemplateVariables = array(
            'profile_name' => $profileName,
            'admin_name' =>  $sender['name']
        );


        $storeId = Mage::app()->getStore()->getId();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(true);
        $transactionalEmail = Mage::getModel('core/email_template')
            ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

        $transactionalEmail->sendTransactional($emailTemplateId, $sender, $email,  $sender['name'], $emailTemplateVariables, $storeId);
    }

    public function cronRunOnClickProfile()
    {
        try {
            $profiles = Mage::getModel('blugento_importer/profile')->getCollection()->addFieldToFilter('run_flag', 1);
            Mage::getConfig()->saveConfig('blugento_urlcheck/general/enabled', '0', 'default', 0)->reinit();
            foreach ($profiles as $profile) {
                $this->run($profile);
                $profile->setRunFlag(0);
                $profile->save();
                //send mail
                $this->_sendMail($profile->getEmail(), $profile->getName());
            }
            Mage::getConfig()->saveConfig('blugento_urlcheck/general/enabled', '1', 'default', 0)->reinit();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Check if profile is valid to run
     *
     * @param int|null $type
     * @return bool
     */
    private function _isValidToRun($lastRunTime, $cronRunFrequency)
    {
        if (!$cronRunFrequency || $cronRunFrequency == 0) {
            return false;
        }

        $currentTime  = time();
        $validToRunAt = strtotime($lastRunTime)  + 60*60*$cronRunFrequency;

        if (!$lastRunTime) {
            return true;
        }

        if ($currentTime >= $validToRunAt) {
            return true;
        }

        return false;
    }
}
