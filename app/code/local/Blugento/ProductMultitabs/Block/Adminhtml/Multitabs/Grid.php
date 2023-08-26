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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Block_Adminhtml_Multitabs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_productmultitabs');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blugento_productmultitabs/multitabs')->getCollection();

        foreach ($collection as $link) {
            if ($link->getStores() && $link->getStores() != 0 ){
                $link->setStores(explode(',',$link->getStores()));
            }
            else{
                $link->setStores(array('0'));
            }
        }

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
                'width'  => '25'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'  => $this->__('Tab Name'),
                'width'   => '150',
                'align'   => 'left',
                'index'   => 'name'
            )
        );
        $this->addColumn(
            'content',
            array(
                'header' => $this->__('Content Type'),
                'align' => 'left',
                'index' => 'content',
                'width' => '200',
                'type'  => 'options',
                'options' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_contenttype')->getOptionArray()
            )
        );
        $this->addColumn(
            'content_block',
            array(
                'header' => $this->__('Content'),
                'align' => 'left',
                'width' => '200',
                'renderer' => 'blugento_productmultitabs/adminhtml_multitabs_grid_content_render'
            )
        );
        $this->addColumn(
            'active_on_products',
            array(
                'header' => $this->__('Active on Products'),
                'align' => 'left',
                'index' => 'active_on_products',
                'width' => '200',
                'type'  => 'options',
                'options' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_active')->getOptionArray()
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
                'options' => Mage::getSingleton('blugento_productmultitabs/adminhtml_system_config_source_multitabs_status')->getOptionArray()
            )
        );
        $this->addColumn(
            'sort_order',
            array(
                'header'  => $this->__('Sort Order'),
                'width'   => '50',
                'align'   => 'center',
                'index'   => 'sort_order',
            )
        );
        $this->addColumn('store', array(
            'header' => 'Website',
            'index' => 'store',
            'type' => 'store',
            'width' => '50',
            'store_view'=> true,
            'display_deleted' => false,
            'renderer' => 'blugento_productmultitabs/adminhtml_multitabs_grid_store_render',
        ));
        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Created Date'),
                'width'  => '150',
                'align'  => 'left',
                'index'  => 'created_at'
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        $tab = Mage::getModel('blugento_productmultitabs/multitabs')->load($row->getId());
        $type = $tab->getType();

        if ($type == 'default') {
            return $this->getUrl('*/*/view', array(
                'id'         => $row->getId(),
                'name'       => $row->getName()
            ));
        } else {
            return $this->getUrl('*/*/edit', array(
                'id'   => $row->getId(),
                'name' => $row->getName()
            ));
        }
    }
}