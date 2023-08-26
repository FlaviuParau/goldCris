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

class Blugento_Richsnippets_Block_Website_Jsonld extends Blugento_Richsnippets_Block_Abstract
{
	/**
	 * Get Data for Website schema.org
	 *
	 * @return Blugento_Richsnippets_Block_Abstract _richSnippets
	 */
	public function getStructuredData()
	{
		if ($this->_richSnippetHelper->isWebsiteRichSnippetEnabled()) {
			$this->_websiteData();
		}

		return $this->_richSnippets;
	}

	/**
	 * Save in _richSnippets data for Website
	 */
	protected function _websiteData()
	{
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);
		
		if (strpos($url, 'www') !== false) {
			$url = substr($url, 12, -1);
		} else {
			$url = substr($url, 9, -1);
		}
		
		$this->_richSnippets = array(
			'@context'        => 'http://schema.org',
			'@type'           => 'WebSite',
			'url'             => $url,
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => 'https://query.' . $url . '/search?q={search_term_string}',
				'query-input' => 'required name=search_term_string'
			)
		);
	}
}
