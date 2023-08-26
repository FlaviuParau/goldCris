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
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Giftwrap_Block_Adminhtml_System_Config_Form_Fieldset_Type_List extends Mage_Adminhtml_Block_Template
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('aw_giftwrap/type/list.phtml');
    }

    /**
     * @return AW_Giftwrap_Model_Resource_Type_Collection
     */
    public function getTypeCollection()
    {
        $typeCollection = Mage::getResourceModel('aw_giftwrap/type_collection');
        $typeCollection->sortBySortOrder();
        return $typeCollection;
    }

    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('aw_giftwrap_type_add_button');
    }

    /**
     * Get html of Visible On multiselect element
     *
     * @return string
     */
    public function getStoreSelector()
    {
        return $this->getChildHtml('store_ids');
    }

    /**
     * Prepare global layout
     *
     * Add "Add Group Price" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'store_ids',
            $this->getLayout()->createBlock('adminhtml/html_select')->setData(
                array(
                    'id'            => 'type_{{id}}_store_ids',
                    'name'          => 'type[{{id}}][store_ids][]',
                    'class'         => 'select',
                    'extra_params'  => 'multiple',
                )
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $options = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
        } else {
            $options = array(Mage::app()->getStore(true)->getId());
        }

        $this->getChild('store_ids')->setOptions($options);

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id'      => 'aw_giftwrap_type_add_new_row',
                    'label'   => $this->__('Add New'),
                    'class'   => 'add'
                )
            );

        $button->setName('aw_giftwrap_type_add_button');

        /* @var $button Mage_Adminhtml_Block_Widget_Button */
        $this->setChild('aw_giftwrap_type_add_button', $button);
        return parent::_prepareLayout();
    }
}