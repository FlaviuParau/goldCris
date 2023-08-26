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

class Blugento_Richsnippets_Block_Blog_Jsonld extends Blugento_Richsnippets_Block_Abstract
{
	/**
	 * Blugento Rich Snippets Current Blog Article
	 *
	 * @var Mage_Catalog_Model_Product _product
	 */
	protected $_product;
	
	/**
	 * Get Data for Blog Article schema.org
	 *
	 * @return Blugento_Richsnippets_Block_Abstract _richSnippets
	 */
	public function getStructuredData()
	{
		if ($this->_richSnippetHelper->isBlogRichSnippetEnabled()) {
			$this->_blogData();
		}
		
		return $this->_richSnippets;
	}
	
	/**
	 * Save in _richSnippets data for Blog Article
	 */
	protected function _blogData()
	{
		$post = Mage::getSingleton('blog/post');
		if ($post) {
			$this->_richSnippets = array(
				'@context'          => 'http://schema.org',
				'@type'             => 'NewsArticle',
				'mainEntityOfPage'  => array(
					'@type'         => 'WebPage',
					'@id'           => $post->getPostId(),
				),
				'headline'          => $post->getTitle(),
				'image'             => $post->getFeaturedImage() ? Mage::getBaseUrl('media') . '/blogpic/' . $post->getFeaturedImage() : '',
				'datePublished'     => $post->getCreatedTime(),
				'dateModified'      => $post->getUpdateTime(),
				'author'            => array(
					'@type'         => 'Person',
					'name'          => $post->getUser(),
				),
				'publisher'         => array(
					'@type'         => 'Organization',
					'name'          => $post->getUser(),
				),
				'description'       => $post->getPostContent(),
			);
		} else {
			$this->_richSnippets = array();
		}
	}
}
