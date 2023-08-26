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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Block_Adminhtml_Label_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productlabels_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blugento_productlabels')->__('Product Labels'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'general_section',
            array(
                'label' => Mage::helper('blugento_productlabels')->__('General Informations'),
                'title' => Mage::helper('blugento_productlabels')->__('General Informations'),
                'content' => $this->getLayout()->createBlock('blugento_productlabels/adminhtml_label_edit_tab_general')->toHtml(),
            )
        );

        $this->addTab(
            'product_section',
            array(
                'label' => Mage::helper('blugento_productlabels')->__('Product Page'),
                'title' => Mage::helper('blugento_productlabels')->__('Product Page'),
                'content' => $this->getLayout()->createBlock('blugento_productlabels/adminhtml_label_edit_tab_product')->toHtml(),
            )
        );

        $this->addTab(
            'category_section',
            array(
                'label' => Mage::helper('blugento_productlabels')->__('Category Page'),
                'title' => Mage::helper('blugento_productlabels')->__('Category Page'),
                'content' => $this->getLayout()->createBlock('blugento_productlabels/adminhtml_label_edit_tab_category')->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}