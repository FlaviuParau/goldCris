<?php

class Blugento_GdprCookies_Block_Adminhtml_Cookies_List_Grid_Column_Renderer_PreparationLabel extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $analyticsCategoryId = $row->getCookieCategory();
        $categories = Mage::getModel('gdprcookies/system_config_source_categories')->toOptionArray();
        foreach ($categories as $category) {
            if ($category['value'] == $analyticsCategoryId) {
                return $category['label'];
            }
        }
    }
}