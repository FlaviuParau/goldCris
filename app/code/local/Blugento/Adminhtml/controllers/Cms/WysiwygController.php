<?php

require_once(Mage::getModuleDir('controllers','Mage_Adminhtml') . DS . 'Cms' . DS . 'WysiwygController.php');

class Blugento_Adminhtml_Cms_WysiwygController extends Mage_Adminhtml_Cms_WysiwygController
{
	public function directiveAction()
	{
		$directive = $this->getRequest()->getParam('___directive');
		$directive = Mage::helper('core')->urlDecode($directive);
		$url = Mage::getModel('cms/adminhtml_template_filter')->filter($directive);
		try {
			$image = Varien_Image_Adapter::factory('GD2');
			$image->open($url);
		} catch (Exception $e) {
			$image = Varien_Image_Adapter::factory('GD2');
			$image->open(Mage::getSingleton('cms/wysiwyg_config')->getSkinImagePlaceholderPath());
		}
		ob_start();
		$image->display();
		$this->getResponse()->setBody(ob_get_contents());
		ob_end_clean();
	}
}
