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
 * @package     Blugento_Popup
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Popup_Model_Popup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_popup/popup');
    }

    /**
     * Unserialize database fields
     *
     * @return Blugento_Popup_Model_Popup
     */
    protected function _afterLoad()
    {
        if ($this->getData('pages') !== null && is_string($this->getData('pages'))) {
            $this->setData('pages', explode(',', $this->getData('pages')));
        }
	    if ($this->getData('stores') !== null && is_string($this->getData('stores'))) {
		    $this->setData('stores', explode(',', $this->getData('stores')));
	    }
        if ($this->getData('category_pages') !== null && is_string($this->getData('category_pages'))) {
            $this->setData('category_pages', explode(',', $this->getData('category_pages')));
        }
        return parent::_afterLoad();
    }

    /**
     * Serialize fields for database storage
     *
     * @return Blugento_Popup_Model_Popup
     */
    protected function _beforeSave()
    {
        if ($this->getData('pages') !== null && is_array($this->getData('pages'))) {
            $this->setData('pages', implode(',', $this->getData('pages')));
        }
	    if ($this->getData('stores') !== null && is_array($this->getData('stores'))) {
		    $this->setData('stores', implode(',', $this->getData('stores')));
	    }
        if ($this->getData('category_pages') !== null && is_array($this->getData('category_pages'))) {
            $this->setData('category_pages', implode(',', $this->getData('category_pages')));
        }
        return parent::_beforeSave();
    }

    /**
    * Get popup for current page
    *
    * @param string $identifier
    * @param int $storeId
    * @param int|array $categories
    * @return array
    */
    public function getPopupsForPage($identifier, $storeId, $categories)
    {
        $popups = [];

        try {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $sql = 'SELECT * FROM `blugento_popup`
                    WHERE `pages` LIKE "%' . $identifier . '%"
                    ORDER BY `sort_order` ASC';
            $result = $readConnection->fetchAll($sql);

            foreach ($result as $item) {
                $pages = explode(',', $item['pages']);
                $stores = explode(',', $item['stores']);
                if (in_array($identifier, $pages) && $item['status'] == 1 && in_array($storeId, $stores)) {
                    if ($categories && (in_array('category', $pages) || in_array('product', $pages))) {
                        $categoryPages = explode(',', $item['category_pages']);
                        if (in_array('all', $categoryPages)) {
                            $popups[$item['id']] = $item;
                        } else {
                            foreach ($categories as $category) {
                                if (!$popups && in_array($category, $categoryPages)) {
                                    $popups[$item['id']] = $item;
                                }
                            }
                        }
                    } else {
                        $popups[$item['id']] = $item;
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $popups;
    }
}