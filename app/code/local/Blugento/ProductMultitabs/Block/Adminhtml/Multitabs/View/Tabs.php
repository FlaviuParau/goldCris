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

class Blugento_ProductMultitabs_Block_Adminhtml_Multitabs_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('multitabs_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blugento_productmultitabs')->__('General'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'tab_section',
            array(
                'label'   => Mage::helper('blugento_productmultitabs')->__('General'),
                'title'   => Mage::helper('blugento_productmultitabs')->__('General'),
                'content' => $this->getLayout()->createBlock('blugento_productmultitabs/adminhtml_multitabs_view_tab_form')->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }
}