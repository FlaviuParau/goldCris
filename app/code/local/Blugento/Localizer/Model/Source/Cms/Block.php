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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CMS Source model for configuration dropdown of CMS static blocks
 *
 */
class Blugento_Localizer_Model_Source_Cms_Block
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var array $_options cached options
     */
    protected $_options;

    /**
     * Return option array
     *
     * @return array Blocks as option array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            /** @var $blocks Mage_Cms_Model_Resource_Block_Collection */
            $blocks = Mage::getModel('cms/block')->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->setOrder('identifier', 'ASC');

            $options = array();

            foreach ($blocks as $block) {
                /** @var $block Mage_Cms_Model_Block */
                $options[$block->getIdentifier()] = $block->getIdentifier();
            }

            foreach ($options as $identifier) {
                $this->_options[] = array(
                    'value' => $identifier,
                    'label' => $identifier,
                );
            }
        }

        array_unshift($this->_options, array('value' => '', 'label' => Mage::helper('blugento_localizer')->__('No Block')));

        return $this->_options;
    }

    /**
     * Get all options as array
     *
     * @return array Blocks as option array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
