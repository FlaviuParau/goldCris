<?php

class Blugento_GdprCookies_Block_Adminhtml_Cookies_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cookie_list_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('gdprcookies/list')->getCollection();
        $collection->setOrder('id', 'DESC');
        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('adminhtml')->__('Cookie ID'),
            'index'     => 'id',
            'width'     => '150px',
        ));

        $this->addColumn('cookie_name', array(
            'header'    => Mage::helper('adminhtml')->__('Cookie Name'),
            'index'     => 'cookie_name',
        ));

        $this->addColumn('cookie_category', array(
            'header'    => Mage::helper('adminhtml')->__('Cookie Category'),
            'index'     => 'cookie_category',
            'renderer'   => 'gdprcookies/adminhtml_cookies_list_grid_column_renderer_preparationLabel',
        ));

        $this->addColumn('cookie_description', array(
            'header'    => Mage::helper('adminhtml')->__('Cookie Description'),
            'index'     => 'cookie_description',
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('adminhtml')->__('Updated At'),
            'index'     => 'updated_at',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}