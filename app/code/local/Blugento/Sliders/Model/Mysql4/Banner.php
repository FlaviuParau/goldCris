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

class Blugento_Sliders_Model_Mysql4_Banner extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('blugento_sliders/banner', 'banner_id');
    }

    /**
     * Logic performed before saving the model
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Blugento_Sliders_Model_Mysql4_Banner
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getGroupId()) {
            $object->setGroupId(null);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Retrieve the group model associated with the banner
     *
     * @param Blugento_Sliders_Model_Banner $banner
     * @return Blugento_Sliders_Model_Group
     */
    public function getGroup(Blugento_Sliders_Model_Banner $banner)
    {
        if ($banner->getGroupId()) {
            $group = Mage::getModel('blugento_sliders/group')->load($banner->getGroupId());

            if ($group->getId()) {
                return $group;
            }
        }

        return false;
    }
}
