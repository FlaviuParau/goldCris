<?php
class AW_Blog_Helper_Image extends Mage_Core_Helper_Abstract
{
    /**
     * Stores version of generic image uploader/resizer
     *
     * @var const string
     */
    const VERSION_ID = '1.0.0';

    /**
     * Storage for image object, used for resizing images
     *
     * @var null/Varien_Image
     */
    protected $_imageObject = null;

    /**
     * Image object properties
     *
     * @var bool
     */
    protected $_keepFrame;
    protected $_backgroundColor;
    protected $_keepAspectRatio;
    protected $_constrainOnly;

    /**
     * Flag used to determine whether to recreate already cached image
     *
     * @var bool
     */
    protected $_forceRecreate = false;

    /**
     * Filename currently initialized in the image object
     *
     * @var null|string
     */
    protected $_filename = '';

    /**
     * The folder name used to store images
     * This is relative to the media directory
     *
     * @var const string
     */
    const IMAGE_FOLDER = 'blogpic';

    /**
     * Retrieve the image URL where images are stored
     *
     * @return string
     */
    public function getBaseImageUrl()
    {
        return Mage::getBaseUrl('media') . self::IMAGE_FOLDER . '/';
    }

    /**
     * Retrieve the directory/path where images are stored
     *
     * @return string
     */
    public function getBaseImagePath()
    {
        return Mage::getBaseDir('media') . DS . self::IMAGE_FOLDER . DS;
    }

    /**
     * Retrieve the full image URL
     * Null returned if image does not exist
     *
     * @param string $image
     * @return string|null
     */
    public function getImageUrl($image)
    {
        if ($this->imageExists($image)) {
            return $this->getBaseImageUrl() . $image;
        }

        return null;
    }

    /**
     * Retrieve the full image path
     * Null returned if image does not exist
     *
     * @param string $image
     * @return string|null
     */
    public function getImagePath($image)
    {
        if ($this->imageExists($image)) {
            return $this->getBaseImagePath() . $image;
        }

        return null;
    }

    /**
     * Determine whether the image exists
     *
     * @param string $image
     * @return bool
     */
    public function imageExists($image)
    {
        return is_file($this->getBaseImagePath() . $image);
    }

    /**
     * Converts a filename, width and height into it's resized uri path
     * returned path does not include base path
     *
     * @param string $filename
     * @param int $width = null
     * @param int $height = null
     * @return string
     */
    public function getResizedImageUrl($filename, $width = null, $height = null)
    {
        return $this->getBaseImageUrl() . $this->_getRelativeResizedImagePath($filename, $width, $height);
    }

    /**
     * Converts a filename, width and height into it's resized path
     * returned path does not include base path
     *
     * @param string $filename
     * @param int $width = null
     * @param int $height = null
     * @return string
     */
    public function getResizedImagePath($filename, $width = null, $height = null)
    {
        return $this->getBaseImagePath() . $this->_getRelativeResizedImagePath($filename, $width, $height);
    }

    /**
     * Converts a filename, width and height into it's resized path
     * returned path does not include base path
     *
     * @param string $filename
     * @param int $width = null
     * @param int $height = null
     * @return string
     */
    protected function _getRelativeResizedImagePath($filename, $width = null, $height = null)
    {
        if (!is_null($width) || !is_null($height)) {
            return 'cache' . '/' . trim($width . 'x' . $height, 'x') . '/' . $filename;
        }

        return $filename;
    }

    /**
     * Initialize the image object
     * This sets up the image object for resizing and caching
     *
     * @param string $attribute
     * @return AW_Blog_Helper_Image
     */
    public function init($attribute = 'featured_image')
    {
        $this->_forceRecreate = false;
        $this->_filename = null;

        if ($imagePath = $this->getImagePath($attribute)) {
            $this->_filename = basename($imagePath);

            $this->keepAspectRatio(true);
        }

        return $this;
    }

    /**
     * Resize the image loaded into the image object
     *
     * @param string $attribute
     * @param int $width = null
     * @param int $height = null
     * @return string
     */
    public function resize($attribute = 'featured_image', $width = null, $height = null)
    {
        $cachedFilename = $this->getResizedImagePath($this->_filename, $width, $height);

        if ($this->_forceRecreate || !is_file($cachedFilename)) {
            $this->_imageObject = new Varien_Image($this->getImagePath($attribute));

            $this->_imageObject->constrainOnly($this->_constrainOnly);
            $this->_imageObject->keepAspectRatio($this->_keepAspectRatio);
            $this->_imageObject->keepFrame($this->_keepFrame);
            $this->_imageObject->backgroundColor($this->_backgroundColor);
            $this->_imageObject->resize($width, $height);
            $this->_imageObject->save($cachedFilename);
        }

        return $this->getResizedImageUrl($this->_filename, $width, $height);
    }

    /**
     * Keep the frame or add a white space
     *
     * @param bool $val
     * @return AW_Blog_Helper_Image
     */
    public function keepFrame($val)
    {
        $this->_keepFrame = $val;
        return $this;
    }

    public function backgroundColor($val)
    {
        $this->_backgroundColor = $val;
        return $this;
    }

    /**
     * Keep the aspect ratio of an image
     *
     * @param bool $val
     * @return AW_Blog_Helper_Image
     */
    public function keepAspectRatio($val)
    {
        $this->_keepAspectRatio = $val;
        return $this;
    }

    /**
     * Don't increase the size of an image, only decrease
     *
     * @param bool $val
     * @return AW_Blog_Helper_Image
     */
    public function constrainOnly($val)
    {
        $this->_constrainOnly = $val;
        return $this;
    }

    /**
     * Determine whether to recreate image that already exists
     *
     * @param bool $val
     * @return AW_Blog_Helper_Image
     */
    public function forceRecreate($val)
    {
        $this->_forceRecreate = $val;
        return $this;
    }

    /**
     * Determine whether the image object has been initialised
     *
     * @return bool
     */
    public function isActive()
    {
        return is_object($this->_imageObject);
    }

    /**
     * Upload an image based on the $fileKey
     *
     * @param string $fileKey
     * @param string|null $filename - set a custom filename
     * @return null|string - returns saved filename
     * @throws Exception
     */
    public function uploadImage($fileKey, $filename = null)
    {
        try {
            $uploader = new Varien_File_Uploader($fileKey);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($this->getBaseImagePath());

            return $result['file'];
        }
        catch (Exception $e) {
            if ($e->getCode() != Varien_File_Uploader::TMP_NAME_EMPTY) {
                throw $e;
            }
        }

        return null;
    }

    /**
     * Resize image if is bigger than 1920 px
     *
     * @param string $imagePath
     */
    public function processImage($imagePath)
    {
        $dimensions = getimagesize($imagePath);

        if ($dimensions[0] > 1920 || $dimensions[1] > 1920) {
            $imageObj = new Varien_Image($imagePath);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->keepFrame(TRUE);

            if ($dimensions[0] > $dimensions[1]) {
                $imageObj->resize(1920, $this->_calculateDimension($dimensions[0], $dimensions[1]));
            } else {
                $imageObj->resize($this->_calculateDimension($dimensions[1], $dimensions[0]), 1920);
            }
            $imageObj->save($imagePath);
        }
    }

    /**
     * Calculate directly proportional dimension to resize
     *
     * @param int $bigger
     * @param int $smaller
     * @return int
     */
    private function _calculateDimension($bigger, $smaller)
    {
        $percentage = 1920 / $bigger * 100;
        $dimension = $smaller * $percentage / 100;

        return (int)$dimension;
    }
}
