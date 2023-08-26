<?php

class AjaxLogin_Model_System_Config_Source_Cmsblock extends Mage_Core_Model_Abstract
{
	protected $_options;
	
	public function toOptionArray()
	{
		if (is_null($this->_options)){
			$this->_options = array();
			$collection = Mage::getModel('cms/block')->getCollection();
			foreach ($collection as $block) {
				$this->_options[] = array('label'=> $block->getTitle(), 'value' => $block->getIdentifier());
			}
		}
		
		$options = $this->_options;
		
		array_unshift($options, array('value' => '', 'label' => Mage::helper('ajaxlogin')->__('-- Select --')));
		
		return $options;
	}
}