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
    'UPDATE `directory_country_region` SET `default_name`="Bucuresti" WHERE `default_name` like "Bucureşti%";
        UPDATE `directory_country_region_name` SET `name`="Bucuresti" WHERE `name` like "Bucureşti";
        
        UPDATE `directory_country_region` SET `default_name`="Arges" WHERE `default_name` like "Argeş";
        UPDATE `directory_country_region_name` SET `name`="Arges" WHERE `name` like "Argeş";
        
        UPDATE `directory_country_region` SET `default_name`="Bistrita-Nasaud" WHERE `default_name` like "Bistriţa%";
        UPDATE `directory_country_region_name` SET `name`="Bistrita-Nasaud" WHERE `name` like "Bistriţa%";
        
        UPDATE `directory_country_region` SET `default_name`="Botosani" WHERE `default_name` like "Botoşani";
        UPDATE `directory_country_region_name` SET `name`="Botosani" WHERE `name` like "Botoşani";
        
        UPDATE `directory_country_region` SET `default_name`="Brasov" WHERE `default_name` like "Braşov";
        UPDATE `directory_country_region_name` SET `name`="Brasov" WHERE `name` like "Braşov";
        
        UPDATE `directory_country_region` SET `default_name`="Constanta" WHERE `default_name` like "Constanţa";
        UPDATE `directory_country_region_name` SET `name`="Constanta" WHERE `name` like "Constanţa";
        
        UPDATE `directory_country_region` SET `default_name`="Calarasi" WHERE `default_name` like "Călăraşi";
        UPDATE `directory_country_region_name` SET `name`="Calarasi" WHERE `name` like "Călăraşi";
        
        UPDATE `directory_country_region` SET `default_name`="Dambovita" WHERE `default_name` like "Dâmboviţa";
        UPDATE `directory_country_region_name` SET `name`="Dambovita" WHERE `name` like "Dâmboviţa";
        
        UPDATE `directory_country_region` SET `default_name`="Galati" WHERE `default_name` like "Galaţi";
        UPDATE `directory_country_region_name` SET `name`="Galati" WHERE `name` like "Galaţi";
        
        UPDATE `directory_country_region` SET `default_name`="Ialomita" WHERE `default_name` like "Ialomiţa";
        UPDATE `directory_country_region_name` SET `name`="Ialomita" WHERE `name` like "Ialomiţa";
        
        UPDATE `directory_country_region` SET `default_name`="Iasi" WHERE `default_name` like "Iaşi";
        UPDATE `directory_country_region_name` SET `name`="Iasi" WHERE `name` like "Iaşi";
        
        UPDATE `directory_country_region` SET `default_name`="Maramures" WHERE `default_name` like "Maramureş";
        UPDATE `directory_country_region_name` SET `name`="Maramures" WHERE `name` like "Maramureş";
        
        UPDATE `directory_country_region` SET `default_name`="Mehedinti" WHERE `default_name` like "Mehedinţi";
        UPDATE `directory_country_region_name` SET `name`="Mehedinti" WHERE `name` like "Mehedinţi";
        
        UPDATE `directory_country_region` SET `default_name`="Mures" WHERE `default_name` like "Mureş";
        UPDATE `directory_country_region_name` SET `name`="Mures" WHERE `name` like "Mureş";
        
        UPDATE `directory_country_region` SET `default_name`="Neamt" WHERE `default_name` like "Neamţ";
        UPDATE `directory_country_region_name` SET `name`="Neamt" WHERE `name` like "Neamţ";
        
        UPDATE `directory_country_region` SET `default_name`="Timis" WHERE `default_name` like "Timiş";
        UPDATE `directory_country_region_name` SET `name`="Timis" WHERE `name` like "Timiş";        
        
        UPDATE `directory_country_region` SET `default_name`="Caras-Severin" WHERE `default_name` like "Caraş%";
        UPDATE `directory_country_region_name` SET `name`="Caras-Severin" WHERE `name` like "Caraş";
        
        UPDATE `directory_country_region` SET `default_name`="Bacau" WHERE `default_name` like "Bacău";
        UPDATE `directory_country_region_name` SET `name`="Bacau" WHERE `name` like "Bacău";
        
         UPDATE `directory_country_region` SET `default_name`="Braila" WHERE `default_name` like "Brăila";
        UPDATE `directory_country_region_name` SET `name`="Braila" WHERE `name` like "Brăila";
        
         UPDATE `directory_country_region` SET `default_name`="Buzau" WHERE `default_name` like "Buzău";
        UPDATE `directory_country_region_name` SET `name`="Buzau" WHERE `name` like "Buzău";
        
         UPDATE `directory_country_region` SET `default_name`="Salaj" WHERE `default_name` like "Sălaj";
        UPDATE `directory_country_region_name` SET `name`="Salaj" WHERE `name` like "Sălaj";
        
         UPDATE `directory_country_region` SET `default_name`="Valcea" WHERE `default_name` like "Vâlcea";
        UPDATE `directory_country_region_name` SET `name`="Valcea" WHERE `name` like "Vâlcea";'
);

$installer->endSetup();
