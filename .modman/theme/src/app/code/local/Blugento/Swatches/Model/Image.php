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
 * @package     Blugento_Swatches
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Swatches_Model_Image extends Mage_Core_Model_Abstract
{
    protected $_extensions = array('jpg', 'jpeg', 'png');
	
	/**
	 * Blugento Swatches Helper Data
	 *
	 * @var Blugento_Swatches_Helper_Data _helper
	 */
    protected $_helper;
    
    public function __construct()
    {
    	$this->_helper = Mage::helper('blugento_swatches');
    	
    	return parent::__construct();
    }
	
	/**
     * Valide image.
     *
     * @param array $image
     * @return bool
     */
    public function validateImage($image)
    {
        $result['error'] = false;
        if ($image['size'] > 524288) {
            $result['error'] = true;
            $result['message'] = Mage::helper('blugento_swatches')->__('The image must be smaller than 512kb.');
        }

        if (!$this->_validateExtension($image['name'])) {
            $result['error'] = true;
            $result['message'] = Mage::helper('blugento_swatches')->__('Invalid extension. Allowed extensions are: jpg, jpeg, png.');
        }

        if ($image['error']) {
            $result['error'] = true;
            $result['message'] = Mage::helper('blugento_swatches')->__('The image file is invalid! Please upload another image.');
        }

        $dimensions = getimagesize($image['tmp_name']);
        if ($dimensions[0] > $this->_helper->fallbackImageWidth() || $dimensions[1] > $this->_helper->fallbackImageHeight()) {
            $result['error'] = true;
            $result['message'] = Mage::helper('blugento_swatches')->
            __('Image dimensions must be maximum %s x %s px', $this->_helper->fallbackImageWidth(), $this->_helper->fallbackImageHeight());
        }

        return $result;
    }

    /**
     * Upload image in media directory
     *
     * @param array $image
     * @param string $field
     * @return mixed
     */
    public function uploadImage($image, $field)
    {
        try {
            $imageName = str_replace(' ', '_', $image['name']);

            $width = $this->_helper->getHoverSwatchImageWidth();
            $height = $this->_helper->getHoverSwatchImageHeight();

            $uploader = new Varien_File_Uploader($field);
            $uploader->setAllowedExtensions($this->_extensions);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);

            $path = Mage::getBaseDir('media') . DS . 'blugento_swatches';

            if (!file_exists($path)) {
                mkdir($path);
            }

            $uploader->save($path, $imageName);

            $dimensions = getimagesize($path . DS . $imageName);

            /** key 0 is width -- key 1 is height */
            if ($dimensions[0] > $width || $dimensions[1] > $height) {
                $this->_resizeImage($path . DS . $imageName, $width, $height);
            }

            return 'blugento_swatches' . DS . $imageName;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    /**
     * Remove image
     *
     * @param string $imagePath
     */
    public function removeImage($imagePath)
    {
        $path = Mage::getBaseDir('media') . DS . $imagePath;
        unlink($path);
    }

    /**
     * Validate image extension.
     *
     * @param string $imageName
     * @return bool
     */
    private function _validateExtension($imageName)
    {
        $extension = explode('.', $imageName);
        $extension = $extension[count($extension) - 1];

        $validation = false;
        if (in_array(strtolower($extension), $this->_extensions)) {
            $validation = true;
        }

        return $validation;
    }

    /**
     * Resize image to 30x15.
     *
     * @param string $imagePath
     * @param int $width
     * @param int $height
     */
    private function _resizeImage($imagePath, $width, $height)
    {
        $vImage = new Varien_Image($imagePath);
        $vImage->constrainOnly(true);
        $vImage->keepFrame(true);
        $vImage->keepTransparency(true);
        $vImage->keepAspectRatio(true);
        $vImage->backgroundColor(array(255,255,255));
        $vImage->resize($width, $height);
        $vImage->save($imagePath);
    }
}