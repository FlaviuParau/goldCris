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
 * @package     Blugento_Shell
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Log Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Blugento extends Mage_Shell_Abstract
{
    private function _getConfig()
    {
        return new Mage_Core_Model_Config();
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('setpackage')) {
            $packageName = $this->getArg('name');
            $packageName = $packageName ? $packageName : 'blugento';

            $this->_getConfig()->saveConfig('design/package/name', $packageName);

            echo 'Package ' . $packageName . ' was set';

        } else if ($this->getArg('localizer')) {

            $country = $this->getArg('country');
            $storeId = $this->getArg('storeid') ? $this->getArg('storeid') : 0;

            if ($country) {
                try {
                    Mage::register('setup_country', $country);
                    /** @var Blugento_Localizer_Model_Setup $model */
                    $model = Mage::getModel('blugento_localizer/setup');
                    $model->runLocalizerSetup($country, $storeId);
                    echo 'Localizer for country: "' . $country . '", storeid: "' . $storeId . '" was run with success.';
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                echo 'Missing "country" parameters.';
            }

        } else if ($this->getArg('deletepoll')) {
            try {
                $poll = Mage::getModel('poll/poll')->load(1);
                $poll->delete();
                echo 'Default Poll was deleted successfully.';
            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else if ($this->getArg('formkeyenable')) {
            try {
                $this->_getConfig()->saveConfig('admin/security/validate_formkey_checkout', 1);

                echo 'Form Key Validation On Checkout was enabled.';

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else if ($this->getArg('hidebanner')) {
            try {
                $this->_getConfig()->saveConfig('paypal/style/logo', ' ');
                echo 'PayPal logo was disabled';

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        }  else if ($this->getArg('multishippingoff')) {
            try {
                $this->_getConfig()->saveConfig('shipping/option/checkout_multiple', '0');

                echo 'Allow Shipping to Multiple Addresses was disabled.';

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else if ($this->getArg('setcountry')) {
            try {
                $country  = $this->getArg('country');
                $allow    = $this->getArg('allow');

                $this->_getConfig()->saveConfig('general/country/default', $country);
                $this->_getConfig()->saveConfig('general/country/allow', $allow);

                echo 'Country configuration success.';

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else if ($this->getArg('createadminrole')) {
            try {
                $type = $this->getArg('type');

                if (!$type) {
                    $roles = array('storeowner', 'manager', 'sales', 'catalogmanager');
                    foreach ($roles as $role) {
                        $this->_createAdminRoles($role);
                    }
                } else if($type) {
                    $this->_createAdminRoles($type);
                }

                echo 'Admin Roles created successfully.';

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else if ($this->getArg('storeemails')) {
            $type  = $this->getArg('type');
            $name  = $this->getArg('name');
            $email = $this->getArg('email');
            try {
                $this->_getConfig()->saveConfig('trans_email/ident_' . $type . '/email', $email);
                $this->_getConfig()->saveConfig('trans_email/ident_' . $type . '/name', $name);

                echo 'Store Email Address configuration success.';
            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else if ($this->getArg('smtp')) {
            $user = $this->getArg('user');
            $pass = Mage::helper('core')->encrypt($this->getArg('pass'));
            $host = $this->getArg('host');
            $port = $this->getArg('port');
            $ssl  = $this->getArg('ssl');
            try {
                $this->_getConfig()->saveConfig('smtppro/general/option', 'smtp');
                $this->_getConfig()->saveConfig('smtppro/general/smtp_authentication', 'login');
                $this->_getConfig()->saveConfig('smtppro/general/smtp_username', $user);
                $this->_getConfig()->saveConfig('smtppro/general/smtp_password', $pass);
                $this->_getConfig()->saveConfig('smtppro/general/smtp_host', $host);
                $this->_getConfig()->saveConfig('smtppro/general/smtp_port', $port);
                $this->_getConfig()->saveConfig('smtppro/general/smtp_port', $port);
                $this->_getConfig()->saveConfig('smtppro/general/smtp_ssl', $ssl); // none, ssl, tls

                $this->_getConfig()->saveConfig('smtppro/queue/usage', 'never');
                $this->_getConfig()->saveConfig('smtppro/debug/logenabled', 1);

                echo 'SMTP Pro configuration success.';

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else if ($this->getArg('cleanupmedia')) {
            echo 'Starting cleanup process...' . PHP_EOL;

            $result = $this->cleanUpProductMedia();

            echo 'Product media successfully cleaned up! ' . $result['images'] . ' images were deleted. Total deleted size: ' . $result['size'] . '.' . PHP_EOL;
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
        
Available Process:
        setpackage        Set the Magento theme package
        localizer         Run the Blugento localizer
        deletepoll        Delete the default Magento Poll 'What is your favorite color'
        formkeyenable     Enable Form Key Validation On Checkout
        hidebanner        Disabled PayPal logo
        multishippingoff  Disabled Allow Shipping to Multiple Addresses
        setcountry        Configure Default Country and Allow Countries
        createadminrole   Create Admin Role and set related permissions
        storeemails       Configure Store Email Addresses
        smtp              Configure SMTP Pro module
        cleanupmedia      Delete unused product images and media gallery images
        help              This help
  
Usage:  
        php -f blugento.php --[options]
        php -f blugento.php --setpackage --name (default 'blugento' package will be set if 'name' is not set)
        php -f blugento.php --localizer --country --storeid (default '0' store id will be set if 'storeid' is not set, use 'ro_RO' structure for 'country')
        php -f blugento.php --deletepoll
        php -f blugento.php --formkeyenable
        php -f blugento.php --hidebanner
        php -f blugento.php --multishippingoff
        php -f blugento.php --setcountry --country --allow (Add Country code, like RO and for 'allow' param add countries separated by comma like RO,IT)
        php -f blugento.php --createadminrole --type ('type' possible values: 'storeowner', 'manager', 'sales' or 'catalogmanager', if missing all the available Admin Role will be created.)
        php -f blugento.php --storeemails --type --name --email ('type' possible values: 'custom1', 'custom2', 'general', 'sales' or 'support')
        php -f blugento.php --smtp --user --pass --host --port --ssl ('ssl' possible values: 'none', 'ssl' or 'tls')
        php -f blugento.php --cleanupmedia
        php -f blugento.php --help


USAGE;
    }

    private function _createAdminRoles($type)
    {
        /**
         * Create new role
         */
        $role = Mage::getModel("admin/roles")
            ->setName($type)
            ->setRoleType('G')
            ->save();

        switch ($type) {
            case 'storeowner':
                $resources = $this->_getStoreOwnerRoleResources();
                break;
            case 'manager':
                $resources = $this->_getManagerRoleResources();
                break;
            case 'sales':
                $resources = $this->_getSalesRoleResources();
                break;
            case 'catalogmanager':
                $resources = $this->_getCatalogmanagerRoleResources();
                break;
            default:
                $resources = null;
                break;
        }

        /**
         * Give privileges to role
         */
        if ($resources) {
            Mage::getModel("admin/rules")
                ->setRoleId($role->getId())
                ->setResources($resources)
                ->saveRel();
        }
    }

    private function _getStoreOwnerRoleResources()
    {
        return array(
            'admin/blugento_adminmenu','admin/blugento_adminmenu/blugento_designcustomiser','admin/blugento_adminmenu/blugento_erpintegration','admin/blugento_adminmenu/blugento_homepagemanager','admin/blugento_adminmenu/blugento_sliders','admin/blugento_adminmenu/configuration','admin/blugento_adminmenu/importexport','admin/catalog','admin/catalog/ajaxproductscroller','admin/catalog/ajaxproductscroller/settings','admin/catalog/attributes','admin/catalog/attributes/attributes','admin/catalog/attributes/sets','admin/catalog/categories','admin/catalog/products','admin/catalog/reviews_ratings','admin/catalog/reviews_ratings/ratings','admin/catalog/reviews_ratings/reviews','admin/catalog/reviews_ratings/reviews/all','admin/catalog/reviews_ratings/reviews/pending','admin/catalog/search','admin/catalog/sitemap','admin/catalog/tag','admin/catalog/tag/all','admin/catalog/tag/pending','admin/catalog/update_attributes','admin/catalog/urlrewrite','admin/cms','admin/cms/block','admin/cms/media_gallery','admin/cms/page','admin/cms/page/delete','admin/cms/page/save','admin/cms/poll','admin/cms/widget_instance','admin/customer','admin/customer/group','admin/customer/manage','admin/customer/online','admin/dashboard','admin/dashboard/ebizmarts_abandonedcart','admin/exporter','admin/exporter/export','admin/exporter/import','admin/global_search','admin/ikantam','admin/ikantam/webtoprint','admin/Mconnect_Ajaxproductscroller','admin/newsletter','admin/newsletter/magemonkey','admin/newsletter/magemonkey/bulksync','admin/newsletter/magemonkey/bulksync/mage_to_mc','admin/newsletter/magemonkey/bulksync/mc_to_mage','admin/newsletter/magemonkey/ebizmarts_emails','admin/newsletter/magemonkey/ecommerce','admin/newsletter/magemonkey/ecommerce/apicommerce','admin/newsletter/magemonkey/ecommerce/commerce','admin/newsletter/mailchimp','admin/newsletter/mailchimp/mailchimperrors','admin/newsletter/problem','admin/newsletter/queue','admin/newsletter/subscriber','admin/newsletter/template','admin/page_cache','admin/productattachment','admin/promo','admin/promo/catalog','admin/promo/quote','admin/promo/zitec_tablerates','admin/sales','admin/sales/billing_agreement','admin/sales/billing_agreement/actions','admin/sales/billing_agreement/actions/manage','admin/sales/billing_agreement/actions/use','admin/sales/billing_agreement/actions/view','admin/sales/checkoutagreement','admin/sales/creditmemo','admin/sales/ebizmarts_abandonedcart','admin/sales/invoice','admin/sales/order','admin/sales/order/actions','admin/sales/order/actions/cancel','admin/sales/order/actions/capture','admin/sales/order/actions/comment','admin/sales/order/actions/create','admin/sales/order/actions/creditmemo','admin/sales/order/actions/edit','admin/sales/order/actions/email','admin/sales/order/actions/emails','admin/sales/order/actions/hold','admin/sales/order/actions/invoice','admin/sales/order/actions/reorder','admin/sales/order/actions/review_payment','admin/sales/order/actions/ship','admin/sales/order/actions/unhold','admin/sales/order/actions/view','admin/sales/recurring_profile','admin/sales/shipment','admin/sales/tax','admin/sales/tax/classes_customer','admin/sales/tax/classes_product','admin/sales/tax/import_export','admin/sales/tax/rates','admin/sales/tax/rules','admin/sales/transactions','admin/sales/transactions/fetch','admin/system','admin/system/adminnotification','admin/system/adminnotification/mark_as_read','admin/system/adminnotification/remove','admin/system/adminnotification/show_list','admin/system/adminnotification/show_toolbar','admin/system/api','admin/system/api/authorizedTokens','admin/system/api/consumer','admin/system/api/consumer/delete','admin/system/api/consumer/edit','admin/system/api/oauth_admin_token','admin/system/api/rest_attributes','admin/system/api/rest_attributes/edit','admin/system/api/rest_roles','admin/system/api/rest_roles/add','admin/system/api/rest_roles/delete','admin/system/api/rest_roles/edit','admin/system/api/roles','admin/system/api/users','admin/system/blugento_feeds','admin/system/Blugento_Form','admin/system/blugento_marketing','admin/system/cache','admin/system/config','admin/system/config/admin','admin/system/config/advanced','admin/system/config/afipredirect','admin/system/config/ajaxproductscroller','admin/system/config/ambase','admin/system/config/amogrid','admin/system/config/amstore','admin/system/config/api','admin/system/config/blugentolocalizer','admin/system/config/blugento_billing','admin/system/config/blugento_cart','admin/system/config/blugento_categoryhover','admin/system/config/blugento_checkout','admin/system/config/blugento_compare','admin/system/config/blugento_designcustomiser','admin/system/config/blugento_ebookdownload','admin/system/config/blugento_erpintegration','admin/system/config/blugento_feeds','admin/system/config/Blugento_Form','admin/system/config/blugento_geoip','admin/system/config/blugento_marketing','admin/system/config/blugento_orderexport','admin/system/config/blugento_reset','admin/system/config/blugento_sliders','admin/system/config/blugento_smsadvert','admin/system/config/blugento_socialmedia','admin/system/config/blugento_sort','admin/system/config/blugento_statusordercolor','admin/system/config/blugento_stocknotification','admin/system/config/blugento_theme','admin/system/config/blugento_uploadfiles','admin/system/config/carriers','admin/system/config/catalog','admin/system/config/cataloginventory','admin/system/config/checkout','admin/system/config/cms','admin/system/config/configswatches','admin/system/config/contacts','admin/system/config/currency','admin/system/config/customer','admin/system/config/design','admin/system/config/dev','admin/system/config/downloadable','admin/system/config/ebizmarts_abandonedcart','admin/system/config/ebizmarts_autoresponder','admin/system/config/ebizmarts_emails','admin/system/config/facebooklb','admin/system/config/general','admin/system/config/google','admin/system/config/ikantam_webtoprint','admin/system/config/mailchimp','admin/system/config/mandrill','admin/system/config/mindmagnet_pagespeed','admin/system/config/moneybookers','admin/system/config/monkey','admin/system/config/newsletter','admin/system/config/oauth','admin/system/config/payment','admin/system/config/payment_services','admin/system/config/paypal','admin/system/config/persistent','admin/system/config/promo','admin/system/config/reports','admin/system/config/retargetingtracker_options','admin/system/config/rss','admin/system/config/sales','admin/system/config/sales_email','admin/system/config/sales_pdf','admin/system/config/sendfriend','admin/system/config/sharecsv','admin/system/config/shipping','admin/system/config/sitemap','admin/system/config/smtppro','admin/system/config/sweetmonkey','admin/system/config/system','admin/system/config/tax','admin/system/config/tmcore','admin/system/config/trans_email','admin/system/config/web','admin/system/config/wishlist','admin/system/convert','admin/system/convert/export','admin/system/convert/gui','admin/system/convert/import','admin/system/convert/profiles','admin/system/currency','admin/system/currency/rates','admin/system/currency/symbols','admin/system/design','admin/system/email_template','admin/system/email_template/mailchimp','admin/system/email_template/mailchimp/mailchimp_syncronization','admin/system/extensions','admin/system/extensions/custom','admin/system/extensions/local','admin/system/index','admin/system/myaccount','admin/system/ogrid_adminsetting','admin/system/order_statuses','admin/system/store','admin/system/tools','admin/system/tools/backup','admin/system/tools/backup/rollback','admin/system/tools/compiler','admin/system/tools/smtppro','admin/system/variable','admin/templates_master','admin/templates_master/tmcore_module','admin/zitec_dpd','admin/zitec_dpd/reports','admin/zitec_dpd/zitec_dpd','admin/zitec_dpd/zitec_dpd_payment_settings','admin/zitec_dpd/zitec_dpd_shipment_sender_settings','admin/zitec_dpd/zitec_dpd_shipment_settings'
        );
    }

    private function _getManagerRoleResources()
    {
        return array(
            'admin/catalog','admin/catalog/ajaxproductscroller','admin/catalog/ajaxproductscroller/settings','admin/catalog/attributes','admin/catalog/attributes/attributes','admin/catalog/attributes/sets','admin/catalog/categories','admin/catalog/products','admin/catalog/reviews_ratings','admin/catalog/reviews_ratings/ratings','admin/catalog/reviews_ratings/reviews','admin/catalog/reviews_ratings/reviews/all','admin/catalog/reviews_ratings/reviews/pending','admin/catalog/search','admin/catalog/sitemap','admin/catalog/tag','admin/catalog/tag/all','admin/catalog/tag/pending','admin/catalog/update_attributes','admin/catalog/urlrewrite','admin/sales','admin/sales/billing_agreement','admin/sales/billing_agreement/actions','admin/sales/billing_agreement/actions/manage','admin/sales/billing_agreement/actions/use','admin/sales/billing_agreement/actions/view','admin/sales/checkoutagreement','admin/sales/creditmemo','admin/sales/ebizmarts_abandonedcart','admin/sales/invoice','admin/sales/order','admin/sales/order/actions','admin/sales/order/actions/cancel','admin/sales/order/actions/capture','admin/sales/order/actions/comment','admin/sales/order/actions/create','admin/sales/order/actions/creditmemo','admin/sales/order/actions/edit','admin/sales/order/actions/email','admin/sales/order/actions/emails','admin/sales/order/actions/hold','admin/sales/order/actions/invoice','admin/sales/order/actions/reorder','admin/sales/order/actions/review_payment','admin/sales/order/actions/ship','admin/sales/order/actions/unhold','admin/sales/order/actions/view','admin/sales/recurring_profile','admin/sales/shipment','admin/sales/tax','admin/sales/tax/classes_customer','admin/sales/tax/classes_product','admin/sales/tax/import_export','admin/sales/tax/rates','admin/sales/tax/rules','admin/sales/transactions','admin/sales/transactions/fetch','admin/system','admin/system/blugento_marketing'
        );
    }

    private function _getSalesRoleResources()
    {
        return array(
            'admin/sales','admin/sales/ebizmarts_abandonedcart','admin/sales/order','admin/sales/order/actions','admin/sales/order/actions/cancel','admin/sales/order/actions/capture','admin/sales/order/actions/comment','admin/sales/order/actions/create','admin/sales/order/actions/creditmemo','admin/sales/order/actions/edit','admin/sales/order/actions/email','admin/sales/order/actions/emails','admin/sales/order/actions/hold','admin/sales/order/actions/invoice','admin/sales/order/actions/reorder','admin/sales/order/actions/review_payment','admin/sales/order/actions/ship','admin/sales/order/actions/unhold','admin/sales/order/actions/view'
        );
    }

    private function _getCatalogmanagerRoleResources()
    {
        return array(
            'admin/catalog','admin/catalog/ajaxproductscroller','admin/catalog/ajaxproductscroller/settings','admin/catalog/attributes','admin/catalog/attributes/attributes','admin/catalog/attributes/sets','admin/catalog/categories','admin/catalog/products','admin/catalog/reviews_ratings','admin/catalog/reviews_ratings/ratings','admin/catalog/reviews_ratings/reviews','admin/catalog/reviews_ratings/reviews/all','admin/catalog/reviews_ratings/reviews/pending','admin/catalog/search','admin/catalog/sitemap','admin/catalog/tag','admin/catalog/tag/all','admin/catalog/tag/pending','admin/catalog/update_attributes','admin/catalog/urlrewrite','admin/cms','admin/cms/block','admin/cms/media_gallery','admin/cms/page','admin/cms/page/delete','admin/cms/page/save','admin/cms/poll','admin/cms/widget_instance'
        );
    }

    private function cleanupProductMedia()
    {
        $imagePaths = array_unique($this->getUsedImages());

        $totalDeletedSize = 0;
        $totalDeletedImages = 0;
        if ($imagePaths) {
            $imagePaths = array_unique($imagePaths);

            $images = [];
            $directories = [];
            foreach ($imagePaths as $path) {
                if ($path && $path != 'no_selection') {
                    $imagesArr = explode('/', $path);

                    $images[] = end($imagesArr);
                    if ($imagesArr[0] == '') {
                        $directories[] = $imagesArr[1] . '/' . $imagesArr[2];
                    } else {
                        $directories[] = $imagesArr[0] . '/' . $imagesArr[1];
                    }
                }
            }

            $directories = array_unique($directories);
            $catalogPath = Mage::getBaseDir('media') . '/catalog/product/';

            $barIndex = 0;
            $bar = $this->progressBar(count($directories));
            foreach ($directories as $dir) {
                $fullDirPath = $catalogPath . $dir;

                $openDir = @opendir($fullDirPath);
                while (false !== ($file = readdir($openDir))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }

                    if (!in_array($file, $images)) {
                        $filePath = $fullDirPath . '/' . $file;
                        $totalDeletedSize += filesize($filePath);

                        unlink($filePath);
                        $totalDeletedImages++;
                    }
                }
                $bar->update(++$barIndex);
            }
            $bar->finish();
        }

        return [
            'images' => $totalDeletedImages,
            'size' => $this->convertSize($totalDeletedSize)
        ];
    }

    private function getUsedImages()
    {
        $attributes = $this->getImageAttributeIds();

        if ($attributes) {
            $mediaQuery = 'SELECT `value`
                           FROM `catalog_product_entity_media_gallery`';

            $imagesQuery = 'SELECT `value`
                            FROM `catalog_product_entity_varchar`
                            WHERE `attribute_id` IN (' . implode(',', $attributes) . ')';

            try {
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $media = $connection->fetchCol($mediaQuery);
                $images = $connection->fetchCol($imagesQuery);

                return array_merge($media, $images);
            } catch (Exception $e) {
                echo $e->getMessage();
                return null;
            }
        }
    }

    private function getImageAttributeIds()
    {
        $query = 'SELECT `attribute_code`, `attribute_id`
                  FROM `eav_attribute`
                  WHERE `attribute_code` IN ("image", "small_image", "thumbnail", "image_hover")
                  AND `entity_type_id` = 4';

        try {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            return $connection->fetchPairs($query);
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    private function convertSize($bytes, $decimal_places = 2) {
        $formulas = array(
            'K' => number_format($bytes / 1024, $decimal_places),
            'M' => number_format($bytes / 1048576, $decimal_places),
            'G' => number_format($bytes / 1073741824, $decimal_places)
        );

        if ($bytes > 1073741824) {
            $converted = $formulas['G'] . ' GB';
        } else if ($bytes > 1048576) {
            $converted = $formulas['M'] . ' MB';
        } else {
            $converted = $formulas['K'] . ' KB';
        }

        return $converted;
    }

    /**
     * Create a new Zend style progress bar
     *
     * Example usage:
     *     $count = 10;
     *     $bar = $this->progressBar($count);
     *     for ($i = 1; $i <= $count; $i++) $bar->update($i);
     *     $bar->finish();
     *
     * @param integer $batches
     * @param integer $start
     * @return Zend_ProgressBar
     * @throws Zend_ProgressBar_Exception
     */
    public function progressBar($batches, $start = 0)
    {
        return new Zend_ProgressBar(
            new Zend_ProgressBar_Adapter_Console(
                array(
                    'elements' => array(
                        Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                        Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                        Zend_ProgressBar_Adapter_Console::ELEMENT_ETA,
                        Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT
                    )
                )
            ),
            $start,
            $batches
        );
    }
}

$shell = new Mage_Shell_Blugento();
$shell->run();
