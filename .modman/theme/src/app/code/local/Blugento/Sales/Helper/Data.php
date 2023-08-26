<?php

class Blugento_Sales_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function imageOnEmailEnabled()
    {
        return Mage::getStoreConfig('sales_email/order/enable_image_email');
    }

    public function showTaxOrder()
    {
        return Mage::getStoreConfig('sales_email/order/show_tax_order');
    }

    public function getImageForEmail($item)
    {
        $productThumbnail = '';
        $productId = '';

        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && count($item->getChildrenItems()) > 0) {
            $productThumbnail = $item->getChildrenItems()[0]->getProduct()->getThumbnail();
            $productId = $item->getChildrenItems()[0]->getProduct()->getId();
        }

        if ($productId == '' || $productThumbnail == '' || $productThumbnail == 'no_selection') {
            $productThumbnail = $item->getProduct()->getThumbnail();
            $productId = $item->getProduct()->getId();
        }

        if (Mage::getStoreConfig('sales_email/order/resize_image_email')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId($item->getOrder()->getStoreId())
                ->load($productId);

            $image = Mage::helper('catalog/image')
                ->init($product, 'image')
                ->constrainOnly(true)
                ->keepAspectRatio(true)
                ->keepFrame(false)
                ->resize(70, 70);
        } else {
            $image = Mage::getModel('catalog/product_media_config')->getMediaUrl($productThumbnail);
        }

        return $image;
    }

    /**
     * Return tracking info by order
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getTrackingInfoByOrder($order)
    {
        $shipTrack = array();

        if ($order) {
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment){
                $increment_id = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos=array();
                foreach ($tracks as $track){
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }
        }

        return $shipTrack;
    }
}
