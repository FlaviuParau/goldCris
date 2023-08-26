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

class Blugento_Importer_Block_Adminhtml_System_Convert_Profile_Edit_Tab_Run extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('blugento/importer/profile/run.phtml');
    }

    public function getButtonHtml($type)
    {
        if ($type == 'run') {
            $label   = $this->__('Run Profile Import');
            $onClick = "runProfile('run')";
            $class   = 'save';
        } else if ($type == 'test'){
            $label   = $this->__('Test Profile Data');
            $onClick = "runProfile('test')";
            $class   = 'new';
        } else {
            $label   = $this->__('Force Import Profile Images');
            $onClick = "runProfile('images')";
            $class   = 'new';
        }

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass($class)
            ->setLabel($label)
            ->setOnClick($onClick)
            ->toHtml();

        return $html;
    }

    public function getProfileId()
    {
        return Mage::registry('current_importer_profile')->getId();
    }
}
