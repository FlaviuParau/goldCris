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

class Blugento_Localizer_Model_Adminhtml_System_Config_Source_Country
{
    protected $_options;

    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
        }

        $options = $this->_options;

        $data_ = $this->_getAvailableCountries();
        $availableCountries = $data_[0];
        $availableCountriesAssoc = $data_[1];

        foreach ($options as $key => $val) {
            if (!in_array(strtoupper($val['value']), $availableCountries)) {
                unset($options[$key]);
            } else {
                $options[$key]['value'] = $availableCountriesAssoc[$val['value']];
            }
        }

        if (!$isMultiselect) {
            array_unshift($options, array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please select --')));
        }

        return $options;
    }

    /**
     * Get available countries codes
     *
     * @return array
     */
    protected function _getAvailableCountries()
    {
        $config = Mage::getConfig();
        $filePath = $config->getModuleDir('etc', 'Blugento_Localizer') . DS . 'resource.xml';

        if (!is_readable($filePath)) {
            return array('ro_RO', 'de_DE', 'en_US');
        }

        $xmlObj = new Varien_Simplexml_Config($filePath);
        $xmlData = $xmlObj->getNode();

        $countries = $xmlData->country->asArray();
        $data = array();
        $data_assoc = array();

        foreach ($countries as $country => $values) {
            $key = @strtoupper(array_pop(explode('_', $country)));
            $data[] = $key;
            $data_assoc[$key] = $country;
        }

        return array(
            $data, $data_assoc
        );
    }
}
