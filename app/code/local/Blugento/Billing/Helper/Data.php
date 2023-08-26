<?php
/**
 * Blugento Billing Attributes
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Billing
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Billing_Helper_Data extends Mage_Core_Helper_Abstract
{
    const HIDDEN_ATTRIBUTE   = '0';
    const OPTIONAL_ATTRIBUTE = '1';
    const REQUIRED_ATTRIBUTE = '2';

    public function getAttributeClass($attribute)
    {
        $config = Mage::getStoreConfig('blugento_billing/fields_config/' . $attribute);

        if (!$config || $config == self::HIDDEN_ATTRIBUTE) {
            return null;
        }

        return ($config == self::REQUIRED_ATTRIBUTE) ? 'required-entry' : ' ';
    }

    public function displayRegionBeforeCity()
    {
        return Mage::getStoreConfig('blugento_billing/global_config/region_before');
    }

    public function displayCountryBeforeRegion()
    {
        return Mage::getStoreConfig('blugento_billing/global_config/country_before');
    }

    public function displayTelephoneAfterEmail()
    {
        return Mage::getStoreConfig('blugento_billing/global_config/telephone_after_mail');
    }

    public function bothFormsEnabled() {
        if ($this->individualFormEnabled() && $this->legalEntityFormEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    public function bothFormDisabled(){
        if (!$this->individualFormEnabled() && !$this->legalEntityFormEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    public function individualFormEnabled() {
        return $personalForm = Mage::getStoreConfig('blugento_billing/global_config/individual_form');
    }

    public function legalEntityFormEnabled() {
        return $personalForm = Mage::getStoreConfig('blugento_billing/global_config/legal_entity_form');
    }

    public function checkPjAttribute() {
        if ($this->bothFormsEnabled() || !$this->legalEntityFormEnabled()) {
            return false;
        }

        if ($this->legalEntityFormEnabled()) {
            return true;
        }
    }

    public function checkPfAttribute() {
        if ($this->bothFormsEnabled() || !$this->individualFormEnabled()) {
            return false;
        }

        if ($this->individualFormEnabled()) {
            return true;
        }
    }

    public function isPostCodeOptional($countries) {

        $countryCodes = [];
        foreach ($countries as $country) {
            array_push($countryCodes, $country['value']);
        }

        $difference = array_diff($countryCodes, Mage::helper('directory')->getCountriesWithOptionalZip());

        if (count($difference) > 1 || $this->getAttributeClass('postal_code')) {
            return true;
        }

        return false;
    }
	
	public function enableVatValidation()
	{
		return Mage::getStoreConfig('blugento_billing/fields_config/validate_vat');
	}
	
	public function enableNameValidation()
	{
		return Mage::getStoreConfig('blugento_billing/fields_config/validate_name');
	}
}
