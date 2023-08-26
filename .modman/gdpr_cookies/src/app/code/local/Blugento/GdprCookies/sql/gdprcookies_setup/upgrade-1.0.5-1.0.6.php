<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $installer->getTable('gdprcookies/list'),
    'cookie_description',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => null
    )
);

if ($installer->tableExists('gdprcookies/list')) {
    $installer->run("UPDATE {$this->getTable('gdprcookies/list')} SET cookie_description = 'Folosit pentru a stoca date variabile personalizate la nivel de vizitator. Acest cookie este creat atunci când un dezvoltator utilizează metoda _setCustomVar cu o variabilă personalizată la nivel de vizitator. Acest cookie a fost folosit și pentru metoda de demontare _setVar. Cookie-ul este actualizat de fiecare dată când datele sunt trimise către Google Analytics.' WHERE cookie_name like '__utmv';");
}
$installer->endSetup();
