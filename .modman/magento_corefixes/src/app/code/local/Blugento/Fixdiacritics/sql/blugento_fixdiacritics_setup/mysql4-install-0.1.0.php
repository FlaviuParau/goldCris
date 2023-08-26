<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_Fixdiacritics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();
$installer->run(
    'UPDATE `directory_country_region` SET `default_name`="București" WHERE `default_name` like "Bucureşti%";
        UPDATE `directory_country_region_name` SET `name`="București" WHERE `name` like "Bucureşti";
        
        UPDATE `directory_country_region` SET `default_name`="Argeș" WHERE `default_name` like "Argeş";
        UPDATE `directory_country_region_name` SET `name`="Argeș" WHERE `name` like "Argeş";
        
        UPDATE `directory_country_region` SET `default_name`="Bistrița - Năsăud" WHERE `default_name` like "Bistriţa%";
        UPDATE `directory_country_region_name` SET `name`="Bistrița - Năsăud" WHERE `name` like "Bistriţa%";
        
        UPDATE `directory_country_region` SET `default_name`="Botoșani" WHERE `default_name` like "Botoşani";
        UPDATE `directory_country_region_name` SET `name`="Botoșani" WHERE `name` like "Botoşani";
        
        UPDATE `directory_country_region` SET `default_name`="Brașov" WHERE `default_name` like "Braşov";
        UPDATE `directory_country_region_name` SET `name`="Brașov" WHERE `name` like "Braşov";
        
        UPDATE `directory_country_region` SET `default_name`="Constanța" WHERE `default_name` like "Constanţa";
        UPDATE `directory_country_region_name` SET `name`="Constanța" WHERE `name` like "Constanţa";
        
        UPDATE `directory_country_region` SET `default_name`="Călărași" WHERE `default_name` like "Călăraşi";
        UPDATE `directory_country_region_name` SET `name`="Călărași" WHERE `name` like "Călăraşi";
        
        UPDATE `directory_country_region` SET `default_name`="Dâmbovița" WHERE `default_name` like "Dâmboviţa";
        UPDATE `directory_country_region_name` SET `name`="Dâmbovița" WHERE `name` like "Dâmboviţa";
        
        UPDATE `directory_country_region` SET `default_name`="Galați" WHERE `default_name` like "Galaţi";
        UPDATE `directory_country_region_name` SET `name`="Galați" WHERE `name` like "Galaţi";
        
        UPDATE `directory_country_region` SET `default_name`="Ialomița" WHERE `default_name` like "Ialomiţa";
        UPDATE `directory_country_region_name` SET `name`="Ialomița" WHERE `name` like "Ialomiţa";
        
        UPDATE `directory_country_region` SET `default_name`="Iași" WHERE `default_name` like "Iaşi";
        UPDATE `directory_country_region_name` SET `name`="Iași" WHERE `name` like "Iaşi";
        
        UPDATE `directory_country_region` SET `default_name`="Maramureș" WHERE `default_name` like "Maramureş";
        UPDATE `directory_country_region_name` SET `name`="Maramureș" WHERE `name` like "Maramureş";
        
        UPDATE `directory_country_region` SET `default_name`="Mehedinți" WHERE `default_name` like "Mehedinţi";
        UPDATE `directory_country_region_name` SET `name`="Mehedinți" WHERE `name` like "Mehedinţi";
        
        UPDATE `directory_country_region` SET `default_name`="Mureș" WHERE `default_name` like "Mureş";
        UPDATE `directory_country_region_name` SET `name`="Mureș" WHERE `name` like "Mureş";
        
        UPDATE `directory_country_region` SET `default_name`="Neamț" WHERE `default_name` like "Neamţ";
        UPDATE `directory_country_region_name` SET `name`="Neamț" WHERE `name` like "Neamţ";
        
        UPDATE `directory_country_region` SET `default_name`="Timiș" WHERE `default_name` like "Timiş";
        UPDATE `directory_country_region_name` SET `name`="Timiș" WHERE `name` like "Timiş";        
        
        UPDATE `directory_country_region` SET `default_name`="Caraș-Severin" WHERE `default_name` like "Caraş%";
        UPDATE `directory_country_region_name` SET `name`="Caraș-Severin" WHERE `name` like "Caraş";'
);

$installer->endSetup();
