<?php

class Blugento_Fixdiacritics_Model_Product_Url extends Mage_Catalog_Model_Product_Url
{
    public function formatUrlKey($str)
    {
        $helper = Mage::helper('fixdiacritics');
        $str = $helper->sanitizeText($str);

        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }
}