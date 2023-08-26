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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Model_Image extends Mage_Core_Model_Abstract
{
    protected $_extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

    /**
     * Validate uploaded image file.
     *
     * @param array $image
     * @return array
     */
    public function validateImage($image)
    {
        $result['error'] = 0;
        if (!$this->_validateSize($image['size'])) {
            $result['error'] = 1;
            $result['message'] = Mage::helper('blugento_productlabels')->__('The image must be smaller than 1 MB.');
        }

        if (!$this->_validateExtension($image['name'])) {
            $result['error'] = 1;
            $result['message'] = Mage::helper('blugento_productlabels')->__('The image extension is invalid. Allowed extensions are: ') . implode(', ', $this->_extensions);
        }

        if ($image['error']) {
            $result['error'] = 1;
            $result['message'] = Mage::helper('blugento_productlabels')->__('The image file is invalid! Please upload another image.');
        }

        return $result;
    }

    /**
     * Upload image to the media folder.
     *
     * @param array $image
     * @param string $fieldName
     * @param string $name
     * @return bool|string
     */
    public function uploadImage($image, $fieldName, $name)
    {
        try {
            $uploader = new Varien_File_Uploader($fieldName);
            $uploader->setAllowedExtensions($this->_extensions);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);

            $path = Mage::getBaseDir('media') . DS .'blugento_productlabels' . DS . 'custom' . DS . $name;

            if (!file_exists($path)) {
                mkdir($path);
            }

            $uploader->save($path, $image['name']);

            $dimensions = getimagesize($path . DS . $image['name']);

            /** key 0 is width -- key 1 is height */
            if ($dimensions[0] > 110 || $dimensions[1] > 110) {
                $this->_resizeImage($path . DS . $image['name']);
            }

            $filepath = $image['name'];

            return $filepath;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    /**
     * Delete the old image before setting the new one
     *
     * @param $imagePath
     */
    public function deleteOldImage($imagePath)
    {
        $imagePath = explode('/', $imagePath);
        $name = $imagePath[count($imagePath) - 1];
        $dirname = $imagePath[count($imagePath) - 2];

        $path = Mage::getBaseDir('media') . '/blugento_productlabels/custom/' . $dirname . '/' . $name;
        unlink($path);
    }

    /**
     * Remove all the images from a certain label and its folder
     *
     * @param string $dirname
     */
    public function removeImageDirectory($dirname)
    {
        /** @var Blugento_ProductLabels_Helper_Data $model */
        $helper = Mage::helper('blugento_productlabels');

        $directory = Mage::getBaseDir('media') . '/blugento_productlabels/custom/' . $dirname;

        $helper->unlinkRecursive($directory, true);
    }

    /**
     * Validate image size to be smaller than 1 MB.
     *
     * @param int $size
     * @return bool
     */
    private function _validateSize($size)
    {
        if ($size <= 1048576) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate extensions to match an image extension.
     *
     * @param string $imageName
     * @return bool
     */
    private function _validateExtension($imageName)
    {
        $extension = explode('.', $imageName);
        $extension = $extension[count($extension) - 1];

        $validation = false;
        foreach ($this->_extensions as $ext) {
            if ($extension == $ext) {
                $validation = true;
            }
        }

        return $validation;
    }

    /**
     * Resize image to 110x110.
     *
     * @param string $imagePath
     */
    private function _resizeImage($imagePath)
    {
        $vImage = new Varien_Image($imagePath);
        $vImage->constrainOnly(true);
        $vImage->keepFrame(true);
        $vImage->keepTransparency(true);
        $vImage->keepAspectRatio(true);
        $vImage->backgroundColor(array(255,255,255));
        $vImage->resize(110, 110);
        $vImage->save($imagePath);
    }
}
