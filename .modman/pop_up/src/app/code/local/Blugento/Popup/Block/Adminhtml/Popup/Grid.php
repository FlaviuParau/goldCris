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
 * @package     Blugento_Popup
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Popup_Block_Adminhtml_Popup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_popup');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blugento_popup/popup')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->__('Popup ID'),
                'align' => 'center',
                'index' => 'id',
                'width' => 20
            )
        );
        $this->addColumn(
            'title',
            array(
                'header' => $this->__('Title'),
                'align' => 'left',
                'index' => 'title'
            )
        );
        $this->addColumn(
            'content',
            array(
                'header' => $this->__('Content (Static Block)'),
                'align' => 'left',
                'index' => 'content'
            )
        );
        $this->addColumn(
            'sort_order',
            array(
                'header' => $this->__('Sort Order'),
                'align' => 'center',
                'index' => 'sort_order',
                'width' => 100,
            )
        );
        $this->addColumn(
            'status',
            array(
                'header' => $this->__('Status'),
                'align' => 'left',
                'index' => 'status',
                'width' => 100,
                'type' => 'options',
                'options' => Mage::getSingleton('blugento_popup/system_config_source_status')->getOptionArray()
            )
        );
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('delete_popup');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'   => Mage::helper('blugento_popup')->__('Delete'),
                'url'     => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('blugento_popup')->__('Are you sure?'),
            )
        );

        return $this;
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