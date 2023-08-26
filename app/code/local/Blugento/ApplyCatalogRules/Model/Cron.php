<?php
class Blugento_ApplyCatalogRules_Model_Cron
{
	public function applyRulesAtNight()
    {
	    $catalogPriceRule = Mage::getModel('catalogrule/rule');
        $catalogPriceRule->applyAll();
	}
}