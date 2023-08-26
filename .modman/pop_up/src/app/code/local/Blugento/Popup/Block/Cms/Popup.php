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

class Blugento_Popup_Block_Cms_Popup extends Mage_Core_Block_Template
{
    protected $_popups;
    protected $_page;
    protected $_categories = [];

    public function __construct() {
        if (!Mage::getSingleton('cms/page')->getIdentifier()) {
            $this->_page = $this->getRequest()->getControllerName();

            // set success page to distinguish it from checkout page because both have same controller name: onepage
            if ($this->_page == 'onepage' && $this->getRequest()->getActionName() == 'success') {
                $this->_page = 'success';
            }
        } else {
            $this->_page = Mage::getSingleton('cms/page')->getIdentifier();
        }

        if ($this->_page == 'category' && $this->getRequest()->getRouteName() != 'ideabook') {
            $this->_categories[] = Mage::registry('current_category')->getId();
        }

        if ($this->_page == 'product') {
            $this->_categories = Mage::registry('current_product')->getCategoryIds();
        }
        
        $this->setPopups();

        parent::__construct();
    }

    /**
     * Return all popups
     *
     * @return array|null
     */
    public function getPopups()
    {
        if (count($this->_popups)) {
            return $this->_popups;
        } else {
            return null;
        }
    }

    /**
     * Get popup content html
     *
     * @param int $popupId
     * @return string
     */
    public function getPopupContent($popupId) {
        return $this->getLayout()->createBlock('cms/block')->setBlockId($this->_popups[$popupId]['content'])->toHtml();
    }

    /**
     * Set popup
     */
    public function setPopups() {
        $this->_popups = Mage::getModel('blugento_popup/popup')->getPopupsForPage($this->_page, Mage::app()->getStore()->getId(), $this->_categories);
    }

    /**
     * Return popup identifier
     *
     * @param int $popupId
     * @return mixed
     */
    public function getPopupIdentifier($popupId) {
        return str_replace(' ', '_', strtolower($this->_popups[$popupId]['title']));
    }
}