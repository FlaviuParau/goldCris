<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.3.16
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Blog_Block_Manage_Cat_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                 'id'     => 'edit_form',
                 'action' => $this->getUrl(
                     '*/*/save', array('id' => $this->getRequest()->getParam('id'))
                 ),
                 'method' => 'post',
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'category_form', array('legend' => Mage::helper('blog')->__('Category Information'))
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                 'label'    => Mage::helper('blog')->__('Title'),
                 'name'     => 'title',
                 'required' => true
            )
        );

        $fieldset->addField(
            'identifier',
            'text',
            array(
                 'label'    => Mage::helper('blog')->__('Identifier'),
                 'name'     => 'identifier',
                 'required' => true
            )
        );

        $customer_groups = array();
        $groups = Mage::getModel('customer/group')->getCollection();
        foreach ($groups as $Group) {
            $customer_groups[] = (array(
                'label' => (string)$Group->getCustomerGroupCode(),
                'value' =>  $Group->getCustomerGroupId()
            ));
        }
        $fieldset->addField(
            'enable_for_customer_group',
            'multiselect',
            array(
                'label'              => Mage::helper('blog')->__('Enable for Specific Customer Group'),
                'name'               => 'enable_for_customer_group',
                'values'             =>  $customer_groups
            )
        );

        $fieldset->addField(
            'sort_order',
            'text',
            array(
                 'label' => Mage::helper('blog')->__('Sort Order'),
                 'name'  => 'sort_order',
            )
        );

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'multiselect',
                array(
                     'name'     => 'stores[]',
                     'label'    => Mage::helper('cms')->__('Store View'),
                     'title'    => Mage::helper('cms')->__('Store View'),
                     'required' => true,
                     'values'   => Mage::getSingleton('adminhtml/system_store')
                             ->getStoreValuesForForm(false, true),
                )
            );
        }

        $fieldset->addField(
            'meta_keywords',
            'editor',
            array(
                 'name'  => 'meta_keywords',
                 'label' => Mage::helper('blog')->__('Keywords'),
                 'title' => Mage::helper('blog')->__('Meta Keywords'),
            )
        );

        $fieldset->addField(
            'meta_description',
            'editor',
            array(
                 'name'  => 'meta_description',
                 'label' => Mage::helper('blog')->__('Description'),
                 'title' => Mage::helper('blog')->__('Meta Description'),
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getBlogData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogData());
            Mage::getSingleton('adminhtml/session')->setBlogData(null);
        } elseif (Mage::registry('blog_data')) {
            $form->setValues(Mage::registry('blog_data')->getData());
        }
        return parent::_prepareForm();
    }
}