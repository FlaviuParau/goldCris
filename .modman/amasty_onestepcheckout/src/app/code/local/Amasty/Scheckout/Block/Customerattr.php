<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Block_Customerattr extends Mage_Core_Block_Template
{
    /**
     * @var string;
     */
    private $typeAddress;

    /**
     * @var array;
     */
    private $relations;

    protected function _construct()
    {
        parent::_construct();
        $this->relations = Mage::helper('amcustomerattr')->getElementsRelation();
    }

    protected function _toHtml()
    {
        $this->setTemplate('amasty/amscheckout/onepage/customerattr_relations.phtml');
        return parent::_toHtml();
    }

    /**
     * Set address type
     * @return ${object};
     */
    public function setTypeAddress($typeAddress)
    {
        $this->typeAddress = $typeAddress . ':';
        return $this;
    }

    /**
     * Get address type
     * @return string;
     */
    public function getTypeAddress()
    {
        return $this->typeAddress;
    }

    /**
     * Get data relations in JSON
     * @return string;
     */
    public function getRelationJSONData()
    {
        return Mage::helper('core')->jsonEncode($this->relations->toArray());
    }

    /**
     * @return boolean;
     */
    public function hasCustomerAtrtibutesRelations()
    {
        return ($this->relations->getSize()) ? true : false;
    }
}