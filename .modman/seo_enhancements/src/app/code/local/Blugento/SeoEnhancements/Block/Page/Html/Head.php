<?php

class Blugento_SeoEnhancements_Block_Page_Html_Head extends Mage_Core_Block_Template
{

    protected function isOptionEnabled()
    {
        return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/new_page_title');
    }
	
	protected function isChangePageAndMetaTitleOptionEnabled()
	{
		return Mage::helper('blugento_seoenhancements')->isChangePageAndMetaTitleOptionEnabled();
	}
	
	protected function getSelectedAttributes()
	{
		$selectedAttributes = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/selected_attributes');
		$selectedAttributes = explode(',', $selectedAttributes);
		
		return $selectedAttributes;
	}
	
	public function changeCategoryFilterTitle()
	{
		$params = $this->getRequest()->getParams();
		$title = '';
		foreach ($params as $key => $param) {
			if (($key != 'id') && ($key != 'dir') && ($key != 'order') && in_array($key, $this->getSelectedAttributes())) {
				$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $key);
				$values = $attribute->getSource()->getAllOptions(true, true);
				foreach ($values as $value) {
					if ($value['value'] == $param) {
						$title .= $value['label'] . ' ' . Mage::getStoreConfig('design/head/title_suffix');
					}
				}
			}
		}
		
		return $title;
	}
	
	public function changeCategoryFilterHOneTagTitle()
	{
		$params = $this->getRequest()->getParams();
		$title = '';
		foreach ($params as $key => $param) {
			if (($key != 'id') && ($key != 'dir') && ($key != 'order') && in_array($key, $this->getSelectedAttributes())) {
				$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $key);
				$values = $attribute->getSource()->getAllOptions(true, true);
				foreach ($values as $value) {
					if ($value['value'] == $param) {
						$title .= ' ' . $value['label'];
					}
				}
			}
		}
		
		return $title;
	}

    public function categoryFilterTitle()
    {
        $params = $this->getRequest()->getParams();
        $cat = Mage::getModel('catalog/category')->load($this->getRequest()->getParam('id'));
        $title = $cat->getName();
        foreach ($params as $key => $param) {
            if (($key != 'id') && ($key != 'dir') && ($key != 'order')) {
                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $key);
                $values = $attribute->getSource()->getAllOptions(true, true);
                foreach ($values as $value) {
                    if ($value['value'] == $param) {
                        $title .= ' ' . $value['label'];
                    }
                }
            }
        }
        $title .= Mage::getStoreConfig('design/head/title_suffix');
        return $title;
    }

    public function categoryFilterHOneTag()
    {
        $params = $this->getRequest()->getParams();
        $title = '';
        foreach ($params as $key => $param) {
            if (($key != 'id') && ($key != 'dir') && ($key != 'order')) {
                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $key);
                $values = $attribute->getSource()->getAllOptions(true, true);
                foreach ($values as $value) {
                    if ($value['value'] == $param) {
                        $title .= ' ' . $value['label'];
                    }
                }
            }
        }
        return $title;
    }
}