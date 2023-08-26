<?php

class Magento_Corefixes_Model_Observer
{
    public function deleteFailedJobs()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $sql1 = "DELETE FROM cron_schedule WHERE status = 'running' AND finished_at IS NULL AND executed_at < NOW() - INTERVAL 4 HOUR;";
        $sql2 = "DELETE FROM cron_schedule WHERE status = 'missed';";

        try {
            $connection->query($sql1);
            $connection->query($sql2);

        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function removeCoupon(Varien_Event_Observer $observer)
    {
        $controller = $observer->getControllerAction();
        if ($controller->getRequest()->getParam('remove') == 1) {
            Mage::getSingleton("checkout/session")->unsetData('cart_coupon_code');
        }
        return $this;
    }

    public function reindexPrices()
    {
        $process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
        $process->reindexAll();
    }
}
