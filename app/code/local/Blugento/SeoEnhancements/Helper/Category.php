<?php
class Blugento_SeoEnhancements_Helper_Category extends Mage_Catalog_Helper_Category
{
    /**
     * Check if <link rel="canonical"> can be used for category
     *
     * @param $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null)
    {
        if ($this->_checkAttributes()) {
            return Mage::getStoreConfig(self::XML_PATH_USE_CATEGORY_CANONICAL_TAG, $store);
        }
        return false;
    }

    private function _checkAttributes()
    {
        $result = true;
        $useAttributes = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/use_attributes');
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
        $current_page = Mage::getBlockSingleton('page/html_pager')->getCurrentPage();

        if ((count($appliedFilters) == 0) && ($current_page == 1)) {
            return false;
        }
        
        if ((count($appliedFilters) > 0) && ($current_page > 1)) {
            return true;
        }

        if ($useAttributes) {
            $removeAttributes = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/remove_canonical_attributes');
            $forceAttributes = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/force_canonical_attributes');

            $removeAttributes = explode(',', $removeAttributes);
            $forceAttributes = explode(',', $forceAttributes);

            foreach ($appliedFilters as $appliedFilter) {
                if (in_array($appliedFilter->getName(), $removeAttributes)) {
                    $result = false;
                }
            }

            foreach ($appliedFilters as $appliedFilter) {
                if (in_array($appliedFilter->getName(), $forceAttributes)) {
                    $result = true;
                }
            }
        }

        return $result;
    }
}
