<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Giftwrap_Helper_Image extends Mage_Core_Helper_Data
{
    const PLACEHOLDER_IMAGE_NAME = 'placeholder.png';

    const EXCEPTION_CODE_UNSUPPORTED_IMAGE_TYPE = 1;

    /**
     * Resize image and return resized url
     *
     * @param int $typeId
     * @param string $imageName
     * @param int $width
     * @param int $height
     * @return string
     */
    public function resizeImage($typeId, $imageName, $width = 100, $height = null)
    {
        $cachedImagePath = $this->getCachedImagePath($typeId, $imageName, $width, $height);
        if ($this->isCached($cachedImagePath)) {
            return $this->getCachedImageUrl($typeId, $imageName, $width, $height);
        }

        $originalImagePath = $this->getImagePath($typeId, $imageName);

        // If media/aw_giftwrap not writable return original full size images
        if (!is_writable($this->getBaseGiftwrapFolderPath())) {
            if (file_exists($originalImagePath) && is_file($originalImagePath)) {
                return $this->getImageUrl($typeId, $imageName);
            }
            return $this->getPlaceholderUrl();
        }

        if (!file_exists($originalImagePath) || !is_file($originalImagePath)) {
            $originalImagePath = $this->getPlaceholderPath();
        }

        if (is_null($width) && is_null($height)) {
            list($width, $height) = getimagesize($originalImagePath);
        }

        try {
            $imageObj = new Varien_Image($originalImagePath);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(true);
            $imageObj->backgroundColor(array(255, 255, 255));
            $imageObj->resize($width, $height);
            $imageObj->save($cachedImagePath);
        } catch (Exception $e) {
            Mage::logException($e);
            return $this->getPlaceholderUrl();
        }

        return $this->getCachedImageUrl($typeId, $imageName, $width, $height);
    }

    /**
     * Upload file, and return uploaded file name
     *
     * @param AW_Giftwrap_Model_Type $type
     * @param string $originalId
     *
     * @return null|string
     * @throws Exception
     */
    public function uploadImage($type, $originalId)
    {
        $fileCode = 'type_' . $originalId . '_image';
        $isFileUploaded = false;
        if (array_key_exists($fileCode, $_FILES) && array_key_exists('tmp_name', $_FILES[$fileCode])) {
            $isFileUploaded = !!$_FILES[$fileCode]['tmp_name'];
        }
        if (!$isFileUploaded) {
            return null;
        }

        if (!$this->isAllowedFileExtensions($_FILES[$fileCode]['type'])) {
            throw new Exception($_FILES[$fileCode]['name'], self::EXCEPTION_CODE_UNSUPPORTED_IMAGE_TYPE);
        }

        $uploader = new Varien_File_Uploader($fileCode);
        $uploader->setAllowCreateFolders(true);
        $uploader->setAllowRenameFiles(true);

        // Set media as the upload dir
        $imagePath = Mage::helper('aw_giftwrap/image')->getImageFolderPath($type->getId());

        // Upload the image
        $uploader->save($imagePath);
        $uploadedFileName = $uploader->getUploadedFileName();

        return $uploadedFileName;
    }

    /**
     * Check is file type allowed for upload
     * @param $fileType
     *
     * @return bool
     */
    public function isAllowedFileExtensions($fileType)
    {
        return array_key_exists($fileType, $this->getAllowedFileExtensions());
    }

    /**
     * Get allowed file extensions
     * @return array
     */
    public function getAllowedFileExtensions()
    {
        return array(
            'image/bmp'  => 'bmp',
            'image/gif'  => 'gif',
            'image/jpeg' => 'jpeg',
            'image/png'  => 'png',
        );
    }

    /**
     * Retrieve full cached image path by $typeId, $imageName and sizes
     *
     * @param int $typeId
     * @param string $imageName
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getCachedImagePath($typeId, $imageName, $width, $height)
    {
        if (is_null($imageName) || !$imageName) {
            $imageName = self::PLACEHOLDER_IMAGE_NAME;
        }
        return $cachedImagePath = $this->getCacheGiftwrapFolderPath() .
            $typeId . DS.
            $width . 'x' . (!is_null($height) ? $height : '') . DS .
            $imageName
        ;
    }

    /**
     * Get URL to cached image
     *
     * @param int $typeId
     * @param string $imageName
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getCachedImageUrl($typeId, $imageName, $width, $height)
    {
        if (is_null($imageName) || !$imageName) {
            $imageName = self::PLACEHOLDER_IMAGE_NAME;
        }
        return $this->getCacheGiftwrapFolderUrl() .
            $typeId . DS.
            $width . 'x' . (!is_null($height) ? $height : '') . DS .
            $imageName
        ;
    }

    /**
     * Check is cached image by $cachedImagePath exists
     *
     * @param string $cachedImagePath
     * @return bool
     */
    public function isCached($cachedImagePath)
    {
        if (file_exists($cachedImagePath) && is_file($cachedImagePath)) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve folder path for wrap type by $typeId
     *
     * @param int $typeId
     * @return string
     */
    public function getImageFolderPath($typeId)
    {
        return $this->getBaseGiftwrapFolderPath() . $typeId . DS;
    }

    /**
     * Retrieve full image path by $typeId and $image name
     *
     * @param int $typeId
     * @param string $imageName
     * @return string
     */
    public function getImagePath($typeId, $imageName)
    {
        return $this->getImageFolderPath($typeId) . $imageName;
    }

    /**
     * Retrieve Base Url to image folder
     *
     * @return string
     */
    public function getBaseGiftwrapFolderUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'aw_giftwrap' . DS;
    }

    /**
     * Retrieve Url for wrap type by $typeId
     *
     * @param int $typeId
     * @return string
     */
    public function getImageFolderUrl($typeId)
    {
        return $this->getBaseGiftwrapFolderUrl() . $typeId . DS;
    }

    /**
     * Retrieve full image url by $typeId and $image name
     *
     * @param int $typeId
     * @param string $imageName
     * @return string
     */
    public function getImageUrl($typeId, $imageName)
    {
        return $this->getImageFolderUrl($typeId) . $imageName;
    }

    /**
     * Retrieve full placeholder image url
     *
     * @return string
     */
    public function getPlaceholderUrl()
    {
        return $this->getBaseGiftwrapFolderUrl() . self::PLACEHOLDER_IMAGE_NAME;
    }

    /**
     * Retrieve placeholder image path
     * @return string
     */
    public function getPlaceholderPath()
    {
        return $this->getBaseGiftwrapFolderPath() . self::PLACEHOLDER_IMAGE_NAME;
    }

    /**
     * Retrieve Path to base AW_Giftwrap folder
     *
     * @return string
     */
    public function getBaseGiftwrapFolderPath()
    {
        return Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'aw_giftwrap' . DS;
    }

    /**
     * Retrieve Path to cache folder
     *
     * @return string
     */
    public function getCacheGiftwrapFolderPath()
    {
        return $this->getBaseGiftwrapFolderPath() . 'cache' . DS;
    }

    /**
     * Retrieve Base Url to cache folder
     *
     * @return string
     */
    public function getCacheGiftwrapFolderUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'aw_giftwrap' . DS . 'cache' . DS;
    }

    /**
     * Clean Image Cache
     *
     * @return bool
     */
    public function cleanImageCache()
    {
        $cacheImageDir = $this->getCacheGiftwrapFolderPath();
        return $this->removeDir($cacheImageDir);
    }

    /**
     * Recursively remove folders with contains files
     *
     * @param string $path
     * @return bool
     */
    static public function removeDir($path)
    {
        if (is_file($path)) {
            @unlink($path);
        } else {
            array_map(array('AW_Giftwrap_Helper_Image', 'removeDir'), glob($path . '/*'));
        }
        return @rmdir($path);
    }
}