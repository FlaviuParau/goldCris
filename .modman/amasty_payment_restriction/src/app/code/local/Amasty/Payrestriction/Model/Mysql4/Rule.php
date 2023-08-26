<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */ 
 class Amasty_Payrestriction_Model_Mysql4_Rule extends Amasty_Commonrules_Model_Resource_Rule
{
    public function _construct()
    {
        $this->_type = 'ampayrestriction';
        parent::_construct();
    }
}