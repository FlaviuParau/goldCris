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
 * @package     Blugento_FormsGenerator
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FormsGenerator_Block_Adminhtml_Form_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_formsgenerator');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blugento_formsgenerator/forms')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->__('Form ID'),
                'align'  => 'center',
                'index'  => 'id',
                'width'  => 20
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => $this->__('Form Name'),
                'align'  => 'left',
                'index'  => 'form_name'
            )
        );
        $this->addColumn(
            'email_template',
            array(
                'header' => $this->__('Email Template'),
                'align'  => 'left',
                'index'  => 'email_template_id',
                'type'   => 'options',
                'options' => Mage::getSingleton('blugento_formsgenerator/system_config_source_email_template')->getOptionArray()
            )
        );
        $this->addColumn(
            'recipient',
            array(
                'header' => $this->__('Recipient'),
                'align'  => 'left',
                'index'  => 'recipient'
            )
        );
        $this->addColumn(
            'recipient_email',
            array(
                'header' => $this->__('Recipient Email'),
                'align'  => 'left',
                'index'  => 'recipient_email'
            )
        );
        $this->addColumn(
            'shortcode',
            array(
                'header' => $this->__('Shortcode'),
                'align'  => 'left',
                'index'  => 'shortcode'
            )
        );
        $this->addColumn(
            'success_page',
            array(
                'header' => $this->__('Success Page'),
                'align'  => 'left',
                'index'  => 'success_page',
                'type'   => 'options',
                'options' => Mage::getSingleton('blugento_formsgenerator/system_config_source_cms_page')->getOptionArray()
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Created Date'),
                'align'  => 'left',
                'index'  => 'created_at'
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header' => $this->__('Last Modified'),
                'align'  => 'left',
                'index'  => 'updated_at'
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'  => $this->__('Status'),
                'width'   => '75',
                'align'   => 'left',
                'index'   => 'form_status',
                'type'    => 'options',
                'options' => Mage::getSingleton('blugento_formsgenerator/system_config_source_status')->getOptionArray()
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'    => $this->__('Action'),
                'width'     => '80',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('blugento_formsgenerator')->__('Edit'),
                        'url'     => array('base' => '*/*/edit'),
                        'field'   => 'id',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('forms_generator');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'   => Mage::helper('blugento_formsgenerator')->__('Delete'),
                'url'     => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('blugento_formsgenerator')->__('Are you sure?'),
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