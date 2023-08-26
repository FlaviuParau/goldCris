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

class Blugento_Importer_Block_Adminhtml_System_Convert_Profile_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('history_grid');
        $this->setDefaultSort('performed_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('blugento_importer/history_collection')
            ->joinAdminUser()
            ->addFieldToFilter('profile_id', Mage::registry('current_importer_profile')->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('profile_name', array(
            'header'    => Mage::helper('adminhtml')->__('Profile Name'),
            'index'     => 'profile_name',
            'width'     => '150px',
        ));

        $this->addColumn('entity_type', array(
            'header'    => Mage::helper('adminhtml')->__('Entity Type'),
            'index'     => 'entity_type',
        ));

        $this->addColumn('result', array(
            'header'    => Mage::helper('adminhtml')->__('Result'),
            'index'     => 'result',
        ));

        $this->addColumn('imported', array(
            'header'    => Mage::helper('adminhtml')->__('No. of Imported Products'),
            'index'     => 'imported',
            'width'     => '5px',
        ));

        $this->addColumn('updated', array(
            'header'    => Mage::helper('adminhtml')->__('No. of Updated Products'),
            'index'     => 'updated',
            'width'     => '5px',
        ));

        $this->addColumn('missing_images', array(
            'header'    => Mage::helper('adminhtml')->__('Missing Images'),
            'index'     => 'missing_images',
        ));

        $this->addColumn('skipped_rows', array(
            'header'    => Mage::helper('adminhtml')->__('Skipped Rows'),
            'index'     => 'skipped_rows',
        ));

        $this->addColumn('run_at', array(
            'header'    => Mage::helper('adminhtml')->__('Run At'),
            'index'     => 'run_at',
            'type'      => 'datetime',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/history', array('_current' => true));
    }
}
