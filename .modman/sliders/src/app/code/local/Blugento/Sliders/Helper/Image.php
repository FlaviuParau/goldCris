<?php
/**
 * Blugento Sliders
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */
 
class Blugento_Sliders_Helper_Image extends Mage_Core_Helper_Abstract
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
    const IMAGE_FOLDER = 'blugento_sliders';
	
	/**
	 * The folder name used to store images
	 * This is relative to the media directory
	 *
	 * @var const string
	 */
	const TABLET_IMAGE_FOLDER = 'blugento_sliders/tablet';
	
	/**
	 * The folder name used to store images
	 * This is relative to the media directory
	 *
	 * @var const string
	 */
	const MOBILE_IMAGE_FOLDER = 'blugento_sliders/mobile';

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
	 * Retrieve the tablet image URL where images are stored
	 *
	 * @return string
	 */
	public function getBaseTabletImageUrl()
	{
		return Mage::getBaseUrl('media') . self::TABLET_IMAGE_FOLDER . '/';
	}
	
	/**
	 * Retrieve the mobile image URL where images are stored
	 *
	 * @return string
	 */
	public function getBaseMobileImageUrl()
	{
		return Mage::getBaseUrl('media') . self::MOBILE_IMAGE_FOLDER . '/';
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
	 * Retrieve the directory/path where tablet images are stored
	 *
	 * @return string
	 */
	public function getBaseTabletImagePath()
	{
		return Mage::getBaseDir('media') . DS . self::TABLET_IMAGE_FOLDER . DS;
	}
	
	/**
	 * Retrieve the directory/path where mobile images are stored
	 *
	 * @return string
	 */
	public function getBaseMobileImagePath()
	{
		return Mage::getBaseDir('media') . DS . self::MOBILE_IMAGE_FOLDER . DS;
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
	 * Retrieve the full tablet image URL
	 * Null returned if image does not exist
	 *
	 * @param string $image
	 * @return string|null
	 */
	public function getTabletImageUrl($image)
	{
		if ($this->tabletImageExists($image)) {
			return $this->getBaseTabletImageUrl() . $image;
		}
		
		return null;
	}
	
	/**
	 * Retrieve the full mobile image URL
	 * Null returned if image does not exist
	 *
	 * @param string $image
	 * @return string|null
	 */
	public function getMobileImageUrl($image)
	{
		if ($this->mobileImageExists($image)) {
			return $this->getBaseMobileImageUrl() . $image;
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
	 * Retrieve the full tablet image path
	 * Null returned if image does not exist
	 *
	 * @param string $image
	 * @return string|null
	 */
	public function getTabletImagePath($image)
	{
		if ($this->tabletImageExists($image)) {
			return $this->getBaseTabletImagePath() . $image;
		}
		
		return null;
	}
	
	/**
	 * Retrieve the full mobile image path
	 * Null returned if image does not exist
	 *
	 * @param string $image
	 * @return string|null
	 */
	public function getMobileImagePath($image)
	{
		if ($this->mobileImageExists($image)) {
			return $this->getBaseMobileImagePath() . $image;
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
	 * Determine whether the tablet image exists
	 *
	 * @param string $image
	 * @return bool
	 */
	public function tabletImageExists($image)
	{
		return is_file($this->getBaseTabletImagePath() . $image);
	}
	
	/**
	 * Determine whether the mobile image exists
	 *
	 * @param string $image
	 * @return bool
	 */
	public function mobileImageExists($image)
	{
		return is_file($this->getBaseMobileImagePath() . $image);
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
	 * Converts a filename, width and height into it's resized uri path
	 * returned path does not include base path
	 *
	 * @param string $filename
	 * @param int $width = null
	 * @param int $height = null
	 * @return string
	 */
	public function getResizedTabletImageUrl($filename, $width = null, $height = null)
	{
		return $this->getBaseTabletImageUrl() . $this->_getRelativeResizedTabletImagePath($filename, $width, $height);
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
	public function getResizedMobileImageUrl($filename, $width = null, $height = null)
	{
		return $this->getBaseMobileImageUrl() . $this->_getRelativeResizedMobileImagePath($filename, $width, $height);
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
	public function getResizedTabletImagePath($filename, $width = null, $height = null)
	{
		return $this->getBaseTabletImagePath() . $this->_getRelativeResizedTabletImagePath($filename, $width, $height);
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
	public function getResizedMobileImagePath($filename, $width = null, $height = null)
	{
		return $this->getBaseMobileImagePath() . $this->_getRelativeResizedMobileImagePath($filename, $width, $height);
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
            return 'cache' . DS . trim($width . 'x' . $height, 'x') . DS . $filename;
        }

        return $filename;
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
	protected function _getRelativeResizedTabletImagePath($filename, $width = null, $height = null)
	{
		if (!is_null($width) || !is_null($height)) {
			return 'cache' . DS . trim($width . 'x' . $height, 'x') . DS . $filename;
		}
		
		return $filename;
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
	protected function _getRelativeResizedMobileImagePath($filename, $width = null, $height = null)
	{
		if (!is_null($width) || !is_null($height)) {
			return 'cache' . DS . trim($width . 'x' . $height, 'x') . DS . $filename;
		}
		
		return $filename;
	}

    /**
     * Initialize the image object
     * This sets up the image object for resizing and caching
     *
     * @param Blugento_AttributeSplash_Model_Page $page
     * @param string $attribute
     * @return Blugento_AttributeSplash_Helper_Image
     */
    public function init(Blugento_AttributeSplash_Model_Page $page, $attribute = 'image')
    {
        $this->_imageObject = null;
        $this->_forceRecreate = false;
        $this->_filename = null;

        if ($imagePath = $this->getImagePath($page->getData($attribute))) {
            $this->_imageObject = new Varien_Image($imagePath);
            $this->_filename = basename($imagePath);

            $this->keepAspectRatio(true);
        }

        return $this;
    }
	
	/**
	 * Initialize the image object
	 * This sets up the image object for resizing and caching
	 *
	 * @param Blugento_AttributeSplash_Model_Page $page
	 * @param string $attribute
	 * @return Blugento_AttributeSplash_Helper_Image
	 */
	public function initTablet(Blugento_AttributeSplash_Model_Page $page, $attribute = 'tablet_image')
	{
		$this->_imageObject = null;
		$this->_forceRecreate = false;
		$this->_filename = null;
		
		if ($imagePath = $this->getTabletImagePath($page->getData($attribute))) {
			$this->_imageObject = new Varien_Image($imagePath);
			$this->_filename = basename($imagePath);
			
			$this->keepAspectRatio(true);
		}
		
		return $this;
	}
	
	/**
	 * Initialize the image object
	 * This sets up the image object for resizing and caching
	 *
	 * @param Blugento_AttributeSplash_Model_Page $page
	 * @param string $attribute
	 * @return Blugento_AttributeSplash_Helper_Image
	 */
	public function initMobile(Blugento_AttributeSplash_Model_Page $page, $attribute = 'mobile_image')
	{
		$this->_imageObject = null;
		$this->_forceRecreate = false;
		$this->_filename = null;
		
		if ($imagePath = $this->getMobileImagePath($page->getData($attribute))) {
			$this->_imageObject = new Varien_Image($imagePath);
			$this->_filename = basename($imagePath);
			
			$this->keepAspectRatio(true);
		}
		
		return $this;
	}

    /**
     * Resize the image loaded into the image object
     *
     * @param int $width = null
     * @param int $height = null
     * @return string
     */
    public function resize($width = null, $height = null)
    {
        if ($this->isActive()) {
            $cachedFilename = $this->getResizedImagePath($this->_filename, $width, $height);

            if ($this->_forceRecreate || !is_file($cachedFilename)) {
                $this->_imageObject->resize($width, $height);
                $this->_imageObject->save($cachedFilename);
            }

            return $this->getResizedImageUrl($this->_filename, $width, $height);
        }

        return '';
    }
	
	/**
	 * Resize the image loaded into the image object
	 *
	 * @param int $width = null
	 * @param int $height = null
	 * @return string
	 */
	public function resizeTablet($width = null, $height = null)
	{
		if ($this->isActive()) {
			$cachedFilename = $this->getResizedTabletImagePath($this->_filename, $width, $height);
			
			if ($this->_forceRecreate || !is_file($cachedFilename)) {
				$this->_imageObject->resize($width, $height);
				$this->_imageObject->save($cachedFilename);
			}
			
			return $this->getResizedTabletImageUrl($this->_filename, $width, $height);
		}
		
		return '';
	}
	
	/**
	 * Resize the image loaded into the image object
	 *
	 * @param int $width = null
	 * @param int $height = null
	 * @return string
	 */
	public function resizeMobile($width = null, $height = null)
	{
		if ($this->isActive()) {
			$cachedFilename = $this->getResizedMobileImagePath($this->_filename, $width, $height);
			
			if ($this->_forceRecreate || !is_file($cachedFilename)) {
				$this->_imageObject->resize($width, $height);
				$this->_imageObject->save($cachedFilename);
			}
			
			return $this->getResizedMobileImageUrl($this->_filename, $width, $height);
		}
		
		return '';
	}

    /**
     * Keep the frame or add a white space
     *
     * @param bool $val
     * @return Blugento_Sliders_Helper_Image
     */
    public function keepFrame($val)
    {
        if ($this->isActive()) {
            $this->_imageObject->keepFrame($val);
        }

        return $this;
    }

    /**
     * Keep the aspect ratio of an image
     *
     * @param bool $val
     * @return Blugento_Sliders_Helper_Image
     */
    public function keepAspectRatio($val)
    {
        if ($this->isActive()) {
            $this->_imageObject->keepAspectRatio($val);
        }

        return $this;
    }

    /**
     * Don't increase the size of an image, only decrease
     *
     * @param bool $val
     * @return Blugento_Sliders_Helper_Image
     */
    public function constrainOnly($val)
    {
        if ($this->isActive()) {
            $this->_imageObject->constrainOnly($val);
        }

        return $this;
    }

    /**
     * Determine whether to recreate image that already exists
     *
     * @param bool $val
     * @return Blugento_Sliders_Helper_Image
     */
    public function forceRecreate($val)
    {
        if ($this->isActive()) {
            $this->_forceRecreate = $val;
        }

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
	 * Upload an image based on the $fileKey
	 *
	 * @param string $fileKey
	 * @param string|null $filename - set a custom filename
	 * @return null|string - returns saved filename
	 * @throws Exception
	 */
	public function uploadTabletImage($fileKey, $filename = null)
	{
		try {
			$uploader = new Varien_File_Uploader($fileKey);
			$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
			$uploader->setAllowRenameFiles(true);
			$result = $uploader->save($this->getBaseTabletImagePath());
			
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
	 * Upload an image based on the $fileKey
	 *
	 * @param string $fileKey
	 * @param string|null $filename - set a custom filename
	 * @return null|string - returns saved filename
	 * @throws Exception
	 */
	public function uploadMobileImage($fileKey, $filename = null)
	{
		try {
			$uploader = new Varien_File_Uploader($fileKey);
			$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
			$uploader->setAllowRenameFiles(true);
			$result = $uploader->save($this->getBaseMobileImagePath());
			
			return $result['file'];
		}
		catch (Exception $e) {
			if ($e->getCode() != Varien_File_Uploader::TMP_NAME_EMPTY) {
				throw $e;
			}
		}
		
		return null;
	}
}
