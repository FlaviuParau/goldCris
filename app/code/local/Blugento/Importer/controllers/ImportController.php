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
 * @package     Blugento_Importer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Importer_ImportController extends Mage_Core_Controller_Front_Action
{
    /**
     * Run import profile manually
     */
    public function manualAction()
    {
        /** @var Blugento_Importer_Model_Importer $importer */
        $importer = Mage::getModel('blugento_importer/importer');

        $importer->cronRunOnClickProfile();
    }
}
