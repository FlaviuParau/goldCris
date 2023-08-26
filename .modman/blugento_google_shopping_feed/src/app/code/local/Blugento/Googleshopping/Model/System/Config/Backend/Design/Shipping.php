<?php

class Blugento_Googleshopping_Model_System_Config_Backend_Design_Shipping
    extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{

    /**
     *
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            if (count($value)) {
                $keys = array();
                for ($i = 0; $i < count($value); $i++) {
                    $keys[] = 'fields_' . uniqid();
                }

                foreach ($value as $key => $field) {
                    $priceFrom = str_replace(',', '.', $field['price_from']);
                    $priceTo = str_replace(',', '.', $field['price_to']);
                    $price = str_replace(',', '.', $field['price']);

                    if (!$priceFrom) {
                        $priceFrom = '0.00';
                    }

                    if (!$priceTo) {
                        $priceTo = '0.00';
                    }

                    if (!$price) {
                        $price = '0.00';
                    }

                    $value[$key]['price_from'] = number_format($priceFrom, 2, '.', '');
                    $value[$key]['price_to'] = number_format($priceTo, 2, '.', '');
                    $value[$key]['price'] = number_format($price, 2, '.', '');
                }

                $value = array_combine($keys, array_values($value));
            }
        }

        $this->setValue($value);
        parent::_beforeSave();
    }

}