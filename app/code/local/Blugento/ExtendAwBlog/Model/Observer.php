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
 * @package     Blugento_ExtendAwBlog
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ExtendAwBlog_Model_Observer
{
    public function appendMyNewCustomFiled(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if (!isset($block)) {
            return $this;
        }

        if ($block instanceof AW_Blog_Block_Manage_Blog_Edit_Tab_Form) {
            $form = $block->getForm();

            $id = Mage::app()->getRequest()->getParam('id');
            $model = Mage::getModel('blog/blog')->load($id);
            $form->setValues($model->getData());

            $fieldset = $form->getElement('blog_form');
            $fieldset->addField('sort_order', 'text', array(
                'name'      => 'sort_order',
                'value'     => $model->getSortOrder(),
                'label'     => Mage::helper('adminhtml')->__('Sort Order'),
                'title'     => Mage::helper('adminhtml')->__('Sort Order'),
                'disabled'  => false,
            ));
        }

        return $this;
    }
}
