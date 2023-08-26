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

class Blugento_Richsnippets_Model_Observer extends Mage_Core_Model_Abstract
{
	/**
	 * Dispatch by:: controller_action_layout_load_before
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Blugento_Richsnippets_Model_Observer $this
	 */
	public function addContactCustomHandle(Varien_Event_Observer $observer)
	{
		/** @var Mage_Core_Model_Layout $layout */
		$layout     = $observer->getEvent()->getLayout();
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		
		if (strpos($currentUrl, 'contact') !== false) {
			$layout->getUpdate()->addHandle('contacts_richsnippet');
		}
		
		return $this;
	}
	
	/**
	 * Dispatch by:: cms_generate_breadcrumbs
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Blugento_Richsnippets_Model_Observer $this
	 */
	public function addPageBreadcrumbs(Varien_Event_Observer $observer)
	{
		$breadcrumbsObject = $observer->getEvent()->getBreadcrumbs();
		$breadcrumbData    = array();
		$position          = 0;
		
		foreach ($breadcrumbsObject->getCrumbs() as $breadcrumbsItem) {
			$breadcrumbData[] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'item'     => array(
					'@id'  => array_key_exists('link', $breadcrumbsItem['crumbInfo']) ? $breadcrumbsItem['crumbInfo']['link'] : Mage::helper('core/url')->getCurrentUrl(),
					'name' => $breadcrumbsItem['crumbInfo']['label']
				)
			);
			
			$position++;
		}
		
		$data = array(
			'@context'        => 'http://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $breadcrumbData
		);
		
		Mage::getSingleton('customer/session')->setData('breadcrumbs', $data);
		
		return $this;
	}
}
