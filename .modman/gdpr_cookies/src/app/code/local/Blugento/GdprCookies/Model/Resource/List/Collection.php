<?php

class Blugento_GdprCookies_Model_Resource_List_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('gdprcookies/list');
    }
}