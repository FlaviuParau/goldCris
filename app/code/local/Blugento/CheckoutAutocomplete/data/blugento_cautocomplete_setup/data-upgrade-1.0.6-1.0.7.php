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
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

$installer = $this;
$installer->startSetup();

$sql1 = 'UPDATE blugento_cautocomplete_city SET zipcode = 100001 WHERE zipcode = 100000 AND city LIKE "Ploiesti"';
$sql2 = 'UPDATE blugento_cautocomplete_city SET zipcode = 300001 WHERE zipcode = 300000 AND city LIKE "Timisoara"';

$installer->run($sql1);
$installer->run($sql2);

$installer->endSetup();
