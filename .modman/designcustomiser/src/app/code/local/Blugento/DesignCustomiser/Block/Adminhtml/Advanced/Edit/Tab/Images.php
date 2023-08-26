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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Block_Adminhtml_Advanced_Edit_Tab_Images
    extends Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Variable_Abstract
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('blugento_designcustomiser')->__('Images');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blugento_designcustomiser')->__('Images');
    }
    
    /**
     * Set collection
     * @return Blugento_DesignCustomiser_Block_Adminhtml_Design_Edit_Tab_Variable_Abstract
     */
    protected function _setCollection() 
    {
        $this->_collection = Mage::getSingleton('blugento_designcustomiser/scss_variable_image_collection');
        return $this;
    }
}
