<?php
/**
 * Helper class
 * Class Blugento_Theme_Helper_Data
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
 * @package     Blugento_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Theme_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param $variable         Variable ID
     * @param string $type Possible values: sass, image, layout
     * @param mixed $defaut Default value
     * @return mixed
     */
    public function getVariable($variable, $type = 'sass', $default = null)
    {
        try {
            switch ($type) {
                case 'image':
                    $xmlSaveValues = Mage::getSingleton('blugento_designcustomiser/scss_save_image_xml');
                    break;
                case 'layout':
                    $xmlSaveValues = Mage::getSingleton('blugento_designcustomiser/layout_save_xml');
                    break;
                case 'sass':
                default:
                    $xmlSaveValues = Mage::getSingleton('blugento_designcustomiser/scss_save_xml');
            }
            if (is_object($xmlSaveValues)) {
                $value = $xmlSaveValues->getVariableValueById($variable);
            }
            if (!$value && $default !== null) {
                $value = $default;
            }

            return $value;
        } catch (Exception $e) {
            Mage::logException($e);
            return $default;
        }
    }

    /**
     * Get real sizes of image from media/catalog/product folder
     *
     * @param string $filename
     * @return array
     */
    public function getImageSizeArray($filename)
    {
        $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $filename;

        if (file_exists($path)) {
            $imageObj = new Varien_Image($path);
            return array(
                'height' => $imageObj->getOriginalHeight(),
                'width' => $imageObj->getOriginalWidth()
            );
        }

        return false;
    }

    public function isCatalogPage()
    {
        $routeName = Mage::app()->getRequest()->getRouteName();
        return $routeName == 'catalog';
    }

    public function isSkinFileInCurrentTheme($path)
    {
        $_skinBaseUrl = Mage::getBaseUrl('skin', Mage::app()->getStore()->isCurrentlySecure());
        $_fullPathUrl = Mage::getDesign()->getSkinUrl($path);
        $_fullPath = Mage::getBaseDir('skin') . DS . str_replace($_skinBaseUrl, '', $_fullPathUrl);

        return is_file($_fullPath);
    }

    public function getBlugentoUrl()
    {
        $_blugentoUrl = $this->__('blugento_url');
        if ($_blugentoUrl == 'blugento_url') {
            $_blugentoUrl = 'https://www.zentoshop.com';
        }

        return $_blugentoUrl;
    }

    public function getResizeCategoryImage($_imgUrl, $width, $height)
    {
        $imageResizedUrl = '';

        if ($_imgUrl) {
            if ($width != '' && $height != '') {
                $folder = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . 'resized' . DS . $width . 'x' . $height;
            } else {
                $folder = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . 'resized';
            }

            if (!file_exists($folder)) {
                mkdir($folder, 0777);
            }

            // get image name
            $imageName = substr(strrchr($_imgUrl, '/'), 1);

            // resized image path (media/catalog/category/resized/IMAGE_NAME)
            $imageResized = $folder . DS . $imageName;

            // changing image url into direct path
            $dirImg = Mage::getBaseDir() . str_replace('/', DS, strstr($_imgUrl, '/media'));

            // if resized image doesn't exist, save the resized image to the resized directory
            if (!file_exists($imageResized) && file_exists($dirImg)) {
                $imageObj = new Varien_Image($dirImg);
                $imageObj->constrainOnly(true);
                $imageObj->keepAspectRatio(true);
                $imageObj->keepTransparency(true);
                $imageObj->backgroundColor(array(255, 255, 255));
                $imageObj->keepFrame(true);
                $imageObj->resize($width ?: $imageObj->getOriginalWidth(), $height ?: $imageObj->getOriginalHeight());
                $imageObj->save($imageResized);
            }

            if ($width != '' && $height != '') {
                $imageResizedUrl = Mage::getBaseUrl('media') . 'catalog/category/resized' . DS . $width . 'x' . $height . DS . $imageName;
            } else {
                $imageResizedUrl = Mage::getBaseUrl('media') . 'catalog/category/resized' . DS . $imageName;
            }
        }

        return $imageResizedUrl;
    }

    /**
     * Get Store name
     *
     * @return string Store name
     */
    public function getStoreName()
    {
        $_siteName = Mage::getStoreConfig('blugentolocalizer/imprint/shop_name');
        if (!$_siteName) {
            $_siteName = Mage::getStoreConfig('general/store_information/name');
        }
        return $_siteName;
    }

    public function hex2rgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        $rgb = array($r, $g, $b);

        return $rgb;
    }

    public function getDiscountPercentage($_product)
    {
        $_actualPrice = $_product->getPrice();
        $_specialPrice = $_product->getFinalPrice();
        $_priceDiscountPercentage = '';
        if ($_specialPrice != $_actualPrice && $_actualPrice != 0) {
            $_priceDiscountPercentage = '-' . (round((100 - ($_specialPrice / $_actualPrice) * 100))) . "%";
        }

        return $_priceDiscountPercentage;
    }

    public function getDiscountValue($_product)
    {
        $_actualPrice = $_product->getPrice();
        $_specialPrice = $_product->getFinalPrice();
        $_priceDiscountValue = '';
        if ($_specialPrice != $_actualPrice) {
            $_priceDiscountValue = '(' . Mage::helper('core')->currency(($_specialPrice - $_actualPrice), true, false) . ')';
        }

        return $_priceDiscountValue;
    }

    public function getHashP($orderID)
    {
        $secretKey = Mage::getStoreConfig('blugento_theme/profitshare/secretkey');
        $myHash = hash_hmac('sha1', $orderID, $secretKey);
        return $myHash;
    }

    /**
     * Return the product badges.
     *
     * @param $_product
     * @return array
     */
    public function getProductBagdes($_product)
    {
        $badges   = array();
        $rightNow = time();

        $isSale =
	        (
	        	$_product->getSpecialPrice() &&
	            (
	                $rightNow >= strtotime($_product->getSpecialFromDate()) && $rightNow <= strtotime($_product->getSpecialToDate()) + 86400 ||
	                $rightNow >= strtotime($_product->getSpecialFromDate()) && is_null($_product->getSpecialToDate()) ||
	                $rightNow <= strtotime($_product->getSpecialToDate()) + 86400 && is_null($_product->getSpecialFromDate())
	            )
	        ) ||
	        (!Mage::helper('core')->isModuleEnabled('Blugento_ForeignCurrency') && $_product->getFinalPrice() != $_product->getPrice()) ||
	        ($this->roundUp($_product->getFinalPrice(), 0.5) != $this->roundUp($_product->getPrice(), 0.5));

        $isNew = ( $_product->getNewsFromDate() || $_product->getNewsToDate() ) &&
            (
                $rightNow >= strtotime($_product->getNewsFromDate()) && $rightNow <= strtotime($_product->getNewsToDate()) + 86400 ||
                $rightNow >= strtotime($_product->getNewsFromDate()) && is_null($_product->getNewsToDate()) ||
                $rightNow <= strtotime($_product->getNewsToDate()) + 86400 && is_null($_product->getNewsFromDate())
            );

        if ($isSale) {
            $helperTheme = Mage::helper('blugento_theme');
            $_productListDiscountPercentageMode = (int)(Mage::app()->getLayout()->getBlock('root')->getProductListDiscountPercentageMode() ?: 2);

            $discountPercentage = $helperTheme->getDiscountPercentage($_product);

            if ($discountPercentage && ($_productListDiscountPercentageMode == 1)) {
                $label = $discountPercentage;
            } else {
                $label = $this->__('Sale!');
            }
            $badges['badge--sale'] = $label;
        }
        
        if ($isNew) {
            $badges['badge--new'] = $this->__('New!');
        }

        return array_unique($badges);
    }

    /**
     * Return current year.
     *
     * @return string
     */
    public function getCurrentYear()
    {
        return date('Y');
    }

    /**
     * Return tracking analytics.
     *
     * @return boolean
     */
    public function trackingAfterCookiesAccepted()
    {
        return Mage::getStoreConfig('web/cookie/cookie_tracking_analytics');
    }
    
    private function roundUp($number, $precision = 2)
    {
        $fig = pow(10, $precision);
        
        return round(ceil($number / $fig) * $fig, 2);
    }

    public function getCatalogRuleToDate($_product, $_promotionCountdown, $_catalogPromotionCountdown)
    {
        if ($_catalogPromotionCountdown == 1 && $_promotionCountdown == 1) {
            $catalogRuleFinalPrice = Mage::getModel('catalogrule/rule')->calcProductPriceRule($_product, $_product->getPrice());

            if (
                ($_product->getSpecialPrice() && $catalogRuleFinalPrice < floatval($_product->getSpecialPrice()))
                || (!$_product->getSpecialPrice() && $catalogRuleFinalPrice)
            ) {
                Mage::getModel('catalogrule/rule')->loadProductRules($_product);

                foreach ($_product->getMatchedRules() as $ruleId=>$ruleval) {
                    /** @var Mage_CatalogRule_Model_Rule $rule */
                    $rule = Mage::getModel('catalogrule/rule')->load($ruleId);
                    return $rule->getToDate();
                }
            } else {
                return '';
            }
        } else if ($_catalogPromotionCountdown == 1 && $_promotionCountdown == 2 && !$_product->getSpecialPrice()) {
            Mage::getModel('catalogrule/rule')->loadProductRules($_product);

            foreach ($_product->getMatchedRules() as $ruleId=>$ruleval) {
                /** @var Mage_CatalogRule_Model_Rule $rule */
                $rule = Mage::getModel('catalogrule/rule')->load($ruleId);
                return $rule->getToDate();
            }
        } else {
            return '';
        }
    }
}
