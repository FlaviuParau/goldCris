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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Block_Adminhtml_Campaign_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_campaign');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blugento_campaign/campaign')->getCollection();
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
                'header'  => $this->__('Campaign Name'),
                'width'   => '150',
                'align'   => 'left',
                'index'   => 'name'
            )
        );
        $this->addColumn(
            'cms_page',
            array(
                'header' => $this->__('CMS Page'),
                'width'  => '150',
                'align'  => 'left',
                'index'  => 'cms_page'
            )
        );
        $this->addColumn(
            'associated_category',
            array(
                'header' => $this->__('Category ID'),
                'width'  => '30',
                'align'  => 'left',
                'index'  => 'associated_category'
            )
        );
        $this->addColumn(
            'start_date',
            array(
                'header' => $this->__('Start Date'),
                'width'  => '110',
                'align'  => 'left',
                'index'  => 'start_date'
            )
        );
        $this->addColumn(
            'end_date',
            array(
                'header' => $this->__('End Date'),
                'width'  => '110',
                'align'  => 'left',
                'index'  => 'end_date'
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'  => $this->__('Status'),
                'width'   => '60',
                'align'   => 'left',
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getSingleton('blugento_campaign/system_config_source_status')->toArray()
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