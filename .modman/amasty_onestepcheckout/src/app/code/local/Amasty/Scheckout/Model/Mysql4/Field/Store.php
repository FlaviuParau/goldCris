<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */
class Amasty_Scheckout_Model_Mysql4_Field_Store extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amscheckout/field_store', 'field_store_id');
    }
}