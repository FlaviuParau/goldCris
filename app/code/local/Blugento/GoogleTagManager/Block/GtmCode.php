<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_GoogleTagManager
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GoogleTagManager_Block_GtmCode extends Mage_Core_Block_Template
{
	/**
	 * Google Tag Manager data
	 *
	 * @var Blugento_GoogleTagManager_Helper_Data _gtmHelper
	 */
	protected $_gtmHelper = null;
	
	/**
	 * Cookie Helper
	 *
	 * @var Mage_Core_Helper_Cookie
	 */
	protected $_cookieHelper = null;
	
	
	/**
	 * Blugento_GoogleTagManager_Block_GtmCode constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_cookieHelper = Mage::helper('core/cookie');
		$this->_gtmHelper    = Mage::helper('blugento_googletagmanager');
	}

	/**
	 * Is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->_gtmHelper->isEnabled();
	}
	
	/**
	 * Get Account Id
	 *
	 * @return string
	 */
	public function getAccountId()
	{
		return $this->_gtmHelper->getAccountId();
	}

	/**
	 * Get script cateegory
	 *
	 * @return string
	 */
	public function getCategory()
	{
		return $this->_gtmHelper->getCategory();
	}

	/**
	 * Render tag manager JS
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		return parent::_toHtml();
	}
}
