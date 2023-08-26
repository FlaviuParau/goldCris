<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */ 
class Amasty_Payrestriction_Model_Rule extends Amasty_Commonrules_Model_Rule
{
    public function _construct()
    {
        $this->_type = 'ampayrestriction';
        parent::_construct();
    }
    
    public function restrict($method)
    {
        $methods = explode(',', $this->getMethods());
        if (in_array($method->getCode(), $methods)) {
            return true;
        } else {
            return false;
        }
    }
}