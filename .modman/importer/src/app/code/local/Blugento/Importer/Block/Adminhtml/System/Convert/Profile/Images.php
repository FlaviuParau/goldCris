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
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Importer_Block_Adminhtml_System_Convert_Profile_Images extends Mage_Adminhtml_Block_Abstract
{
    /**
     * Get a profile
     * @return object
     */
    public function getProfile()
    {
        return Mage::registry('current_importer_profile');
    }

    /**
     * Process de profile images.
     *
     * @return array|string
     */
    public function getProcessProfileImages()
    {
        /** @var Blugento_Importer_Model_Images $imageModel */
        $imageModel = Mage::getModel('blugento_importer/images');

        return $imageModel->processRemoteImages($this->getProfile()->getId(), $this->getProfile()->getIsDuplicateImages());
    }
}
