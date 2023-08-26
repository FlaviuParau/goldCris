<?php
/**
 * Blugento Sliders
 * Tab Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Adminhtml_Group_Edit_Tab_Banners extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('banner_grid');
        $this->setDefaultSort('title');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Initialise and set the collection for the grid
     *
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('blugento_sliders/banner_collection')->addGroupIdFilter($this->getGroupId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Add the columns to the grid
     *
     */
    protected function _prepareColumns()
    {
        $this->addColumn('banner_id', array(
            'header'    => $this->__('ID'),
            'align'     => 'left',
            'width'     => '60px',
            'index'     => 'banner_id'
        ));

        $this->addColumn('title', array(
            'header'    => $this->__('Title'),
            'align'     => 'left',
            'index'     => 'title'
        ));

        $this->addColumn('is_enabled', array(
            'header'    => $this->__('Enabled'),
            'width'     => '90px',
            'index'     => 'is_enabled',
            'type'      => 'options',
            'options'   => array(
                1   => $this->__('Enabled'),
                0   => $this->__('Disabled')
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve the URL used to modify the grid via AJAX
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/banner', array('_current' => true));
    }

    /**
     * Retrieve the URL for the row
     *
     */
    public function getRowUrl($row)
    {
        // return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Retrieve the group ID
     *
     * @return int
     */
    public function getGroupId()
    {
        return Mage::registry('blugento-sliders-group') ? Mage::registry('blugento-sliders-group')->getId() : 0;
    }
}
