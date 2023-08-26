<?php
/**
 * Blugento Cart Settings
 * Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Cart
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Cart_Block_Form extends Mage_Core_Block_Template
{
    protected $_loadedProduct = null;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('blugento/cart/form.phtml');
	    if (!$this->_loadedProduct) {
		    try {
		    	$this->_loadedProduct = Mage::getModel('catalog/product')->load($this->_getProductId());
		    } catch (Exception $e) {
			    Mage::logException($e);
			    return '-';
		    }
	    }
    }

    public function getFormAction()
    {
        return $this->getUrl('product-inquiry');
    }

    public function getInquiryProduct()
    {
    	$title = $this->_loadedProduct->getName();
    	
    	if ($this->_getProductAttribute() != '') {
    	    $title .= ' (' . $this->_loadedProduct->getResource()->getAttribute($this->_getProductAttribute())->getFrontendLabel() . ': ';
    	    
    	    if ($this->_loadedProduct->getAttributeText($this->_getProductAttribute()) != '') {
		        $title .= $this->_loadedProduct->getAttributeText($this->_getProductAttribute()) . ')';
	        } else {
		        $title .= $this->_loadedProduct->getData($this->_getProductAttribute()) . ')';
	        }
	    } else {
    		$title .= ' (SKU: ' . $this->_loadedProduct->getSku() . ')';
	    }
    	
    	return $title;
    }

    public function getInquiryProductID()
    {
    	return $this->_loadedProduct->getId();
    }

    public function getUrlProduct()
    {
    	return $this->_loadedProduct->getProductUrl();
    }
	
	public function getSuccessPage($productId)
	{
		return Mage::helper('blugento_cart')->getSuccessPageURL($productId);
	}
	
	protected function _getProductId()
	{
		return Mage::app()->getRequest()->getParam('product');
	}
	
	protected function _getProductAttribute()
	{
		return Mage::getStoreConfig('blugento_cart/global_config/attributes');
	}
}
