<?php

$installer = $this;
$installer->startSetup();

$sql = "UPDATE rating SET rating_code = 'Rating' WHERE rating_code LIKE 'Quality';";
try {
    $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
    $conn->query($sql);
} catch (Exception $e) {
    echo $e->getMessage();
}
$sql = "SELECT store_id from core_store;";
$sql2 = "SELECT rating_id FROM rating WHERE rating_code LIKE 'Rating';";
try {
    $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
    $allStores = $conn->fetchAll($sql);
    $rating = $conn->fetchRow($sql2);
} catch (Exception $e) {
    echo $e->getMessage();
}
$rating_id = $rating['rating_id'];
foreach ($allStores as $store) {
    $store_id = $store['store_id'];
    if ($store_id != 0) {
        $sql = "INSERT INTO `rating_store` (`rating_id`,`store_id`) VALUES ($rating_id,$store_id);";
        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
            $conn->query($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
$installer->endSetup();
