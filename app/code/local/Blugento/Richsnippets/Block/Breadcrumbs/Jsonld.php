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

class Blugento_Richsnippets_Block_Breadcrumbs_Jsonld extends Blugento_Richsnippets_Block_Abstract
{
	/**
	 * Get Data for Breadcrumbs schema.org
	 *
	 * @return Blugento_Richsnippets_Block_Abstract _richSnippets
	 */
	public function getStructuredData()
	{
		if ($this->_richSnippetHelper->isBreadcrumbsEnabled()) {
			
			/** @var Mage_Catalog_Helper_Data $crumbs */
			$crumbs = Mage::helper('catalog')->getBreadcrumbPath();
			$uri    = $this->getAction()->getRequest()->getRequestUri();
			
			if (strpos($uri, 'blog')) {
				$this->_getBlogBreadcrumbs();
			} elseif (is_array($crumbs) && count($crumbs) > 0) {
				$this->_getCatalogBreadcrumbs($crumbs);
			} else {
				$this->_getPageBreadcrumbs();
			}
		}
		
		return $this->_richSnippets;
	}
	
	/**
	 * Save in _richSnippets array of category|product breadcrumbs
	 *
	 * @param Mage_Catalog_Helper_Data $breadcrumbs
	 */
	protected function _getCatalogBreadcrumbs($breadcrumbs)
	{
		$breadcrumbData = array();
		$position       = 0;
		
		if ($this->getLayout()->getBlock('breadcrumbs')) {
			$breadcrumbData[] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'item'     => array(
					'@id'  => $this->escapeHtml(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true)),
					'name' => $this->_richSnippetHelper->__('Home')
				)
			);
		}
		
		foreach ($breadcrumbs as $key => $breadcrumb) {
			$position++;
			
			$breadcrumbData[] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'item'     => array(
					'@id'  => array_key_exists('link', $breadcrumb) ? $this->escapeHtml($breadcrumb['link']) : Mage::helper('core/url')->getCurrentUrl(),
					'name' => $this->escapeHtml($breadcrumb['label'])
				)
			);
		}
		
		$this->_richSnippets = array(
			'@context'        => 'http://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $breadcrumbData
		);
	}
	
	/**
	 * Save in _richSnippets array of pages breadcrumbs
	 */
	protected function _getPageBreadcrumbs()
	{
		$this->_richSnippets = Mage::getSingleton('customer/session')->getData('breadcrumbs');
		
		Mage::getSingleton('customer/session')->unsetData('breadcrumbs');
	}
	
	/**
	 * Save in _richSnippets array of blog breadcrumbs
	 */
	protected function _getBlogBreadcrumbs() {
		$crumbs     = $this->getLayout()->getBlock('breadcrumbs')->getCrumbs();
		$crumbsData = array();
		$position   = 0;

		if ($crumbs && count($crumbs) > 0) {
			foreach ($crumbs as $crumb) {
				$position++;
	
				$crumbsData[] = array(
					'@type'    => 'ListItem',
					'position' => $position,
					'item'     => array(
						'@id'  => array_key_exists('link', $crumb) && $crumb['link'] !== NULL ? $this->escapeHtml($crumb['link']) : Mage::helper('core/url')->getCurrentUrl(),
						'name' => $this->escapeHtml($crumb['label'])
					)
				);
			}
		}
		
		$this->_richSnippets = array(
			'@context'        => 'http://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $crumbsData
		);
	}
}
