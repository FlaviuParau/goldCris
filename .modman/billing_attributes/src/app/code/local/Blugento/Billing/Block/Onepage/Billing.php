<?php
class Blugento_Billing_Block_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
{
    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $this->getCustomerTitle($address->getCompany(), $address->getVatId()) . $address->format('oneline')
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    public function getCustomerTitle($company, $vatId)
    {
        $title = '';

        $individual  = Mage::getStoreConfig('blugento_billing/global_config/individual_title');
        $legalEntity = Mage::getStoreConfig('blugento_billing/global_config/legal_entity_title');

        if ($company && $vatId && $legalEntity) {
            $title = $legalEntity . ' ';
        } elseif ($individual) {
            $title = $individual . ' ';
        }

        return $title;
    }

    public function checkMultipleCountries() {
        $numberOfCountries = count($this->getCountryOptions());

        if ($numberOfCountries > 2) {
            return true;
        } else {
            return false;
        }
    }

    public function displayPostCode() {

        return Mage::helper('blugento_billing')->isPostCodeOptional($this->getCountryOptions());
    }
}