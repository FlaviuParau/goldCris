<?php
/**
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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Model_Scss_Save_Image extends Varien_Object
    implements Blugento_DesignCustomiser_Model_Scss_Save_Interface
{
    /**
     * Variable input image folder
     */
    protected $_inputImageFolder = 'images';

    /**
     * Src folder
     * @var string
     */
    protected $_srcSkinDir = 'blugento';

    /**
     * Default preset folder
     * @var string
     */
    protected $_defaultPresetDir = 'default';

    /**
     * Presets folder
     * @var string
     */
    protected $_presetsDir = 'presets';

    /**
     * Save the collection data in file
     * @param Varien_Data_Collection $collection
     * @return array Success and error
     */
    public function save(Varien_Data_Collection $collection)
    {
        $success = array();
        $error   = array();

        foreach ($collection as $variable) {
            /**
             * @var $variable Blugento_DesignCustomiser_Model_Scss_Save_Image
             */
            $variableId = $variable->getId();
            try {
                $uploader = new Mage_Core_Model_File_Uploader($variableId);
                $type = $this->_getVariableType($variable);
                if (!$type) {
                    $error[$variableId] = $variable->getTitle();
                    continue;
                }
                $uploader->setAllowedExtensions(array($type));
                $path = $this->_getImagesPath();
                $nameArray = array_filter(explode('.', $variable->getDefault()));

                // Delete old image and resized images
                $this->unlinkOldImages($variable);

                // Save new image
                $uploader->save($path, $nameArray[0] . '.' . $type);
                chmod($path . '/' . $nameArray[0] . '.' . $type, 0755);

                // Generate @1x and @2x images - only for non SVG files
                if ($type != 'svg') {
                    $this->_resize($variable, $path, 1);
                    $this->_resize($variable, $path, 2);
                    //$this->_resize($variable, $path, 1, $variable->getDefault());
                }

                $success[$variableId] = $variable->getTitle();
            } catch (Exception $e) {
                Mage::logException($e);
                $error[$variableId] = $variable->getTitle() . ': ' . $e->getMessage();
            }
        }

        return array(
            'success' => $success,
            'error'   => $error
        );
    }

    /**
     * Check image resize
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable
     * @param int $multiple
     * @return bool
     */
    private function _canResize(Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable, $multiple = 1)
    {
        $type = $this->_getVariableType($variable);
        if (!$type || $type == 'svg' || $variable->getRetina() == 'false') {
            return false;
        }

        if ($variable->uploadWidth === null || $variable->uploadHeight === null) {
            $variable->setUploadSizes();
        }
        if ($variable->uploadWidth == 0 || $variable->uploadHeight == 0) {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('blugento_designcustomiser')->__('Uploaded image: cannot determine uploaded image size.'));
            return false;
        }

        $xmlImageArray  = explode('x', $variable->getSize());
        $multipleWidth  = @intval($xmlImageArray[0]) * $multiple;
        $multipleHeight = @intval($xmlImageArray[1]) * $multiple;

        if (($multipleWidth > 0 && $multipleWidth > $variable->uploadWidth) || ($multipleHeight > 0 && $multipleHeight > $variable->uploadHeight)) {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('blugento_designcustomiser')->__('Uploaded image %s is too small for dimensions %sx.', $variable->getDefault(), $multiple));
            return false;
        }

        return true;
    }

    /**
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable
     * @param Mage_Core_Model_File_Uploader $uploader
     * @param string $path
     * @param int $multiple
     * @param string|null $destinationImageName
     * @return bool
     */
    private function _resize(Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable, $path, $multiple, $destinationImageName = null)
    {
        $type = $this->_getVariableType($variable);
        if (!$type || $type == 'svg') {
            return false;
        }

        $xmlImageArray = explode('x', $variable->getSize());
        $multipleWidth = @intval($xmlImageArray[0]) * $multiple;
        $multipleHeight = @intval($xmlImageArray[1]) * $multiple;
        if ($this->_canResize($variable, $multiple)) {
            $nameArray = array_filter(explode('.', $variable->getDefault()));
            $imageName = $destinationImageName !== null ? $destinationImageName : $this->_getResizeName($nameArray[0], $multiple, $variable);

            if ($multipleWidth == 0) {
                $multipleWidth = null;
            }
            if ($multipleHeight == 0) {
                $multipleHeight = null;
            }

            $image = new Varien_Image($path . DS . $variable->getDefault());
            $image->constrainOnly(false);
            $image->keepFrame(false);
            $image->keepAspectRatio(true);
            $image->keepTransparency(true);
            $image->resize($multipleWidth, $multipleHeight);
            $image->save($path . DS . $imageName);

            return true;
        }

        return false;
    }

    /**
     * Get Variable Value
     * @TODO: Get Value from scss file
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable
     * @return string
     */
    public function getVariableValue(Blugento_DesignCustomiser_Model_Scss_Variable_Abstract $variable)
    {
        $value = '';

        return $value;
    }

    /**
     * Reset image
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Image_Abstract $variable
     * @param string|null $template
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Image $this
     */
    public function resetImage(Blugento_DesignCustomiser_Model_Scss_Variable_Image_Abstract $variable, $template = null)
    {
        $themeDefaultPath = $template !== null ? $this->_getThemeTemplatePath($template) : $this->_getThemeDefaultPath();
        $imageDefaultPath = $themeDefaultPath . $this->_inputImageFolder . DS . $variable->getDefault();
        if (!file_exists($imageDefaultPath)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blugento_designcustomiser')->__('Image %s does not exist in default theme folder (%s).', $variable->getDefault(), $themeDefaultPath . $this->_inputImageFolder));
            return $this;
        }

        $imagesPath = $this->_getImagesPath();
        $imageThemePath = $imagesPath . $variable->getDefault();

        $this->_replaceImage($imageDefaultPath, $imageThemePath, $variable->getDefault(), $template);

        // Copy the @1x and @2x images also - only for non SVG files
        $variableType = $this->_getVariableType($variable);
        if (!$variableType || $variableType == 'svg') {
            return $this;
        }
        $resize = array(1, 2);
        $variableDefault = $variable->getDefault();
        foreach ($resize as $multiple) {
            $nameArray = array_filter(explode($variableType, $variableDefault));
            $imageName = $this->_getResizeName($nameArray[0], $multiple, $variable);

            $imageDefaultPath = $themeDefaultPath . $this->_inputImageFolder . DS . $imageName;
            $imageThemePath = $imagesPath . $imageName;

            if (!file_exists($imageDefaultPath)) {
                @unlink($imageThemePath);
                continue;
            }
            if (!$this->_replaceImage($imageDefaultPath, $imageThemePath, $imageName, $template)) {
                @unlink($imageThemePath);
            }
        }

        return $this;
    }

    /**
     * Copy a image from source to destination and set success or error message
     * @param string $sourcePath
     * @param string $destinationPath
     * @param string $imageFilename
     * @param string|null $template
     * @return bool
     */
    private function _replaceImage($sourcePath, $destinationPath, $imageFilename, $template = null)
    {
        if ($template === null) {
            $template = 'default';
        }
        if (!@copy($sourcePath, $destinationPath)) {
            if (@chmod($sourcePath, 0666) && @chmod($destinationPath, 0666)) {
                if (!@copy($sourcePath, $destinationPath)) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blugento_designcustomiser')->__('Error occurred while resetting image %s to %s. Please try again later.', $imageFilename, $template));
                    return false;
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blugento_designcustomiser')->__('Error occurred while resetting image %s to %s. Please try again later.', $imageFilename, $template));
                return false;
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blugento_designcustomiser')->__('Image %s successfully restored to %s.', $imageFilename, $template));

        return true;
    }

    /**
     * Get image folder path
     * @return string
     */
    private function _getImagesPath()
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            Mage::helper('blugento_designcustomiser')->getScssDefinitionStore()
        );

        $finalPath = Mage::getDesign()->getSkinBaseDir();
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        if ($finalPath[strlen($finalPath)-1] != DS) {
            $finalPath .= DS;
        }

        $finalPath .= $this->_srcSkinDir . DS . $this->_inputImageFolder . DS;

        return $finalPath;
    }

    /**
     * Get image theme default path
     * @return string
     */
    private function _getThemeDefaultPath()
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            Mage::helper('blugento_designcustomiser')->getScssDefinitionStore()
        );

        $finalPath = Mage::getDesign()->getSkinBaseDir();
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        if ($finalPath[strlen($finalPath)-1] != DS) {
            $finalPath .= DS;
        }

        $finalPath .= $this->_presetsDir . DS . $this->_defaultPresetDir . DS;

        return $finalPath;
    }

    /**
     * Get image theme default path
     * @return string
     */
    private function _getThemeTemplatePath($template)
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            Mage::helper('blugento_designcustomiser')->getScssDefinitionStore()
        );

        $finalPath = Mage::getDesign()->getSkinBaseDir();
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        if ($finalPath[strlen($finalPath)-1] != DS) {
            $finalPath .= DS;
        }

        $finalPath .= $this->_presetsDir . DS . $template . DS;

        return $finalPath;
    }

    /**
     * Builds the @x filename
     * @param string $name
     * @param int $multiple
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Image_Abstract $variable
     * @param string $type
     * @return string
     */
    protected function _getResizeName($name, $multiple, $variable, $type = null)
    {
        if (!$type) {
            $type = $this->_getVariableType($variable);
        }
        return substr_replace($name, "", -1) . '@' . $multiple . 'x.' . $type;
    }

    /**
     * Determine variable type, consider multiple allowed types also
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Image_Abstract $variable
     * @return mixed
     */
    protected function _getVariableType($variable)
    {
        $type = $variable->getType();
        if ($type == 'default') {
            $type = $variable->uploadType;
        }
        return $type;
    }

    /**
     * Deletes old resized images, used for multiple allowed image types, force delete image even for single image extension
     * @param Blugento_DesignCustomiser_Model_Scss_Variable_Image_Abstract $variable
     * @param bool $forceSingle
     * @return bool
     */
    protected function unlinkOldImages($variable, $forceSingle = false)
    {
        $xmlType = $variable->getXMLType();
        if (!$xmlType) {
            return false;
        }

        $nameArray = array_filter(explode('.', $variable->getDefault()));
        $name = $nameArray[0];
        $path = $this->_getImagesPath();

        $arr = explode('|', $xmlType);
        if (count($arr) == 1 && $forceSingle) {
            return false;
        }

        foreach ($arr as $type) {
            @unlink($path . $name . '.' . $type);

            $imageName = $this->_getResizeName($name, 1, $variable, $type);
            @unlink($path . $imageName);

            $imageName = $this->_getResizeName($name, 2, $variable, $type);
            @unlink($path . $imageName);
        }

        return true;
    }
}
