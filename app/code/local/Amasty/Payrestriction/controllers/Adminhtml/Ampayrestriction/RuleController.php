<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */
require_once Mage::getModuleDir('controllers', 'Amasty_Commonrules').DS.'Adminhtml'.DS.'Amcommonrules'.DS.'RuleController.php';

class Amasty_Payrestriction_Adminhtml_Ampayrestriction_RuleController extends Amasty_Commonrules_Adminhtml_Amcommonrules_RuleController
{
    protected function _construct()
    {
        parent::_construct();
        $this->_title       = 'Payment Restriction';
        $this->_namespace   = 'ampayrestriction';
    }
}