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
 * @package     Blugento_Popup
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Popup_Block_Adminhtml_Popup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('popup_form', array('legend' => Mage::helper('blugento_popup')->__('Popup Information')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('blugento_popup')->__('Title'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'title'
            )
        );

        $fieldset->addField(
            'content',
            'select',
            array(
                'label' => Mage::helper('blugento_popup')->__('Content (Static Block)'),
                'name' => 'content',
                'values' => Mage::getSingleton('blugento_popup/system_config_source_cms_block')->getAllOptions()
            )
        );

        $pages = $fieldset->addField(
            'pages',
            'multiselect',
            array(
                'label' => Mage::helper('blugento_popup')->__('Show on Pages'),
                'name' => 'pages[]',
                'values' => Mage::getSingleton('blugento_popup/system_config_source_cms_page')->getAllOptions(),
            )
        );

        $message = Mage::helper('blugento_popup')->__('This will be consider only if the value <b>Category Page</b> is selected in <b>Show on Pages</b> multiselect.');
        $categories = $fieldset->addField(
            'category_pages',
            'multiselect',
            array(
                'label' => Mage::helper('blugento_popup')->__('Show on Category Pages'),
                'name' => 'category_pages[]',
                'values' => Mage::getSingleton('blugento_popup/system_config_source_category')->toOptionArray(),
                'after_element_html' => $message
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => Mage::helper('blugento_popup')->__('Status'),
                'name' => 'status',
                'values' => Mage::getSingleton('blugento_popup/system_config_source_status')->getOptionArray()
            )
        );
	
	    $fieldset->addField(
	    	'stores',
		    'multiselect',
		    array(
			    'name'      => 'stores[]',
			    'label'     => Mage::helper('blugento_popup')->__('Select Store'),
			    'title'     => Mage::helper('blugento_popup')->__('Select Store'),
			    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, true),
		    )
	    );

        $message = Mage::helper('blugento_popup')->__('Will be used when more than one popup on the same page.');
        $fieldset->addField(
            'sort_order',
            'text',
            array(
                'label' => Mage::helper('blugento_popup')->__('Sort Order'),
                'name' => 'sort_order',
                'after_element_html' => $message
            )
        );

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($pages->getHtmlId(), $pages->getName())
            ->addFieldMap($categories->getHtmlId(), $categories->getName())
            ->addFieldDependence($categories->getName(), $pages->getName(), 'category'));

        if (Mage::getSingleton('adminhtml/session')->getPopupData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPopupData());
            Mage::getSingleton('adminhtml/session')->setPopupData(null);
        } elseif (Mage::registry('popup_data')) {
            $form->setValues(Mage::registry('popup_data')->getData());
        }
        return parent::_prepareForm();
    }
}


