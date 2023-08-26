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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Model_Layout_Definition_Xml
    extends Blugento_HomepageManager_Model_Layout_Definition_Xml_Abstract
{
    /**
     * Helper class to get info about definition file and variable models
     * Set this in $this->_helper
     * @var Mage_Core_Helper_Abstract 
     */
    protected function _setHelper() 
    {
        $this->_helper = Mage::helper('blugento_homepagemanager');
        return $this;
    }

    /**
     * Set root node in $this->_rootNode
     * @return Blugento_HomepageManager_Model_Layout_Definition_Xml
     */
    protected function _setRootNode() 
    {
        $this->_rootNode = 'nodes';
        return $this;
    }
}
