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
 * @package     Blugento_Swatches
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Swatches_Block_Adminhtml_Swatches_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_swatches');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blugento_swatches/swatches')->getCollection();
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
            'attribute',
            array(
                'header'  => $this->__('Attribute'),
                'width'   => '150',
                'align'   => 'left',
                'index'   => 'attribute'
            )
        );

        $this->addColumn(
            'value',
            array(
                'header' => $this->__('Value'),
                'width' => '150',
                'align' => 'left',
                'index' => 'option_id',
                'renderer' => 'blugento_swatches/adminhtml_swatches_grid_render_value',
                'filter_condition_callback' => array($this, '_valueFilter')
            )
        );

        $this->addColumn(
            'mode',
            array(
                'header' => $this->__('Mode'),
                'align' => 'left',
                'index' => 'mode',
                'width' => '200',
                'type'  => 'options',
                'options' => Mage::getSingleton('blugento_swatches/adminhtml_system_config_source_mode')->getOptionArray()
            )
        );

        $this->addColumn(
            'image_name',
            array(
                'header' => $this->__('Image'),
                'align' => 'left',
                'width' => '200',
                'index' => 'image_name',
                'renderer' => 'blugento_swatches/adminhtml_swatches_grid_render_image'
            )
        );

        $this->addColumn(
            'color',
            array(
                'header' => $this->__('Color'),
                'align' => 'left',
                'index' => 'color',
                'width' => '200',
                'renderer' => 'blugento_swatches/adminhtml_swatches_grid_render_color'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Return row url
     *
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'        => $row->getId(),
            'option_id' => $row->getOptionId()
        ));
    }

    /**
     * Return grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));

    }

    /**
     * Filter render value column
     *
     * @param $collection
     * @param $column
     * @return $this
     */
    protected function _valueFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->getSelect()->joinLeft(array('attribute_option' => 'eav_attribute_option_value'),
            'main_table.option_id = attribute_option.option_id and attribute_option.store_id = 0',
            array('eaov_val'=>'value' , 'eaov_option'=>'option_id'));

        $collection->getSelect()->where("`attribute_option`.`value` like ?", "%$value%");

        return $this;
    }
}