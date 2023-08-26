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

class Blugento_Localizer_Model_Setup_Agreements extends Blugento_Localizer_Model_Setup_Abstract
{
    /**
     * @var Mage_Eav_Model_Entity_Setup
     */
    protected $_setup;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Setup setup class and connection
     */
    public function __construct()
    {
        $this->_setup = Mage::getModel('eav/entity_setup', 'core_setup');
        $this->_connection = $this->_setup->getConnection();
    }

    /**
     * Setup Checkout Agreements
     * Run only at store view level
     *
     * @return void
     */
    public function setup($locale)
    {
        $storeId = $locale['store'];
        $localeCode = $locale['code'];

        if (!$storeId || $storeId == 'default') {
            return;
        }

        foreach ($this->_getConfigAgreements() as $name => $data) {
            if ($data['execute'] == 1) {
                $this->_createAgreement($data, $localeCode, false, $storeId);
            }
        }

        // Set config value to true
        $this->_setup->setConfigData('checkout/options/enable_agreements', '1');
    }

    /**
     * Collect data and create Agreement
     *
     * @param  array    $agreementData Cms page data
     * @param  string   $locale        Locale
     * @param  boolean  $override      Override cms page if it exists
     * @param  int|null $storeId       Store Id
     * @return void
     */
    protected function _createAgreement($agreementData, $locale, $override = true, $storeId = null)
    {
        if (!is_array($agreementData)) {
            return;
        }

        $filename = Mage::getBaseDir('locale') . DS . $locale . DS . 'template' . DS . $agreementData['filename'];
        if (!file_exists($filename)) {
            return;
        }

        $templateContent = $this->getTemplateContent($filename);

        // Find name
        $name = '';
        if (preg_match('/<!--@name\s*(.*?)\s*@-->/u', $templateContent, $matches)) {
            $name = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        // Find checkbox_text
        $checkboxText = '';
        if (preg_match('/<!--@checkbox_text\s*(.*?)\s*@-->/u', $templateContent, $matches)) {
            $checkboxText = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        // Remove comment lines
        $templateContent = preg_replace('#\{\*.*\*\}#suU', '', $templateContent);

        $agreementData = array(
            'name'                    => $name,
            'content'                 => $templateContent,
            'checkbox_text'           => $checkboxText,
            'is_active'               => $agreementData['is_active'],
            'is_html'                 => $agreementData['is_html'],
            'is_required'             => $agreementData['is_required'],
            //'agreement_type'          => $agreementData['agreement_type'],
            //'revocation_product_type' => isset($agreementData['revocation_product_type']) ? $agreementData['revocation_product_type'] : '',
            'stores'                  => $storeId ? $storeId : 0,
        );

        /* @var $agreement Mage_Checkout_Model_Agreement */
        $agreement = Mage::getModel('checkout/agreement')->setStoreId($storeId)->load($agreementData['name'], 'name');
        if (is_array($agreement->getStores()) && !in_array(intval($storeId), $agreement->getStores())) {
            $agreement = Mage::getModel('checkout/agreement');
        }

        if (!(int)$agreement->getId() || $override) {
            $agreement->setData($agreementData)->save();
        }
    }

    /**
     * Get pages/default from config file
     *
     * @return array Config agreements
     */
    protected function _getConfigAgreements()
    {
        return $this->_getConfigNode('agreements', 'default');
    }
}
