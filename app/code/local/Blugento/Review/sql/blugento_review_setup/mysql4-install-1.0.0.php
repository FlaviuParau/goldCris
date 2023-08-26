<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('review/review'),'thumbs_up', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned'  => false,
        'default'   => 0,
        'comment'   => 'Thumbs Up'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('review/review'),'thumbs_down', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned'  => false,
        'default'   => 0,
        'comment'   => 'Thumbs Down'
    ));

$sql  = "DELETE FROM rating WHERE rating_code <> 'Quality';";
$sql2 = "INSERT INTO `review_status` (`status_code`) VALUES ('Approved And Verified');";

try {
    $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
    $conn->query($sql);
    $conn->query($sql2);
} catch (Exception $e){
    echo $e->getMessage();
}

$installer->run("
    CREATE TABLE `{$installer->getTable('review_replies')}` (
      `reply_id` int(11) NOT NULL auto_increment,
      `review_id` int(11) NOT NULL,
      `customer_reply` text,
      `admin_answer` text,
      PRIMARY KEY  (`reply_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;"
);

$installer->endSetup();
