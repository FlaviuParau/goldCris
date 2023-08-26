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

abstract class Blugento_DesignCustomiser_Model_Layout_Variable_Abstract extends Varien_Object
{
    /**
     * Variable id
     * @var string
     */
    protected $_id = '';

    /**
     * Variable title
     * @var string
     */
    protected $_title = '';

    /**
     * Variable Scss syntax
     * @var string
     */
    protected $_scss = '';

    /**
     * Variable description
     * @var string
     */
    protected $_description = '';

    /**
     * Variable help link
     * @var string
     */
    protected $_help = '';

    /**
     * Variable options
     * @var array
     */
    protected $_options = array();

    /**
     * Variable default value
     * @var string
     */
    protected $_default = '';

    /**
     * Default variable admin element renderer class
     * @var string
     */
    protected $_defaultRenderClass = 'blugento_designcustomiser/adminhtml_form_renderer_layout_variable_default';

    /**
     * Variable admin element renderer class
     * @var string
     */
    protected $_rendererClass = '';

    /**
     * Is variable using custom set element renderer class
     * @var boolean
     */
    protected $_useCustomRendererClass = false;

    /**
     * Variable value to save
     * @var string
     */
    protected $_saveValue = '';

    /**
     * Variable parent variable id
     * @var string
     */
    protected $_parentId = false;

    /**
     * Variable fieldset
     */
    protected $_fieldset = '';

    /**
     * Variable generates a layout update or not
     * @var int
     */
    protected $_layout = 0;

    /**
     * Variable disables a module or not
     * @var string
     */
    protected $_disable_module = '';

    /**
     * Variable tab
     * @var string
     */
    protected $_tab = '';

    /**
     * Variable input type form
     */
    protected $_inputTypeForm = 'select';

    /**
     * Translation prefix
     * @var string
     */
    protected $_translationPrefix = 'layout';

    /**
     * Construct
     * @param mixed $variableData
     */
    public function __construct($variableData)
    {
        parent::__construct();
        $this->_init($variableData);

    }

    /**
     * Initialize data
     * @param type $variableData
     * @return Blugento_DesignCustomiser_Model_Layout_Variable_Abstract
     */
    protected function _init($variableData)
    {
        if ($variableData instanceof Varien_Simplexml_Element) {
            $this->_initFromXml($variableData);
        }
        return $this;
    }


    /**
     * Set properties data from XML node variable
     * @param string $rendererClass
     * @param Varien_Simplexml_Element $elementVariableXml
     * @return Blugento_DesignCustomiser_Model_Layout_Variable_Abstract
     */
    protected function _initFromXml(Varien_Simplexml_Element $elementVariableXml)
    {
        $this->_rendererClass   = str_replace('default', $this->getType(), $this->_defaultRenderClass);
        $this->_id              = (string)$elementVariableXml->id;
        $this->_title           = (string)$elementVariableXml->title;
        $this->_scss            = (string)$elementVariableXml->scss;
        $this->_description     = (string)$elementVariableXml->description;
        $this->_help            = (string)$elementVariableXml->help;
        $this->_options         = (array)$elementVariableXml->options;
        $this->_default         = (string)$elementVariableXml->default;
        $this->_fieldset        = (string)$elementVariableXml->fieldset;
        $this->_layout          = (int)$elementVariableXml->layout;
        $this->_disable_module  = (string)$elementVariableXml->disable_module;
        $this->_tab             = (string)$elementVariableXml->tab;
        return $this;
    }

    /**
     * Get Renderer Class
     * @return string
     */
    final public function getRendererClass()
    {
        $block = $this->_rendererClass;

        if (strpos($block, '/')!==false) {
            $block = Mage::getConfig()->getBlockClassName($this->_rendererClass);
        }

        if ($this->_useCustomRendererClass && (class_exists($block, false) || mageFindClassFile($block))) {
            return $this->_rendererClass;
        }

        if (class_exists($block, false) || mageFindClassFile($block)) {
            return $this->_rendererClass;
        }

        $blockDefault = Mage::getConfig()->getBlockClassName($this->_defaultRenderClass);

        if (class_exists($blockDefault, false) || mageFindClassFile($blockDefault)) {
            return $this->_defaultRenderClass;
        }

        return '';
    }

    /**
     * Set Renderer Class
     * @return string
     */
    final public function setRendererClass($rendererClass)
    {
        $this->_rendererClass = $rendererClass;
        $this->_useCustomRendererClass = true;
        return $this;
    }

    /**
     * Get Id
     * @return string
     */
    public function getId()
    {
        if (is_string($this->_id)) {
            return $this->_id;
        }

        return '';
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        if (is_string($this->_title)) {
            return $this->_title;
        }

        return '';
    }

    /**
     * Get SCSS format
     * @return string
     */
    public function getScss()
    {
        if (is_string($this->_scss)) {
            return $this->_scss;
        }

        return '';
    }

        /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        if (is_string($this->_description)) {
            return $this->_description;
        }

        return '';
    }

    /**
     * Get help
     * @return string
     */
    public function getHelp()
    {
        if (is_string($this->_help)) {
            return $this->_help;
        }

        return '';
    }

    /**
     * Get default
     * @return string
     */
    public function getDefault()
    {
        if (is_string($this->_default)) {
            return $this->_default;
        }

        return '';
    }

    /**
     * Get options
     * @return array
     */
    public function getOptions()
    {
        if (is_array($this->_options)) {
            return $this->_options;
        }

        return array();
    }

    /**
     * Get layout
     * @return int
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Get disable module
     * @return string
     */
    public function getDisableModule()
    {
        return (string) $this->_disable_module;
    }

    /**
     * Get tab
     * @return string
     */
    public function getTab()
    {
        if ($this->_tab) {
            return $this->_tab;
        }
        return 'Other';
    }

    /**
     * Get variable type
     * return string
     */
    abstract public function getType();

    /**
     * The input form type for Magento form usage
     * @return string
     */
    final public function getInputTypeForm()
    {
        return $this->_inputTypeForm;
    }

    /**
     * Get Variable Value
     * @param Blugento_DesignCustomiser_Model_Layout_Save_Interface $valuesFile
     * @return string
     */
    final public function getValue(Blugento_DesignCustomiser_Model_Layout_Save_Interface $valuesFile)
    {
        $value = $valuesFile->getVariableValue($this);

        if (empty($value)) {
            $value = $this->getDefault();
        }

        return $value;
    }

    /**
     * Validate save value
     * @return boolean
     */
    abstract public function validate();

    /**
     * Set Save Value
     * @param string $value
     * @return Blugento_DesignCustomiser_Model_Layout_Variable_Abstract
     */
    final public function setSaveValue($value)
    {
        $this->_saveValue = $value;
        return $this->_saveValue;
    }

    /**
     * Get variable save value
     * @return string
     */
    public function getSaveValue()
    {
        return $this->_saveValue;
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
     * Get translation prefix
     * @return string
     */
    public function getTranslationPrefix()
    {
        return $this->_translationPrefix;
    }
}
