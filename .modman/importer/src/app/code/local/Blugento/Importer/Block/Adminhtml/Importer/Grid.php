<?php
/**
 *
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
 * @package     Blugento_Importer
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Importer_Block_Adminhtml_Importer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('convertProfileGrid');
        $this->setDefaultSort('profile_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('blugento_importer/profile_collection')
            ->addFieldToFilter('id', array('notnull'=>''));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => '50px',
            'index'     => 'id',
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('adminhtml')->__('Name'),
            'width'     => '50px',
            'index'     => 'name',
        ));
        $this->addColumn('entity_type', array(
            'header'    => Mage::helper('adminhtml')->__('Entity Type'),
            'width'     => '50px',
            'index'     => 'entity_type',
            'type'      => 'options',
            'options'   => array('1'=>'Products'),
        ));
        $this->addColumn('behavior', array(
            'header'    => Mage::helper('adminhtml')->__('Behavior'),
            'width'     => '50px',
            'index'     => 'behavior',
            'type'      => 'options',
            'options'   => array('append'=>'Append', '2'=>'Create'),
        ));
        $this->addColumn('store_id', array(
            'header'    => Mage::helper('adminhtml')->__('Store Id'),
            'width'     => '50px',
            'index'     => 'store_id',
        ));
        $this->addColumn('last_run_time', array(
            'header'    => Mage::helper('adminhtml')->__('Last Run Time'),
            'type'      => 'datetime',
            'width'     => '50px',
            'align'     => 'center',
            'index'     => 'last_run_time',
        ));

//        $this->addColumn('action', array(
//            'header'    => Mage::helper('adminhtml')->__('Action'),
//            'width'     => '50px',
//            'align'     => 'center',
//            'sortable'  => false,
//            'filter'    => false,
//            'type'      => 'action',
//            'actions'   => array(
//                array(
//                    'url'       => $this->getUrl('*/*/edit') . 'id/$profile_id',
//                    'caption'   => Mage::helper('adminhtml')->__('Edit')
//                )
//            )
//        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}
