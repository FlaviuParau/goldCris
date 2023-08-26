<?php
/**
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
 * @package     Blugento_Category
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Category_Block_Html_Topmenu_Renderer extends Mage_Page_Block_Html_Topmenu_Renderer
{
    /**
     * Get Megamenu image url
     *
     * @param Varien_Data_Tree_Node $child
     * @return string
     */
    public function getMegamenuImageUrl(Varien_Data_Tree_Node $child)
    {
    	return $this->helper('blugento_category')->getImageUrl($this->_getCategory($child), 'blugento_megamenu_image');
    }
	
	/**
	 * Get Custom Left CMS Block Content
	 *
	 * @param Varien_Data_Tree_Node $child
	 * @return string
	 */
	public function getLeftBlockContent(Varien_Data_Tree_Node $child)
	{
		return $this->_getCategory($child)->getBlugentoCatLeftCmsBlock();
	}
	
	/**
	 * Get Custom Right CMS Block Content
	 *
	 * @param Varien_Data_Tree_Node $child
	 * @return string
	 */
	public function getRightBlockContent(Varien_Data_Tree_Node $child)
	{
		return $this->_getCategory($child)->getBlugentoCatRightCmsBlock();
	}

    /**
     * Get category id
     *
     * @param $nodeId
     * @return int
     */
    private function _getCategoryId($nodeId)
    {
        $nodeIdChunks = explode('-', $nodeId);
        return intval(array_pop($nodeIdChunks));
    }
	
	/**
	 * Load category by ID
	 *
	 * @param Varien_Data_Tree_Node $child
	 * @return Mage_Catalog_Model_Category $category
	 */
	private function _getCategory($child)
	{
		$categoryId = $this->_getCategoryId($child->getId());
		/** @var Mage_Catalog_Model_Category $category */
		$category   = Mage::getModel('catalog/category')->load($categoryId);
		
		return $category;
	}
}
