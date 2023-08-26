<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

/**
 * @author Amasty
 */ 
class Amasty_Payrestriction_Model_Mysql4_Rule_Collection extends Amasty_Commonrules_Model_Resource_Rule_Collection
{
    public function _construct()
    {
        $this->_init('ampayrestriction/rule');
    }
}