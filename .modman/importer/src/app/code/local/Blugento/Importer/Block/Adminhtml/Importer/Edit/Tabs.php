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

class Blugento_Importer_Block_Adminhtml_Importer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('convert_importer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Import/Export Profile'));
    }

    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_importer_profile');

        $wizardBlock = $this->getLayout()->createBlock('blugento_importer/adminhtml_system_convert_gui_edit_tab_wizard');
        $wizardBlock->addData($profile->getData());

        $new = !$profile->getId();

        $this->addTab('wizard', array(
            'label'     => Mage::helper('adminhtml')->__('Profile Settings'),
            'content'   => $wizardBlock->toHtml(),
            'active'    => true,
        ));

        if (!$new) {
//            if ($profile->getDirection()!='export') {
//                $this->addTab('upload', array(
//                    'label'     => Mage::helper('adminhtml')->__('Upload File'),
//                    'content'   => $this->getLayout()->createBlock('blugento_importer/adminhtml_system_convert_gui_edit_tab_upload')->toHtml(),
//                ));
//            }

            $this->addTab('map', array(
                'label'     => Mage::helper('adminhtml')->__('Map Fields'),
                'content'   => $this->getLayout()->createBlock('blugento_importer/adminhtml_system_convert_profile_edit_tab_map')->toHtml(),
            ));

            $this->addTab('run', array(
                'label'     => Mage::helper('adminhtml')->__('Run Profile'),
                'content'   => $this->getLayout()->createBlock('blugento_importer/adminhtml_system_convert_profile_edit_tab_run')->toHtml(),
            ));

            $this->addTab('history', array(
                'label'     => Mage::helper('adminhtml')->__('Profile History'),
                'content'   => $this->getLayout()->createBlock('blugento_importer/adminhtml_system_convert_profile_edit_tab_history')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }
}
