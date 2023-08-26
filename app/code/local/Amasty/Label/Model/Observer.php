<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */
class Amasty_Label_Model_Observer
{
    /**
     * event page_block_html_topmenu_gethtml_after
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function startObserveCollection(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('amlabel/options/use_js')) {
            //start observe collection only in content section( don't observe minicart)
            Mage::register('amlabel_start_observing', true, true);
        }
    }

    public function onCoreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        if (!$this->isNeedObserve()) {
            return $this;
        }

        Mage::register('amlabel_getting_product', true, true);

        $block = $observer->getBlock();
        if ($block instanceof Mage_Catalog_Block_Product_Price) {
            $id = $block->getProduct()->getId();
            if (!$this->isProductIdObserved($id)) {
                $html = $observer->getTransport()->getHtml();
                $html = '<div class="price" id="amlabel-product-price-' . $id . '" style="display:none"></div>' . $html;

                $product = $block->getProduct();
                $label   = Mage::helper('amlabel')->getLabels(
                    $product,
                    $this->getLabelType($product),
                    true
                );
                if ($label) {
                    $this->addScript($id, addslashes($label));
                }

                $observer->getTransport()->setHtml($html);
            }
        }

        Mage::unregister('amlabel_getting_product');

        return $this;
    }

    /**
     * @param $productId
     * @param $label
     * @throws Mage_Core_Exception
     */
    public function addScript($productId, $label)
    {
        $scripts = Mage::registry('amlabel_scripts');
        if (!is_array($scripts)) {
            $scripts = array();
        }

        if (!array_key_exists($productId, $scripts)) {
            $scripts[$productId] = $label;
            Mage::unregister('amlabel_scripts');
            Mage::register('amlabel_scripts', $scripts);
        }
    }

    public function addLabelProductCollectionScript(Varien_Event_Observer $observer)
    {
        if (!$this->isNeedObserve()) {
            return $this;
        }

        /*
         * register global flag to prevent grouped/configurable/bundle products
         * loading all child products caught by observer
         */
        Mage::register('amlabel_getting_product', true, true);
        $productCollection = $observer->getCollection();
        $blockClass = get_class($productCollection);
        $blockedClasses = array(
            'Mage_Reports_Model_Resource_Product_Index_Viewed_Collection',
        );
        if (in_array($blockClass, $blockedClasses)) {
            return $this;
        }

        if ($productCollection) {
            foreach ($productCollection as $item) {
                if (!$this->isProductIdObserved($item->getId())) {
                    $label = Mage::helper('amlabel')->getLabels(
                        $item,
                        $this->getLabelType($item),
                        true
                    );
                    if ($label) {
                        $this->addScript($item->getId(), $label);
                    }
                }
            }
        }

        Mage::unregister('amlabel_getting_product');

        return $this;
    }

    public function addLabelProductLoadScript(Varien_Event_Observer $observer)
    {
        if (Mage::registry('amlabel_getting_product')
            || !Mage::getStoreConfig('amlabel/options/use_js')
        ) {
            return $this;
        }

        /*
         * register global flag to prevent grouped/configurable/bundle products
         * loading all child products caught by observer
         */
        Mage::register('amlabel_getting_product', true, true);

        $product = $observer->getProduct();
        if ($product) {
            $controller = Mage::app()->getRequest()->getControllerName();
            $module = Mage::app()->getRequest()->getModuleName();

            if (strpos($controller, 'cart') === false
                && strpos($controller, 'category') === false
                && $module != 'qquoteadv'
                && !Mage::app()->getRequest()->isXmlHttpRequest()
            ) {
                if (!$this->isProductIdObserved($product->getId())) {
                    $label = Mage::helper('amlabel')->getLabels($product, 'product', true);
                    if ($label) {
                        $this->addScript($product->getId(), $label);
                    }
                }
            }
        }

        Mage::unregister('amlabel_getting_product');

        return $this;
    }
    
    public function getLabelType($product)
    {
        $type = 'category';
        $currentProduct = Mage::registry('current_product');
        if ($currentProduct &&
            ($currentProduct->getId() == $product->getId() ||
            $currentProduct->getId() == $product->getParentId())
        ) {
            $type = 'product';
        }

        return $type;
    }


    /**
     * @param $productId
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isProductIdObserved($productId)
    {
        $ids = Mage::registry('amlabel_observer_ids');
        if (!is_array($ids)) {
            $ids = array();
        }

        $result = in_array($productId, $ids);
        $ids[] = $productId;
        Mage::unregister('amlabel_observer_ids');
        Mage::register('amlabel_observer_ids', $ids);

        return $result;
    }

    /**
     * @return bool
     */
    private function isScroll()
    {
        $result = false;
        if (Mage::app()->getRequest()->getParam('is_scroll')
            && Mage::app()->getRequest()->getParam('is_ajax')
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isNavigation()
    {
        $result = false;
        if (in_array(
                Mage::app()->getFrontController()->getAction()->getFullActionName(),
                array(
                    'amshopby_index_index',
                    'catalog_category_view',
                    'catalogsearch_result_index',
                    'splash_page_view'
                )
            )
            && Mage::app()->getRequest()->getParam('is_ajax')
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isNeedObserve()
    {
        $result = false;
        if (Mage::app()->getFrontController()->getAction()
            && !Mage::registry('amlabel_getting_product')
            && Mage::getStoreConfig('amlabel/options/use_js')
            && (Mage::registry('amlabel_start_observing') || $this->isScroll() || $this->isNavigation())
        ) {
            $result = true;
        }

        return $result;
    }
}
