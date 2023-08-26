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

abstract class Blugento_DesignCustomiser_Model_Scss_Variable_Image_Abstract
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
{
    /**
     * Variable size
     * @var string
     */
    protected $_size = '';

    /**
     * Variable retina
     * @var string
     */
    protected $_retina = '';

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
     * Default variable admin element renderer class
     * @var string
     */
    protected $_defaultRenderClass = 'blugento_designcustomiser/adminhtml_form_renderer_image_default';

    /**
     * Image skin folder
     * @var string
     */
    protected $_imageSkinDir = 'images';

    /**
     * Image upload width
     * @var int
     */
    public $uploadWidth = null;

    /**
     * Image upload height
     * @var int
     */
    public $uploadHeight = null;

    /**
     * Variable fieldset
     */
    protected $_fieldset = '';

    /**
     * Image defined types
     * @var string
     */
    protected $_xmlType = null;

    /**
     * Uploaded image type, used for multiple types
     * @var string
     */
    public $uploadType = null;

    /**
     * Translation prefix
     * @var string
     */
    protected $_translationPrefix = 'image';


    /**
     * Set properties data from XML node variable
     * @param string $rendererClass
     * @param Varien_Simplexml_Element $elementVariableXml
     * @return Blugento_DesignCustomiser_Model_Scss_Image_Abstract
     */
    protected function _initFromXml(Varien_Simplexml_Element $elementVariableXml)
    {
        $this->_rendererClass   = str_replace('default', $this->getType(), $this->_defaultRenderClass);
        $this->_id              = (string)$elementVariableXml->id;
        $this->_scss            = (string)$elementVariableXml->scss;
        $this->_title           = (string)$elementVariableXml->title;
        $this->_description     = (string)$elementVariableXml->description;
        $this->_size            = (string)$elementVariableXml->size;
        $this->_retina          = (string)$elementVariableXml->retina;
        $this->_help            = (string)$elementVariableXml->help;
        $this->_default         = (string)$elementVariableXml->default;
        $this->_fieldset        = (string)$elementVariableXml->fieldset;
        $this->_xmlType         = (string)$elementVariableXml->type;
        return $this;
    }

    /**
     * Get Size
     * @return string
     */
    public function getSize()
    {
        if (is_string($this->_size)) {
            return $this->_size;
        }
        return '';
    }

    /**
     * Get Retina
     * @return string
     */
    public function getRetina()
    {
        if (is_string($this->_retina)) {
            return $this->_retina;
        }
        return '';
    }

    /**
     * Get image url
     * @param $imageName string
     * @return string
     */
    public function getUrl($imageName)
    {
        $xmlSaveValues = Mage::getSingleton('blugento_designcustomiser/scss_save_image_xml');
        $value = $xmlSaveValues->getVariableValue($this);
        if (!$value) {
            $value = $imageName;
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');

        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            Mage::helper('blugento_designcustomiser')->getScssDefinitionStore()
        );

        $tempUrl = $this->_srcSkinDir . '/' . $this->_inputImageFolder . '/' . $value;

        $url = Mage::getDesign()->getSkinUrl($tempUrl);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $url;
    }

    /**
     * Validate save value
     * @return boolean
     */
    public function validate()
    {
        return true;
    }

    /**
     * Check if image ratio is the same as the image declared in specs_images.xml
     * @param array $ratioArray
     * @return bool
     */
    public function validateRatio($ratioArray)
    {
        // Skip validtion for SVG images
        if ($this->getType() == 'svg') {
            return true;
        }

        if (empty($ratioArray[0]) && empty($ratioArray[1])) {
            return true;
        }

        $uploadWidth  = $this->uploadWidth;
        $uploadHeight = $this->uploadHeight;

        if ($uploadWidth === null || $uploadHeight === null) {
            $this->setUploadSizes();
        }
        if ($uploadWidth == 0 || $uploadHeight == 0) {
            Mage::getSingleton('adminhtml/session')->addError('Uploaded image error.');
            return false;
        }

        $ratioWidth  = (int) $ratioArray[0];
        $ratioHeight = isset($ratioArray[1]) ? (int) $ratioArray[1] : 0;

        if ($ratioWidth > 0 && $ratioHeight > 0) {
            $imageXmlRatio = $ratioWidth / $ratioHeight;
            $uploadedImageRatio = $uploadWidth / $uploadHeight;
            if (abs($uploadedImageRatio - $imageXmlRatio) <= 0.2) {
                return true;
            }
        } else
        if ($ratioHeight > 0) {
            if ($ratioHeight <= $uploadHeight) {
                return true;
            }
        } else
        if ($ratioWidth > 0) {
            if ($ratioWidth <= $uploadWidth) {
                return true;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError('The image you have uploaded does not have the same ratio as the original one: ' . $ratioWidth . 'x' . $ratioHeight);
        return false;
    }

    /**
     * Set width and height of the uploaded tmp image
     *
     * @return Blugento_DesignCustomiser_Model_Scss_Variable_Image_Abstract
     */
    public function setUploadSizes()
    {
        $dataUpload = $this->getSaveValue();
        list($this->uploadWidth, $this->uploadHeight) = @getimagesize($dataUpload['tmp_name']);

        $this->uploadWidth  = (int) $this->uploadWidth;
        $this->uploadHeight = (int) $this->uploadHeight;

        return $this;
    }

    /**
     * Get width and height for use in html
     * @return string
     */
    public function getHtmlWidthHeight()
    {
        if (!is_string($this->_size)) {
            return '';
        }
        $arr = explode('x', $this->_size);
        if (count($arr) != 2) {
            return '';
        }
        $html   = '';
        $width  = trim($arr[0]);
        $height = trim($arr[1]);
        if ($width && $width != '*') {
            $html = 'width="' . $width . '"';
        }
        if ($height && $height != '*') {
            $html .= ' height="' . $height . '"';
        }

        return $html;
    }

    /**
    * Get fieldset
    * @return string
    */

    public function getFieldset()
    {
        if ($this->_fieldset) {
            return $this->_fieldset;
        }
        return 'fieldset/general';
    }

    /**
     * Get Type
     * @return string
     */
    public function getXMLType()
    {
        if (is_string($this->_xmlType)) {
            return $this->_xmlType;
        }
        return '';
    }

    /**
     * Get Type as readable string
     * @return string
     */
    public function getXMLTypeString()
    {
        if (is_string($this->_xmlType)) {
            return implode(', ', explode('|', $this->_xmlType));
        }
        return '';
    }

    /**
     * Validates uploaded image type against specs XML defined types
     * @param string $uploadType
     * @return bool
     */
    public function validateType($uploadType)
    {
        $arr = array_filter(explode('|', $this->getXMLType()));
        return in_array($uploadType, $arr);
    }

    /**
     * Get translation prefix
     * @return string
     */
    public function getTranslationPrefix()
    {
        return $this->_translationPrefix;
    }
}
