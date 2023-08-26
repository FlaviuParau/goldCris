<?php

class Blugento_Swatches_Helper_Image extends Mage_Core_Helper_Abstract
{
	/**
	 * Storage for image object, used for resizing images
	 *
	 * @var null/Varien_Image
	 */
	protected $_imageObject = null;
	
	/**
	 * Image object properties
	 */
	protected $_keepFrame;
	protected $_backgroundColor;
	protected $_keepAspectRatio;
	protected $_constrainOnly;
	
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
	 * @var string
	 */
	const IMAGE_FOLDER = 'blugento_swatches';
	
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
	 * Converts an image, width and height into it's resized uri path
	 * returned path does not include base path
	 *
	 * @param string $image
	 * @param int $width = null
	 * @param int $height = null
	 * @return string
	 */
	public function getResizedImageUrl($image, $width = null, $height = null)
	{
		return $this->getBaseImageUrl() . $this->_getRelativeResizedImagePath($image, $width, $height);
	}
	
	/**
	 * Converts a image, width and height into it's resized path
	 * returned path does not include base path
	 *
	 * @param string $image
	 * @param int $width = null
	 * @param int $height = null
	 * @return string
	 */
	public function getResizedImagePath($image, $width = null, $height = null)
	{
		return $this->getBaseImagePath() . $this->_getRelativeResizedImagePath($image, $width, $height);
	}
	
	/**
	 * Get the image cached path
	 * returned path does not include base path
	 *
	 * @param string $image
	 * @param int $width = null
	 * @param int $height = null
	 * @return string
	 */
	protected function _getRelativeResizedImagePath($image, $width = null, $height = null)
	{
		if (!is_null($width) || !is_null($height)) {
			return 'cache' . '/' . trim($width . 'x' . $height, 'x') . '/' . $image;
		}
		
		return $image;
	}
	
	/**
	 * Initialize the image object
	 * This sets up the image object for resizing and caching
	 *
	 * @param string $image
	 * @return Blugento_Swatches_Helper_Image
	 */
	public function init($image)
	{
		$this->_filename = null;
		
		if ($imagePath = $this->getImagePath($image)) {
			$this->_filename = basename($imagePath);
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
		if ($this->_filename) {
			$cachedFilename = $this->getResizedImagePath($this->_filename, $width, $height);
			
			if (!is_file($cachedFilename)) {
				$this->_imageObject = new Varien_Image($this->getImagePath($this->_filename));
				
				$this->_imageObject->constrainOnly($this->_constrainOnly);
				$this->_imageObject->keepAspectRatio($this->_keepAspectRatio);
				$this->_imageObject->keepFrame($this->_keepFrame);
				$this->_imageObject->backgroundColor($this->_backgroundColor);
				$this->_imageObject->resize($width, $height);
				$this->_imageObject->save($cachedFilename);
			}
			
			return $this->getResizedImageUrl($this->_filename, $width, $height);
		}
		
		return  null;
	}
	
	/**
	 * Keep the frame or add a white space
	 *
	 * @param bool $val
	 * @return Blugento_Swatches_Helper_Image
	 */
	public function keepFrame($val)
	{
		$this->_keepFrame = $val;
		
		return $this;
	}
	
	/**
	 * Get the background color for image
	 *
	 * @param array $val
	 * @return Blugento_Swatches_Helper_Image
	 */
	public function backgroundColor($val)
	{
		$this->_backgroundColor = $val;
		
		return $this;
	}
	
	/**
	 * Keep the aspect ratio of an image
	 *
	 * @param bool $val
	 * @return Blugento_Swatches_Helper_Image
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
	 * @return Blugento_Swatches_Helper_Image
	 */
	public function constrainOnly($val)
	{
		$this->_constrainOnly = $val;
		return $this;
	}
}
