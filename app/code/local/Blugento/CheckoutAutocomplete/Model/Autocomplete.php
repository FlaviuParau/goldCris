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
 * @package     Blugento_CheckoutAutocomplete
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_CheckoutAutocomplete_Model_Autocomplete extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_cautocomplete/autocomplete');
    }

    /**
     * Return all required data from database
     *
     * @param string $search
     * @param string $countryCode
     * @return array
     */
    public function getSearchData($search, $countryCode)
    {
        $query = 'SELECT city, region, region_id, zipcode
                  FROM blugento_cautocomplete_city 
                  WHERE (city LIKE "' . $search . '%" 
                  OR region LIKE "' . $search . '%" 
                  OR zipcode LIKE "' . $search . '%")
                  AND country_code LIKE "' . $countryCode . '"
                  ORDER BY priority';

        $data = array();
        try {
            $data = $this->_getConnection()->fetchAll($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $data;
    }

    /**
     * Retrieve connection
     *
     * @param null|string $type
     * @return mixed
     */
    private function _getConnection($type = null)
    {
        if ($type == 'write') {
            return $this->_getResourceConnection()->getConnection('core_write');
        } else {
            return $this->_getResourceConnection()->getConnection('core_read');
        }
    }

    /**
     * Get the resource model
     *
     * @return Mage_Core_Model_Abstract
     */
    private function _getResourceConnection()
    {
        return Mage::getSingleton('core/resource');
    }
}