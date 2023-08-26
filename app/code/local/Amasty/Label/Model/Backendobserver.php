<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


class Amasty_Label_Model_Backendobserver
{
    public function applyLabels($observer)
    {
        if (!Mage::app()->getRequest()->getParam('amlabels')
            || !Mage::getSingleton('admin/session')->isAllowed('catalog/products/assign_labels')
        ) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $collection = Mage::getModel('amlabel/label')->getCollection()
            ->addFieldToFilter('include_type', array('neq' => 1));

        foreach ($collection as $label) {
            $skus = trim($label->getIncludeSku(), ', ');
            if ($skus) {
                $skus = explode(',', $skus);
            } else {
                $skus = array();
            }

            $name = 'amlabel_' . $label->getId();
            if (Mage::app()->getRequest()->getParam($name)) { // add
                if (!in_array($product->getSku(), $skus)) {
                    $skus[] = $product->getSku();
                }
            } else { // remove
                $key = array_search($product->getSku(), $skus);
                while (false !== $key) {
                    unset($skus[$key]);
                    $key = array_search($product->getSku(), $skus);
                }
            }
            $label->setIncludeSku(implode(',', $skus));
            $label->save();
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function onCoreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('catalog/products/assign_labels')) {
            $block = $observer->getBlock();
            $catalogEditTabsClass = Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_edit_tabs');
            if ($catalogEditTabsClass == get_class($block) && $block->getProduct()->getTypeId()) {
                $name = Mage::helper('amlabel')->__('Product Labels');
                $block->addTab(
                    'general',
                    array(
                        'label'   => $name,
                        'content' => $block->getLayout()->createBlock('amlabel/adminhtml_catalog_product_edit_labels')
                                ->setTitle($name)->toHtml(),
                    )
                );
            }
        }

        return $this;
    }
}
