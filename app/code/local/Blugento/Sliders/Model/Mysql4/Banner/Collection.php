<?php
/**
 * Blugento Sliders
 * Collection Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Model_Mysql4_Banner_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('blugento_sliders/banner');
    }

    /**
     * Init collection select
     *
     * @return Blugento_Sliders_Model_Mysql4_Banner_Collection
    */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getMainTable()));

        return $this;
    }

    /**
     * Filter the collection by a group ID
     *
     * @param int $groupId
     * @return Blugento_Sliders_Model_Mysql4_Banner_Collection
     */
    public function addGroupIdFilter($groupId)
    {
        return $this->addFieldToFilter('group_id', $groupId);
    }

    /**
     * Filter the collection by enabled banners
     *
     * @param bool $isEnabled = true
     * @return Blugento_Sliders_Model_Mysql4_Banner_Collection
     */
    public function addIsEnabledFilter($isEnabled = true)
    {
        return $this->addFieldToFilter('is_enabled', $isEnabled ? 1 : 0);
    }

    /**
     * Add a random order to the banners
     *
     * @param string $dir
     * @return Blugento_Sliders_Model_Mysql4_Banner_Collection
    */
    public function addOrderByRandom($dir = 'ASC')
    {
        $this->getSelect()->order('RAND() ' . $dir);
        return $this;
    }

    /**
     * Add order by sort order
     *
     * @param string $dir
     * @return Blugento_Sliders_Model_Mysql4_Banner_Collection
    */
    public function addOrderBySortOrder($dir = 'ASC')
    {
        $this->getSelect()->order('sort_order ' . $dir);
        return $this;
    }
	
	/**
	 * Returns pairs banner_id - title
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('banner_id', 'title');
	}
}
