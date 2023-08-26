<?php
/**
 * Blugento Sliders
 * Source Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Model_System_Config_Source_Controls_Position
{
    /**
     * Retrieve an array of possible options
     *
     * @param bool $includeEmpty
     * @param string $emptyText
     * @return array
     */
    public function toOptionArray($includeEmpty = false, $emptyText = '-- Please Select --')
    {
        $options = array();

        if ($includeEmpty) {
            $options[] = array(
                'value' => '',
                'label' => Mage::helper('adminhtml')->__($emptyText)
            );
        }

        foreach($this->getOptions() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => Mage::helper('adminhtml')->__($label)
            );
        }

        return $options;
    }

    /**
     * Retrieve an array of possible options
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            ''                  => 'None',
            'controls-left'     => 'Left',
            'controls-middle'   => 'Middle',
            'controls-right'    => 'Right'
        );
    }
}
