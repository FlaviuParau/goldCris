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

class Blugento_Popup_Model_System_Config_Source_Cms_Block extends Mage_Core_Model_Abstract
{
    protected $_options;

    public function getAllOptions()
    {
        if (is_null($this->_options)){
            $this->_options = array();
            $collection = Mage::getModel('cms/block')->getCollection();
            foreach ($collection as $block) {
                $this->_options[] = array('label'=> $block->getTitle(), 'value' => $block->getIdentifier());
            }
        }
        $options = $this->_options;

        array_unshift($options, array('value' => '', 'label' => ''));

        return $options;
    }
}