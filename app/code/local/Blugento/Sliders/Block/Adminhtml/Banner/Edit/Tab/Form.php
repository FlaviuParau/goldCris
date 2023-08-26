<?php
/**
 * Blugento Sliders
 * Tab Form Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Retrieve Additional Element Types
     *
     * @return array
    */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('blugento_sliders/adminhtml_banner_helper_image')
        );
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('banner_');
        $form->setFieldNameSuffix('banner');
        
        $this->setForm($form);

        $fieldset = $form->addFieldset('banner_general', array('legend' => $this->__('General Information')));

        $this->_addElementTypes($fieldset);

        $fieldset->addField('is_enabled', 'select', array(
            'name'      => 'is_enabled',
            'title'     => $this->__('Enabled'),
            'label'     => $this->__('Enabled'),
            'required'  => true,
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'class'     => 'required-entry'
        ));

        $fieldset->addField('group_id', 'select', array(
            'name'      => 'group_id',
            'label'     => $this->__('Group'),
            'title'     => $this->__('Group'),
            'required'  => true,
            'class'     => 'required-entry',
            'values'    => $this->_getGroups()
        ));

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => $this->__('Title'),
            'title'     => $this->__('Title'),
            'required'  => true,
            'class'     => 'required-entry'
        ));

        $fieldset->addField('url', 'text', array(
            'name'      => 'url',
            'label'     => $this->__('URL'),
            'title'     => $this->__('URL')
        ));

        $fieldset->addField('url_target', 'select', array(
            'name'      => 'url_target',
            'label'     => $this->__('URL Target'),
            'title'     => $this->__('URL Target'),
            'comment'   => $this->__('If empty, current window/tab will be used'),
            'values'    => array(
                '_self'     => 'In the same window (default)',
                '_blank'    => 'In a new window'
            )
        ));

        $fieldset->addField('alt_text', 'text', array(
            'name'      => 'alt_text',
            'label'     => $this->__('ALT Text'),
            'title'     => $this->__('ALT Text')
        ));

        $fieldset->addField('html', 'editor', array(
            'name'      => 'html',
            'label'     => $this->__('HTML'),
            'title'     => $this->__('HTML'),
            'style'     => 'height: 6em; line-height: 1.2;',
            'note'      => $this->__('Use {{banner_image}} for image tag.<br />Use {{banner_url}} for URL.'),
            'wysiwyg'   => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            	array(
            		'add_variables' => false,
		            'add_widgets' => false,
		            'add_images' => true,
		            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
		            'files_browser_window_width' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
		            'files_browser_window_height'=> (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
                )
            )
        ));

        $fieldset->addField('image', 'image', array(
            'name'      => 'image',
            'label'     => $this->__('Image'),
            'title'     => $this->__('Image')
        ));
	
	    $fieldset->addType('tablet_image', Mage::getConfig()->getBlockClassName('blugento_sliders/form_element_tablet_image'));
	    $fieldset->addField('tablet_image', 'tablet_image', array(
		    'name'      => 'tablet_image',
		    'label'     => $this->__('Tablet Image'),
		    'title'     => $this->__('Tablet Image'),
	    ));

	    $fieldset->addType('mobile_image', Mage::getConfig()->getBlockClassName('blugento_sliders/form_element_mobile_image'));
	    $fieldset->addField('mobile_image', 'mobile_image', array(
		    'name'      => 'mobile_image',
		    'label'     => $this->__('Mobile Image'),
		    'title'     => $this->__('Mobile Image'),
	    ));

        $fieldset->addField('background_color', 'text', array(
            'name'      => 'background_color',
            'label'     => $this->__('Background Color'),
            'title'     => $this->__('Background Color'),
            'class'     => 'input-text color {hash: true}'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => $this->__('Sort Order'),
            'title'     => $this->__('Sort Order'),
            'class'     => 'validate-digits'
        ));
	
	    $fieldset->addField('tablet_banner_width', 'text', array(
		    'name'      => 'tablet_banner_width',
		    'label'     => $this->__('Width for resize banner image on table'),
		    'title'     => $this->__('Width for resize banner image on table'),
		    'class'     => 'validate-digits',
		    'note'      => $this->__('Recommended value not bigger than 1024'),
	    ));
	
	    $fieldset->addField('tablet_banner_height', 'text', array(
		    'name'      => 'tablet_banner_height',
		    'label'     => $this->__('Height for resize banner image on table'),
		    'title'     => $this->__('Height for resize banner image on table'),
		    'class'     => 'validate-digits',
		    'note'      => $this->__('Recommended value not bigger than 512'),
	    ));
	
	    $fieldset->addField('mobile_banner_width', 'text', array(
		    'name'      => 'mobile_banner_width',
		    'label'     => $this->__('Width for resize banner image on mobile'),
		    'title'     => $this->__('Width for resize banner image on mobile'),
		    'class'     => 'validate-digits',
		    'note'      => $this->__('Recommended value not bigger than 640'),
	    ));
	
	    $fieldset->addField('mobile_banner_height', 'text', array(
		    'name'      => 'mobile_banner_height',
		    'label'     => $this->__('Height for resize banner image on mobile'),
		    'title'     => $this->__('Height for resize banner image on mobile'),
		    'class'     => 'validate-digits',
		    'note'      => $this->__('Recommended value not bigger than 320'),
	    ));
        
        if ($banner = Mage::registry('blugento-sliders-banner')) {
            $form->setValues($banner->getData());
        }

        return parent::_prepareForm();
    }

    /**
     * Retrieve an array of all of the stores
     *
     * @return array
     */
    protected function _getGroups()
    {
        $groups = Mage::getResourceModel('blugento_sliders/group_collection');
        $options = array('' => $this->__('-- Please Select --'));

        foreach($groups as $group) {
            $options[$group->getId()] = $group->getTitle();
        }

        return $options;
    }
}
