<?php
/**
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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Localizer_Model_Setup_Country_De extends Blugento_Localizer_Model_Setup_Abstract
{
    /**
     * Setup for custom functionality
     *
     * @param array $locale Locale[store, code]
     */
    public function setup($locale)
    {
        $storeId = $locale['store'];

        $this->_defaultDeliveryTime();
        $this->_defaultHomepage($storeId);
        $this->_defaultOptionalDescription();
        $this->_defaultOptionalShortDescription();
        $this->_defaultBlocksTitle();
        $this->_defaultBlocksContent();
        $this->_defaultDisabledModules();
        $this->_defaultShippingMessages($storeId);
        $this->_defaultAttributes();
    }

    protected function _defaultAttributes()
    {
        $setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
        $setup->updateAttribute('catalog_product','short_description','is_required', 0);
        $setup->updateAttribute('catalog_product','description','is_required', 0);
        $setup->updateAttribute('catalog_product','weight','is_required', 0);
    }

    /**
     * Set default error messages for Magento default shipping methods
     * Run only at store level
     *
     * @param $storeId
     */
    protected function _defaultShippingMessages($storeId)
    {
        if (!$storeId || $storeId == 'default') {
            return false;
        }

        $scope = 'stores';
        $scopeId = $storeId;

        $carriers = array(
            'freeshipping',
            'dhlint',
            'dhl',
            'fedex',
            'usps',
            'ups',
            'tablerate',
            'flatrate'
        );

        $setup = Mage::getModel('eav/entity_setup', 'core_setup');
        $message = 'Diese Versandart ist momentan nicht verfügbar. Bitte kontaktieren Sie uns, wenn Sie diese Versandart nutzen möchten.';

        foreach ($carriers as $carrier) {
            $key = 'carriers/' . $carrier . '/specificerrmsg';
            if (!Mage::getStoreConfig($key)) {
                continue;
            }
            $setup->setConfigData($key, $message, $scope, $scopeId);
        }

        return true;
    }

    /**
     * Disable modules
     * @return bool
     */
    protected function _defaultDisabledModules()
    {
        $this->_disableModule('Mage_Tag');

        try {

            $model = new Blugento_DesignCustomiser_Model_Layout_Save_DisableModule();
            $model->saveFromConfig();

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return true;
    }

    /**
     * Set German title for blocks created by theme
     * @return bool
     */
    protected function _defaultBlocksTitle()
    {
        $content = '<ul class="links-after"><li><a href="{{store url="contacts"}}" title="Kontakt">Kontakt</a></li></ul>';

        $this->_renameBlock('blugento-navigation-before-links', 'Blugento Navigation Links davor');
        $this->_renameBlock('blugento-navigation-after-links', 'Blugento Navigation Links danach', $content);

        return true;
    }

    /**
     * Set German content for blocks
     * @return bool
     */
    protected function _defaultBlocksContent()
    {
        $content = '<p>Diese Website verwendet Cookies, um alle Funktionen anbieten zu können. Erfahren Sie mehr über Cookies und die erhobenen Daten unter <a href="{{store direct_url="datenschutz"}}">Datenschutz</a>. Sie stimmen durch die weitere Nutzung der Website der Einbindung von Cookies zu.</p>';
        $this->_updateBlock('cookie_restriction_notice_block', $content);

        return true;
    }

    /**
     * Set description product attributes to be optional
     * @return bool
     */
    protected function _defaultOptionalDescription()
    {
        try {
            $model = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'description');
            $model->setIsRequired(0)->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    /**
     * Set description and short_description product attributes to be optional
     * @return bool
     */
    protected function _defaultOptionalShortDescription()
    {
        try {
            $model = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'short_description');
            $model->setIsRequired(0)->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    /**
     * Set default value for delivery_time product attribute
     * @param string $defaultValue
     * @return bool
     */
    protected function _defaultDeliveryTime($defaultValue = '2-3 Tage')
    {
        try {
            $model = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'delivery_time');
            $model->setDefaultValue($defaultValue)->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    /**
     * Setup for homepage
     *
     * @param string $storeId Store identifier
     */
    protected function _defaultHomepage($storeId)
    {
        if (is_null($storeId) || $storeId == 'default') {
            $storeId = 0;
        }
        $page = $this->_getDefaultHomepage($storeId);

        if ($page) {
            $page->setTitle('Startseite');
            $page->setRootTemplate('one_column');
            $page->save();
        }
    }

    /**
     * Retrieve default homepage for the given store identifier
     *
     * @param  int $storeId Store identifier
     * @return Mage_Cms_Model_Page Page Model
     */
    protected function _getDefaultHomepage($storeId)
    {
        /** @var $cmsPage Mage_Cms_Model_Page */
        $cmsPage = Mage::getModel('cms/page')
            ->setStoreId($storeId)
            ->load(Mage::getStoreConfig('web/default/cms_home_page', $storeId));

        if (!$cmsPage->getId()) {
            return null;
        }

        return $cmsPage;
    }

    /**
     * Set block title
     *
     * @param string $blockId
     * @param string $newTitle
     * @return Mage_Cms_Model_Block|null
     * @throws Exception
     */
    protected function _renameBlock($blockId, $newTitle, $content = null)
    {
        /** @var $cmsPage Mage_Cms_Model_Block */
        $cmsBlock = Mage::getModel('cms/block')->load($blockId);

        if (!$cmsBlock->getId()) {
            return null;
        }

        if ($content !== null) {
            $cmsBlock->setContent($content);
        }
        $cmsBlock->setTitle($newTitle);
        $cmsBlock->save();

        return $cmsBlock;
    }

    /**
     * Disable a module output
     * @param string $moduleName
     * @return bool
     */
    protected function _disableModule($moduleName)
    {
        try {
            $configModel = new Mage_Core_Model_Config();
            $configModel->saveConfig('advanced/modules_disable_output/' . $moduleName, 1, 'default', 0);

            $model = new Blugento_DesignCustomiser_Model_Layout_Save_DisableModule();
            $model->saveFromConfig();

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    /**
     * Set block content
     *
     * @param string $blockId
     * @param string $newTitle
     * @return Mage_Cms_Model_Block|null
     * @throws Exception
     */
    protected function _updateBlock($blockId, $newContent)
    {
        /** @var $cmsPage Mage_Cms_Model_Block */
        $cmsBlock = Mage::getModel('cms/block')->load($blockId);

        if (!$cmsBlock->getId()) {
            return null;
        }

        $cmsBlock->setContent($newContent);
        $cmsBlock->save();

        return $cmsBlock;
    }
}
