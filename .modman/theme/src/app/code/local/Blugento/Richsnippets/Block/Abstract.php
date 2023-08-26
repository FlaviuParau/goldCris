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
 * @package     Blugento_RichSnippets
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Blugento_Richsnippets_Block_Abstract extends Mage_Core_Block_Template
{
	/**
	 * Blugento Rich Snippets Helper Data
	 *
	 * @var Blugento_Richsnippets_Helper_Data _richSnippetHelper
	 */
	protected $_richSnippetHelper = null;
	
	/**
	 * Blugento Rich Snippets Attribute Model
	 *
	 * @var Blugento_Richsnippets_Model_Attributes _richSnippetModel
	 */
	protected $_richSnippetModel = null;
	
	/**
	 * Blugento Rich Snippets array
	 *
	 * @var Blugento_Richsnippets_Block_Abstract _richSnippets
	 */
	protected $_richSnippets;
	
	/**
	 * Blugento_Richsnippets_Block_Abstract constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_richSnippetHelper = Mage::helper('blugento_richsnippets');
		$this->_richSnippetModel  = Mage::getModel('blugento_richsnippets/attributes');
	}
	
	/**
	 * Json Encode Rich Snippet Data
	 *
	 * @param $data
	 * @return string|null
	 */
	public function jsonEncodePretty($data)
	{
		return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * Get Data for schema.org
	 */
	abstract public function getStructuredData();
}
