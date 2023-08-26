<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Commonrules
 */
abstract class Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Rule Object
     *
     * @var Mage_Rule_Model_Abstract
     */
    protected $_rule;
    
    /**
     * @return Mage_Rule_Model_Abstract
     */
    protected function _getRule()
    {
        return $this->_rule;
    }

    /**
     * @param Mage_Rule_Model_Abstract $rule
     * @return $this
     */
    protected function _setRule($rule)
    {
        $this->_rule = $rule;
        return $this;
    }
}