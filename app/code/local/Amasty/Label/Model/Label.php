<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */
class Amasty_Label_Model_Label extends Mage_Core_Model_Abstract
{
    /**
     * Label cache tag
     */
    const CACHE_TAG      = 'amasty_label';
    protected $_cacheTag = 'amasty_label';
    protected $_info     = array();

    /**
     * Price separator
     * @var null|string
     */
    protected $separator = null;

    /**
     * Current store
     * @var null|Mage_Core_Model_Store
     */
    protected $store = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('amlabel/label');
    }
    
    public function init($p, $mode=null, $parent = null)
    {
        $this->setProduct($p);

        // auto detect product page
        if ($mode) {
            $this->setMode($mode == 'category' ? 'cat' : 'prod');
        } else {
            $this->setMode('cat');
            if (Mage::registry('current_product')) {
                $this->setMode('prod');
            }   
        }
        
        $regularPrice = $p->getPrice();

        if ($this->getIsSale()
            && $this->getSpecialPriceOnly()) {
            $specialPrice = $p->getData('special_price');

            if ($this->isNotValidSpecialPrice($p)) {
                $specialPrice = 0;
            }
        } else {
            $specialPrice = $p->getFinalPrice();

            if ($p->getTypeId() == 'bundle') {
                /*
                 * Mege::register() & unregister()  are a bit dirty hacks to prevent observer triggering
                 *  when $product->getPrices() called
                 */
                list($specialPrice, $maxPrice) = $p->getPriceModel()->getPrices($p);
                $regularPrice = $specialPrice;

                $price = $p->getData('special_price');

                if ($price !== null && $price < 100) {
                    $regularPrice = ($specialPrice / $price) * 100;
                }
            }
        }

        if ($parent && ($parent->getTypeId() == 'grouped')) {
            $usedProds = Mage::helper('amlabel')->getUsedProducts($parent);
            foreach ($usedProds as $child) {
                if ($child != $p) {
                    $regularPrice += $child->getPrice();
                    $specialPrice += $child->getFinalPrice();
                }
            }
        }

        if ($p->getTypeId() == 'grouped' && !$regularPrice) {
            $regularPrice = $p->getMinimalPrice();
        }

        $this->_info['price']         = $regularPrice;
        $this->_info['special_price'] = $specialPrice;

        $this->_info['created_at'] = strtotime($p->getCreatedAt());
    }

    public function isApplicable()
    {
        $p = $this->getProduct();
        if (!$p || (!$this->getImageUrl() && !$this->getText())) {
            return false;
        }

        $now = Mage::getModel('core/date')->date();
        $breakFromDate = $this->getFromDate() ? $now < $this->getFromDate() : false;
        $breakToDate = $this->getToDate() ? $now > $this->getToDate() : false;
        if ($this->getDateRangeEnabled() && ($breakFromDate || $breakToDate)) {
            return false;
        }

        // individual products logic
        $inArray = in_array($p->getSku(), explode(',', $this->getIncludeSku()));
        if (!$p->getSku()) {
            $inArray = false;
        }

        // include skus
        if (0 == $this->getIncludeType() && $inArray) {
            return true;
        }
        // exclude skus
        if (1 == $this->getIncludeType() && $inArray) {
            return false;
        }
        // use for skus only
        if (2 == $this->getIncludeType()) {
            return $inArray;
        }

        // price ranges are enabled
        if (!$this->_checkPriceRange($p)) {
            return false;
        }

        // condition be attribute are enabled
        if (!$this->_checkUseAttribute($p)) {
            return false;
        }

        $catIds = $this->getCategory();
        $catIds = explode(',', $catIds);        
        if (!empty($catIds) && !in_array('', $catIds)) {
            $ids = $p->getCategoryIds();
            if (!is_array($ids)) {
                return false;
            }

            $found = false;
            foreach ($catIds as $catId) {
                if (in_array($catId, $ids)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return false;
            }
        }

        $stockRangeEnabled = $this->getProductStockEnabled();
        if ($stockRangeEnabled == "1") {
            //TODO check for all types of products
            $qty = $this->_getProductQty($p);
            $lessThan = $this->getStockLess();
            if ($lessThan >= 0 && $lessThan <= $qty) {
                return false;
            }
        }

        $stockStatus = $this->getStockStatus();
        if ($stockStatus && $stockItem = $p->getStockItem()) {
            $inStock = $stockItem->getIsInStock() ? 2 : 1;
            if ($inStock != $stockStatus) {
                return false;
            }
        }

        if ($this->getIsNew()) {
            $isNew = $this->_isNew($p) ? 2 : 1;
            if ($this->getIsNew() != $isNew) {
                return false;
            }
        }

        if ($this->getIsSale()) {
            $isSale = $this->_isSale() ? 2 : 1;
            if ($this->getIsSale() != $isSale) {
                return false;
            }
        }

        // and if all conditions are not false ...
        return true;
    }

    protected function _checkUseAttribute($p)
    {
        $attrCode = $this->getAttrCode();
        if ($attrCode) {
            // has attribute condition
            if (!array_key_exists($attrCode, $p->getData())
                && (('custom_stock_status' === $attrCode) // the custom_stock_status can be not set in case with custom_stock_status_qty_based
                    && !array_key_exists('custom_stock_status_qty_based', $p->getData()))) {
                return false;
            }
            // compatibility with the `Amasty: Custom Stock Status` extension
            // if the `Use Quantity Ranges Based Stock Status` property setted to `Yes`
            // so the value of the `Custom Stock Status` is dynamic
            // and setted to the product value is not used
            if ('custom_stock_status' === $attrCode
                && Mage::helper('core')->isModuleEnabled('Amasty_Stockstatus')
                && $this->getAttrValue() != Mage::helper('amstockstatus')->getCustomStockStatusId($p)
            ) {
               return false;
            } elseif ($this->getAttrValue()) {
                if ($this->getAttrMulti()) { // multiselect
                    $attrValues = explode(',', $this->getAttrValue());
                    $count = count($attrValues);
                    $prodValues = explode(',', $p->getData($attrCode));
                    $check = array_diff($attrValues, $prodValues);
                    if ($this->getAttrRule()) { // all selected
                        if (!empty($check)) {
                            return false;
                        }
                    } else { // one of selected
                        if ($count == count($check)) {
                            return false;
                        }
                    }
                } else {
                    $v = $p->getData($attrCode);
                    if (preg_match('/^[0-9,]+$/', $v)) {
                        if (!in_array($this->getAttrValue(), explode(',', $v))) {
                            return false;
                        }
                    } elseif ($v != $this->getAttrValue()) {
                        return false;
                    }
                }
            } elseif (!$p->getData($attrCode)) { // sometimes needed for has attribute condition too
                return false;
            }
        }

        return true;
    }

    protected function isNotValidSpecialPrice($product)
    {
        $now = Mage::getModel('core/date')->date('Y-m-d 00:00:00');

        return ($product->getSpecialFromDate() && $now < $product->getSpecialFromDate())
            || ($product->getSpecialToDate() && $now > $product->getSpecialToDate());
    }

    protected function _checkPriceRange($p)
    {
        if ($this->getPriceRangeEnabled()) {
            switch ($this->getByPrice()) {
                case '1': // Special Price
                    if ($this->isNotValidSpecialPrice($p)) {
                         $price = 0;
                    } else {
                        $price = $p->getSpecialPrice();
                    }
                    break;
                case '2': // Final Price
                    $price = Mage::helper('tax')->getPrice($p, $p->getFinalPrice());
                    break;
                case '3': // Final Price Incl Tax
                    $price = Mage::helper('tax')->getPrice($p, $p->getFinalPrice(), true);
                    break;
                case '4': // Starting from Price
                    if ($p->isGrouped()) {
                        $price = $this->_getMinimalPrice($p);
                    } else {
                        return false;
                    }
                    break;
                case '5': // Starting to Price
                    if ($p->isGrouped()) {
                        $price = $this->_getMaximalPrice($p);
                    } else {
                        return false;
                    }
                    break;
                case '0': // Base Price
                default:
                    $price = $p->getPrice();
                    break;
            }

            if ($p->getTypeId() == 'bundle') {
                $priceMin = $p->getMinPrice();
                $priceMax = $p->getMaxPrice();
                if ($priceMin < $this->getFromPrice() || $priceMax > $this->getToPrice()) {
                    return false;
                }
            } else {
                if ($price < $this->getFromPrice() || $price > $this->getToPrice()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getValue($key)
    {
        return $this->_getData($this->getMode() . '_' . $key);
    }

    protected function _getMinimalPrice($product)
    {
        $minimalPrice = Mage::helper('tax')->getPrice($product, $product->getMinimalPrice(), true);
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach ($associatedProducts as $item) {
                $temp = Mage::helper('tax')->getPrice($item, $item->getFinalPrice(), true);
                if (is_null($minimalPrice) || $temp < $minimalPrice) {
                    $minimalPrice = $temp;
                }
        }

        return $minimalPrice;
    }

    protected function _getMaximalPrice($product)
    {
        $maximalPrice = 0;
        $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach ($associatedProducts as $item) {
                    if ($qty = $item->getQty() * 1) {
                    $maximalPrice += $qty * Mage::helper('tax')->getPrice($item, $item->getFinalPrice(), true);
                } else {
                    $maximalPrice += Mage::helper('tax')->getPrice($item, $item->getFinalPrice(), true);
                }
            }
        if (!$maximalPrice) {
            $maximalPrice = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
        }

        return $maximalPrice;
    }
    
    protected function _isNew($p)
    {
        $fromDate = '';
        $toDate   = '';
        if (Mage::getStoreConfig('amlabel/new/is_new')) {
            $fromDate = $p->getNewsFromDate();
            $toDate   = $p->getNewsToDate();
        }

        if (!$fromDate && !$toDate) {
            if (Mage::getStoreConfig('amlabel/new/creation_date')) {
                $days = Mage::getStoreConfig('amlabel/new/days');
                if (!$days)
                    return false;
                $createdAt = $this->_info['created_at'];
                $now = Mage::getModel('core/date')->date('U');
                return ($now - $createdAt <= $days * 86400); // 60 sec. * 60 min. * 24 hours = 86400 sec.
            } else {
                return false;
            }
        }
        
        $now = Mage::getModel('core/date')->date();
        
        if ($fromDate && $now < $fromDate)
            return false;

        if ($toDate) { 
            $toDate = str_replace('00:00:00', '23:59:59', $toDate);
            if ($now > $toDate)
                return false;
        }

        return true;
    }
    
    protected function _isSale()
    {
        if ($this->_info['price'] <= 0)
            return false;

        if ($this->getSpecialPriceOnly()
            && !$this->_info['special_price'])
            return false;

        $value = $this->_info['price'] - $this->_info['special_price'];
        if ($value < 0.001 )
            return false;

        // in dollars
        $min = Mage::getStoreConfig('amlabel/general/sale_min');
        if ($min && $value < $min)
            return false;

        // in percents
        $value = $this->_calculatePercentDiscount($value);
        $minPercent = Mage::getStoreConfig('amlabel/general/sale_min_percent');
        if ($minPercent && $value < $minPercent)
            return false;
        
        return true;
    }
    
    public function getImageUrl()
    {
        if ($this->getValue('img')) {
            return Mage::getBaseUrl('media') . 'amlabel/' . $this->getValue('img');
        } else {
            return '';
        }
    }

    public function getImageInfo()
    {
        if ($this->getImagePath()) {
            $info = getimagesize($this->getImagePath());
            if (!$info && strpos($this->getImagePath(), 'svg') !== false) {
                $xml = simplexml_load_file($this->getImagePath());
                if ($xml) {
                    $attr = $xml->attributes();
                    $info = array(intVal($attr->width) . 'pt', intVal($attr->height) . 'pt');
                } else {
                    return array();
                }
            } else {
                $info[0] .= 'px';
                $info[1] .= 'px';
            }
        } else {
            return array();
        }

        return array('w'=>$info[0], 'h'=>$info[1]);
    }

    public function getImagePath()
    {
        if (!$this->getValue('img')) {
            return false;
        }

        return Mage::getBaseDir('media') . '/amlabel/' . $this->getValue('img');
    }

    public function getCssClass()
    {
        $all = $this->getAvailablePositions(false);
        return $all[$this->getValue('pos')];
    }

    public function getAvailablePositions($asText = true)
    {
        $a = array();
        foreach (array('top', 'middle', 'bottom') as $first) {
            foreach (array('left', 'center', 'right') as $second) {
                $a[] = $asText ?
                    Mage::helper('amlabel')->__(ucwords($first . ' ' . $second))
                    :
                    $first . '-' . $second;
            }
        }

        return $a;
    }
    
    public function getStyle()
    {
        return $this->getValue('style');
    }

    public function getText()
    {
        $txt = $this->getValue('txt');

        $vars = array();
        preg_match_all('/{([a-zA-Z:\_0-9]+)}/', $txt, $vars);
        if (!$vars[1]) {
            return $txt;
        }

        $vars  = $vars[1];
        $store = Mage::app()->getStore();
        $p     = $this->getProduct();

        foreach ($vars as $var) {
            $value = '';
            switch ($var) {
                case 'PRICE':
                    //don't show empty price for grouped products
                    if ($this->_info['price'] || (!$this->_info['price'] && $p->getTypeId() !== 'grouped')) {
                        $value = $this->_convertPrice($this->_info['price']);
                    }
                    break;
                case 'SPECIAL_PRICE':
                    $value = $this->_convertPrice($this->_info['special_price']);
                    break;
                case 'SPDL':
                    $toDate = $p->getSpecialToDate();
                    if($toDate) {
                        $currentTime = Mage::getModel('core/date')->date();

                        $diff = strtotime($toDate) - strtotime($currentTime);
                        $value = floor($diff / (60*60*24));//days
                    }

                    break;
                case 'SPHL':
                    $toDate = $p->getSpecialToDate();
                    if($toDate) {
                        $currentTime = Mage::getModel('core/date')->date();

                        $diff = strtotime($toDate) - strtotime($currentTime);
                        $value = floor($diff / (60*60));//hours
                    }
                    break;
                case 'FINAL_PRICE':
                    $value = $this->_convertPrice(Mage::helper('tax')->getPrice($p, $p->getFinalPrice()));
                    break;
                case 'FINAL_PRICE_INCL_TAX':
                    $value = $this->_convertPrice(Mage::helper('tax')->getPrice($p, $p->getFinalPrice(), true));
                    break;
                case 'STARTINGFROM_PRICE':
                    $value = $this->_convertPrice($this->_getMinimalPrice($p));
                    break;
                case 'STARTINGTO_PRICE':
                    $value = $this->_convertPrice($this->_getMaximalPrice($p));
                    break;
                case 'SAVE_AMOUNT':
                    $diff = $this->_info['price'] - $this->_info['special_price'];
                    if ($diff > 0) {
                        $value = $this->_convertPrice($diff);
                    }
                    break;
                case 'SAVE_PERCENT':
                    $value = 0;
                    if ($this->_info['price']) {
                        $value = $this->_info['price'] - $this->_info['special_price'];
                        $value = $this->_calculatePercentDiscount($value);
                    }

                    if ($value <= 0) {
                        $value = '';
                    }
                    break;

                case 'BR':
                    $value = '<br/>';
                    break;

                case 'SKU':
                    $value = $p->getSku();
                    break;

                case 'NEW_FOR':
                    $createdAt = strtotime($p->getCreatedAt());
                    $value     = max(1, floor((time() - $createdAt) / 86400));
                    break;

                case 'STOCK':
                    if (!in_array($p->getTypeId(), array('configurable', 'grouped', 'bundle'))) {
                        $value = $this->_getProductQty($p);
                    }
                    break;

                default:
                    $str = 'ATTR:';
                    if (substr($var, 0, strlen($str)) == $str) {
                        $code  = trim(substr($var, strlen($str)));
                        
                        $decimal = null;
                        if (false !== strpos($code, ':')) {
                            $temp = explode(':', $code);
                            $code = $temp[0];
                            $decimal = $temp[1];
                        }
                        
                        $value = $p->getData($code);
                        if (is_numeric($value) && $p->getData($code . '_value')) {
                            $value = $p->getData($code . '_value');
                        }
                        
                        if (!is_null($decimal)
                            && false !== strpos($value, '.')) {
                            $temp = explode('.', $value);
                            $value = $temp[0] . '.' . substr($temp[1], 0, $decimal);
                        }
                        
                        if ($value && $code) {
                            try {
                                $value = strip_tags($this->_getAttributeValue($code, $value));
                            } catch(Mage_Eav_Exception $exception) {
                                $value = '';
                            }
                        }
                    }
            }
            $txt = str_replace('{' . $var . '}', $value, $txt);
        }

        return $txt;
    }

    protected function _getAttributeValue($code, $pValue)
    {
        $value     = $pValue;
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $code);
        if ($attribute->getId() && $inputType = $attribute->getFrontend()->getInputType()) {
            switch ($inputType) {
                case 'select':
                case 'boolean':
                    $value = $this->_getLabelForOption($attribute, $value);
                    break;
                case 'multiselect':
                    $temp   = '';
                    $values = explode(',', $value);
                    foreach ($values as $val) {
                        $temp .= $this->_getLabelForOption($attribute, $val) . ', ';
                    }
                    if ($temp) {
                        $temp = substr($temp, 0, -2);
                    }
                    $value = $temp;
                    break;
            }
        }

        return $value;
    }

    protected function _getLabelForOption($attribute, $value)
    {
        if (!$attribute->getSourceModel()) {
            $attribute->setSourceModel('eav/entity_attribute_source_table');
        }

        $optionId       = $attribute->getSource()->getOptionId($value);
        $db             = Mage::getSingleton('core/resource')->getConnection('core_read');
        $table          = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
        $select         = $db->select()->from($table)->where('option_id = ?', $optionId);
        $labels         = $db->fetchAll($select);
        $labelForOption = '';
        foreach ($labels as $label) {
            if ((Mage::app()->getStore()->getId() == $label['store_id']) && ('' !== $label['value'])) {
                $labelForOption = $label['value'];
            } elseif ((0 == $label['store_id']) && ('' === $labelForOption)) {
                $labelForOption = $label['value'];
            }
        }

        return $labelForOption;
    }

    public function getJs()
    {
        $js = 'ondblclick="if(product_zoom)product_zoom.toggleFull.bind(product_zoom)();"';
        if ('cat' == $this->getMode()) {
            $url = $this->getProduct()->getProductUrl();
            $js  = 'onclick="window.location=\'' . $url . '\'"';
        }

        return $js;
    }

    private function _getProductQty($product)
    {
        $stockItem   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        $quantity = $stockItem->getData('qty');

        return  intval($quantity);
    }

    /**
     * @param $price
     * @return string
     */
    protected function _convertPrice($price)
    {
        if (!$this->store) {
            $this->store = Mage::app()->getStore();
        }
        $price = strip_tags($this->store->convertPrice($price, true, false));
        //$price = $this->cutZeroes($price);

        return $price;
    }

    /**
     * Cut trailing zeroes
     *
     * @param string $value
     * @return string
     */
    private function cutZeroes($value)
    {
        if (!$this->separator) {
            $symbols = Zend_Locale_Data::getList(Mage::app()->getLocale()->getLocaleCode(), 'symbols');
            $separator = isset($symbols['decimal']) ? $symbols['decimal'] : '.';
            $this->separator = $separator;
        }
        $priceRegexp = '@(\d+(?:[^\\' . $this->separator . ']\d+)*)\\' . $this->separator . '0+@';
        $value = preg_replace($priceRegexp, '$1', $value);

        return $value;
    }

    /**
     * Round diff of price and special price by config setting
     *
     * @param $value
     * @return float
     */
    protected function _calculatePercentDiscount($value)
    {
        switch (Mage::getStoreConfig('amlabel/general/rounding')) {
            case 'floor':
                $value = floor($value * 100 / $this->_info['price']);
                break;
            case 'round':
                $value = round($value * 100 / $this->_info['price']);
                break;
            case 'ceil':
                $value = ceil($value * 100 / $this->_info['price']);
                break;
        }

        return $value;
    }
}
