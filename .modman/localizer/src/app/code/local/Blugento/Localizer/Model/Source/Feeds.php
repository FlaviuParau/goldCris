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
class Blugento_Localizer_Model_Source_Feeds
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
            $feeds = Mage::getStoreConfig('blugento_feeds/feeds');
            foreach ($feeds as $feedId => $value) {
                $this->_options[] = array(
                    'value' => $feedId,
                    'label' => $feedId,
                );
            }
        }

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
