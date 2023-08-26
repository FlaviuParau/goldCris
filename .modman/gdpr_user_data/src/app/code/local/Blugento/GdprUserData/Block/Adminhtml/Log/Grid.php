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
 * @package     Blugento_GdprUserData
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GdprUserData_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_gdpruserdata');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blugento_gdpruserdata/request')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->__('Request ID'),
                'align'  => 'center',
                'index'  => 'id',
                'width'  => 20
            )
        );
        $this->addColumn(
            'type',
            array(
                'header'  => $this->__('Type'),
                'width'   => '75',
                'align'   => 'left',
                'index'   => 'type',
                'type'    => 'options',
                'options' => Mage::getSingleton('blugento_gdpruserdata/config_type')->toOptionArray()
            )
        );
        $this->addColumn(
            'customer_email',
            array(
                'header' => $this->__('Customer Email'),
                'align'  => 'left',
                'index'  => 'customer_email'
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Created Date'),
                'width'  => '150',
                'align'  => 'left',
                'index'  => 'created_at'
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'  => $this->__('Status'),
                'width'   => '150',
                'align'   => 'left',
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getSingleton('blugento_gdpruserdata/config_status')->toOptionArray()
            )
        );
        $this->addColumn(
            'admin_confirmation',
            array(
                'header'  => $this->__('Deletion Confirm'),
                'width'   => '150',
                'align'   => 'left',
                'index'   => 'admin_confirmation',
                'type'    => 'options',
                'options' => Mage::getSingleton('blugento_gdpruserdata/config_confirmation')->toOptionArray()
            )
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        if ($row->getType() == 'delete') {
            return $this->getUrl('*/*/editDeleteRequest', array('id' => $row->getId()));
        } elseif ($row->getType() == 'export') {
            return $this->getUrl('*/*/viewExportRequest', array('id' => $row->getId()));
        }
    }
}