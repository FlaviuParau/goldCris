<?php
/**
 * Blugento Feeds
 * Source model for configuration dropdown of description options
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Feeds
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Model_Source_Shopmania_Bundleprice
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var array $_options cached options
     */
    protected $_options;

    /**
     * Return option array
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array (
                array(
                    'value' => 'min',
                    'label' => 'Minimum price',
                ),
                array(
                    'value' => 'default',
                    'label' => 'Default configuration price',
                )
            );
        }

        return $this->_options;
    }

    /**
     * Get all options as array
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
