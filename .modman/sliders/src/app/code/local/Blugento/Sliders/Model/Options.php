<?php
/**
 * Blugento Sliders
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Model_Options
{
    /**
     * Provide available options as a value/label array
     *
     * @return array
     */
    public function toOptionArray() {

        $groups = Mage::getModel('blugento_sliders/group')->getCollection();

        $arrGroups = array();
        foreach ($groups as $group) {
            $arrGroups[] = array('value' => $group->getCode(), 'label' => $group->getTitle());
        }

        return $arrGroups;
    }
}
