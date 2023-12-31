<?php
/**
 * Blugento Sliders
 * Grid Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('blugento_sliders_banner_grid');
        $this->setDefaultSort('title');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Insert the Add New button
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Add New Banner'),
                'class'     => 'add',
                'onclick'   => "setLocation('" . $this->getUrl('*/blugento_sliders_banner/new') . "');",
            ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve the main buttons html
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        return parent::getMainButtonsHtml() . $this->getChildHtml('add_button');
    }

    /**
     * Initialise and set the collection for the grid
     *
     */
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getResourceModel('blugento_sliders/banner_collection'));

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

        $this->addColumn('group_id', array(
            'header'    => $this->__('Group'),
            'align'     => 'left',
            'index'     => 'group_id',
            'type'      => 'options',
            'options'   => $this->_getGroups()
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

        $this->addColumn('action', array(
            'width'     => '50px',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('catalog')->__('Edit'),
                    'url'       => array(
                        'base'  =>'*/blugento_sliders_banner/edit'
                    ),
                'field' => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'align'     => 'center',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get the current URL for the grid
     *
     * @return string
     */
    public function getCurrentUrl($params = array())
    {
        return $this->getUrl('*/*/pageGrid');
    }

    /**
     * Retrieve the URL for the row
     *
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/blugento_sliders_banner/edit', array('id' => $row->getId()));
    }

    /**
     * Retrieve an array of all of the stores
     *
     * @return array
     */
    protected function _getGroups()
    {
        $groups = Mage::getResourceModel('blugento_sliders/group_collection');
        $options = array();

        foreach($groups as $group) {
            $options[$group->getId()] = $group->getTitle();
        }

        return $options;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('banner');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'     => $this->__('Delete'),
            'url'       => $this->getUrl('*/blugento_sliders_banner/massDelete'),
            'confirm'   => Mage::helper('catalog')->__('Are you sure?')
        ));
    }
}