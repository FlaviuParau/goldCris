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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Block_Adminhtml_Label_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_productlabels');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blugento_productlabels/label')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->__('ID'),
                'align'  => 'center',
                'index'  => 'id',
                'width'  => 20
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'  => $this->__('Label Name'),
                'width'   => '150',
                'align'   => 'left',
                'index'   => 'name'
            )
        );
        $this->addColumn(
            'type',
            array(
                'header'  => $this->__('Type'),
                'width'   => '100',
                'align'   => 'left',
                'index'   => 'type',
                'type'    => 'options',
                'options' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_type')->getOptionArray()
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'  => $this->__('Status'),
                'width'   => '100',
                'align'   => 'left',
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getSingleton('blugento_productlabels/adminhtml_system_config_source_label_status')->getOptionArray()
            )
        );
        $this->addColumn(
            'path',
            array(
                'header' => $this->__('Label Model'),
                'width'   => '200',
                'align'  => 'center',
                'index'  => 'path',
                'renderer'  => 'blugento_productlabels/adminhtml_label_grid_renderer_image',
            )
        );
        $this->addColumn(
            'stores',
            array(
                'header' => $this->__('Store Views'),
                'width'  => '150',
                'align'  => 'left',
                'index'  => 'stores',
                'renderer'  => 'blugento_productlabels/adminhtml_label_grid_renderer_stores',
            )
        );

        return parent::_prepareColumns();
    }
//
//    public function getGridUrl()
//    {
//        return $this->getUrl('*/*/grid', array('_current'=>true));
//    }
//
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'name' => $row->getName()));
    }
}