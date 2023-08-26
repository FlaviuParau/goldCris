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
 * @package     Blugento_UploadFiles
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_UploadFiles_Model_Observer
{
    public function adminSystemConfigSave(Varien_Event_Observer $observer)
    {
        /* @var Blugento_UploadFiles_Helper_Data $helper */
        $helper = Mage::helper('blugento_uploadfiles');

        $errors  = array();
        $uploadedFiles  = array();

        $configPath = Mage::getStoreConfig('blugento_uploadfiles/general/path');
        $uploadDirectory = Mage::getBaseDir() . DS . $configPath;
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        try {
            $counter = 0;
            foreach($_FILES["files"]["tmp_name"] as $key=>$tmp_name) {
                $temp = $_FILES["files"]["tmp_name"][$key];
                $name = $_FILES["files"]["name"][$key];

                if(empty($temp)) {
                    break;
                }

                $counter++;

                if(file_exists($uploadDirectory . "/" . $name) == true) {
                    unlink($uploadDirectory . "/" . $name);
                }

                move_uploaded_file($temp,$uploadDirectory . "/" . $name);
                array_push($uploadedFiles, $name);
            }

            if($counter>0) {
                if(count($errors)>0) {
                    $errorMessage = implode(',', $errors);
                    Mage::getSingleton('adminhtml/session')->addError($errorMessage);
                }

                if(count($uploadedFiles)>0) {
                    $successMessage = $helper->__('%s file(s) are successfully uploaded.', count($uploadedFiles));
                    Mage::getSingleton('adminhtml/session')->addSuccess($successMessage);
                }
            } else {
                $errorMessage = $helper->__('Please, Select file(s) to upload.');
                Mage::getSingleton('adminhtml/session')->addError($errorMessage);
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $this;
    }
}
