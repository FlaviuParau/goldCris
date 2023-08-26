<?php
/**
 * Helper class
 * Class Blugento_DesignCustomiser_Helper_Data
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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * System config path for scss definition file extension
     * @var string
     */
    protected $_scssDefinitionFileExtension = 'blugento_designcustomiser/scss/definition_file_extension';
    
    /**
     * System config path for scss definition file name
     * @var string
     */
    protected $_scssDefinitionFileName = 'blugento_designcustomiser/scss/definition_filename';
    
    /**
     * System config path for scss definition directory place
     * @var string
     */
    protected $_scssDefinitionDirectory = 'blugento_designcustomiser/scss/definition_directory_theme';
    
    /**
     * System config path for scss definition store theme
     * @var string
     */
    protected $_scssDefinitionStore = 'blugento_designcustomiser/scss/definition_store';
    
    /**
     * Default variable model
     * @var string 
     */
    protected $_defaultVariableModel = 'blugento_designcustomiser/scss_variable_default';
    
    /**
     * Default definition Scss model path
     * @var string 
     */
    protected $_defaultDefinitionScssModel = 'blugento_designcustomiser/scss_definition';

    /**
     * Default template model
     * @var string
     */
    protected $_defaultTemplateModel = 'blugento_designcustomiser/template_item';
    
    /**
     * Allowed variable types
     * @var array 
     */
    protected $_allowedVariableTypes = array();
    
    /**
     * Allowed product types config node
     * @var string 
     */
    protected $_globalNodeAllowProductTypes = 'global/scss/variable/allowed_types';
    
    /**
     * Default file name for definition scss file
     * @var string 
     */
    protected $_defaultFileName = 'specs';

    /**
     * Default definition final css model path
     * @var string
     */
    protected $_defaultDefinitionFinalCssModel = 'blugento_designcustomiser/file_definition';

    /**
     * System config path for final css definition file extension
     * @var string
     */
    protected $_finalCssDefinitionFileExtension = 'blugento_designcustomiser/css/definition_file_extension';

    /**
     * System config path for final css definition file name
     * @var string
     */
    protected $_finalCssDefinitionFileName = 'blugento_designcustomiser/css/definition_filename';

    /**
     * System config path for css definition store theme
     * @var string
     */
    protected $_finalCssDefinitionStore = 'blugento_designcustomiser/css/definition_store';

    /**
     * Default file name for definition final css file
     * @var string
     */
    protected $_defaultFinalCssFileName = 'final';

    /**
     * System config path for templates definition store theme
     * @var string
     */
    protected $_templateDefinitionDirectoryName = 'blugento_designcustomiser/template/definition_directory';

    /**
     * Default directory name for templates
     * @var string
     */
    protected $_defaultTemplateDirectoryName = 'presets';

    /**
     * Default definition grunt logs model path
     * @var string
     */
    protected $_defaultDefinitionGruntLogsModel = 'blugento_designcustomiser/file_definition';

    /**
     * Default file name for grunt logs file
     * @var string
     */
    protected $_defaultGruntLogsFileName = 'grunt.log';

    /**
     * Default file name for grunt images logs file
     * @var string
     */
    protected $_defaultGruntLogsImageFileName = 'grunt.imagecron.log';

    /**
     * System config path for grunt logs file name
     * @var string
     */
    protected $_gruntLogsDefinitionFileName = 'blugento_designcustomiser/grunt/logs_filename';

    /**
     * System config path for grunt images logs file name
     * @var string
     */
    protected $_gruntLogsImageDefinitionFileName = 'blugento_designcustomiser/grunt/logs_images_filename';

    /**
     * System config path for scss definition directory place
     * @var string
     */
    protected $_gruntLogsDefinitionDirectory = 'blugento_designcustomiser/grunt/logs_directory';

    /**
     * System config path for grunt logs store theme
     * @var string
     */
    protected $_gruntLogsDefinitionStore = 'blugento_designcustomiser/grunt/definition_store';

    /**
     * Variable auto value; will be skipped from sass file
     * @var string
     */
    private $_autoValue = 'auto';

    /**
     * System config path for layout definition store theme
     * @var string
     */
    protected $_layoutDefinitionStore = 'blugento_designcustomiser/layout/definition_store';

    /**
     * Default file name for definition layout file
     * @var string
     */
    protected $_defaultLayoutFileName = 'specs-layout';

    /**
     * System config path for layout definition file name
     * @var string
     */
    protected $_layoutDefinitionFileName = 'blugento_designcustomiser/layout/definition_filename';

    /**
     * System config path for layout definition directory place
     * @var string
     */
    protected $_layoutDefinitionDirectory = 'blugento_designcustomiser/layout/definition_directory_theme';

    /**
     * System config path for layout definition file extension
     * @var string
     */
    protected $_layoutDefinitionFileExtension = 'blugento_designcustomiser/layout/definition_file_extension';

    /**
     * Default definition Layout model path
     * @var string
     */
    protected $_defaultDefinitionLayoutModel = 'blugento_designcustomiser/layout_definition';

    /**
     * Allowed variable types
     * @var array
     */
    protected $_allowedVariableTypesLayout = array();

    /**
     * Allowed product types config node
     * @var string
     */
    protected $_globalNodeAllowLayoutTypes = 'global/layout/variable/allowed_types';

    /**
     * Default layout variable model
     * @var string
     */
    protected $_defaultLayoutVariableModel = 'blugento_designcustomiser/layout_variable_default';

    /**
     * System config path for user generated files directory
     * @var string
     */
    protected $_userDirectory = 'blugento_designcustomiser/user_directory';

    /**
     * Get variable type code
     * @param string $type
     * @return string
     */
    public function getAllowedVariableType($type)
    {
        $typeCode = (string)$type;
        
        if (!count($this->_allowedVariableTypes) && !empty($typeCode)) {
            $allowedTypesNodes = Mage::getConfig()->getNode($this->_globalNodeAllowProductTypes)->children();
            
            foreach ($allowedTypesNodes as $typeNode) {
                $this->_allowedVariableTypes[] = $typeNode->getName();
            }
        }
        
        if (empty($typeCode) || !in_array($typeCode, $this->_allowedVariableTypes)) {
            $typeCode = 'default';
        }
        
        return $typeCode;
    }
 
    /**
     * Get instance of variable model
     * @param mixed $variable
     * @return Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
     */
    public function getVariableModel($variable, $forcedType = null)
    {
        $definition = $this->getDefinitionModel();

        if (is_null($definition)) {
            return null;
        }
        
        $type = $forcedType !== null ? $forcedType : $definition->getVariableType($variable);

        $className = $this->_getVariableClassName($type);
        
        try {
            $objectVariable = Mage::getModel($className, $variable);
        } catch (Exception $e) {
            $objectVariable = null;
            Mage::logException($e);
        }
        
        return $objectVariable;
    }
    
    /**
     * Get model class name 
     * @param string $type
     * @return string
     */
    protected function _getVariableClassName($type)
    {
        $model = str_replace('default', (string)$type, $this->_defaultVariableModel);
        
        $modelClass = Mage::getConfig()->getModelClassName($model);
            
        if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
            return $model;
        }        
        
        return $this->_defaultVariableModel;
    }
    
    /**
     * Instance of Blugento_DesignCustomiser_Model_Scss_Definition_Interface
     * @todo Improve the determination of definition model class to use custom definition class from other modules 
     * @return null/Blugento_DesignCustomiser_Model_Scss_Definition_Interface
     */
    public function getDefinitionModel()
    {
        $model = $this->_defaultDefinitionScssModel.'_'. 'xml';
        
        $modelClass = Mage::getConfig()->getModelClassName($model);
            
        $instance = null;
        
        if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
            $instance = Mage::getSingleton($model);
        }        
        
        if ($instance instanceof Blugento_DesignCustomiser_Model_Scss_Definition_Interface) {
            return $instance;
        }
        
        return null;
    }

    /**
     * Get instance of template model
     * @param array $templateData
     * @return Blugento_DesignCustomiser_Model_Template_Item
     */
    public function getTemplateModel($templateData)
    {
        return Mage::getModel($this->_defaultTemplateModel, $templateData);
    }

    /**
     * Get Store ID
     * @return int
     */
    protected function _getDefaultDefinitionStore()
    {
        $defaultWebsite = Mage::getModel('core/website')->load('1', 'is_default');

        $storeId = Mage_Core_Model_App::DISTRO_STORE_ID;

        if ($defaultWebsite->getId()) {
            $storeGroup = Mage::getModel('core/store_group')->load($defaultWebsite->getId(), 'website_id');
        }

        if ($storeGroup->getId()) {
            $storeId = $storeGroup->getDefaultStoreId();
        }

        return $storeId;
    }
    
    /**
     * Get Store theme ID where scss definition file is located
     * @return int
     */
    public function getScssDefinitionStore()
    {
        if (Mage::getStoreConfig($this->_scssDefinitionStore)) {
            return Mage::getStoreConfig($this->_scssDefinitionStore);
        }
        
        return $this->_getDefaultDefinitionStore();
    }
    
    /**
     * Get filename of scss defintion variables
     * @return string
     */
    public function getScssDefinitionFile()
    {
        $file = $this->_defaultFileName;
        
        if (Mage::getStoreConfig($this->_scssDefinitionFileName)) {
            $file = Mage::getStoreConfig($this->_scssDefinitionFileName);
        }
        
        $extension = 'xml';
        
        if (Mage::getStoreConfig($this->_scssDefinitionFileExtension)) {
            $extension = Mage::getStoreConfig($this->_scssDefinitionFileExtension);
        }
        
        $filename = $file . '.' . $extension;
        
        $folderTheme = 'etc';
        
        if (Mage::getStoreConfig($this->_scssDefinitionDirectory)) {
            $folderTheme = Mage::getStoreConfig($this->_scssDefinitionDirectory);
        }
        
        return Mage::getDesign()->validateFile(
            $filename,
            array('_type' => $folderTheme)
        );
    }

    /**
     * Intance of Blugento_DesignCustomiser_Model_File_Definition_Interface
     * @todo Improve the determination of definition model class to use custom definition class from other modules
     * @return null|Blugento_DesignCustomiser_Model_File_Definition_Interface
     */
    public function getFinalCssDefinitionModel()
    {
        $model = $this->_defaultDefinitionFinalCssModel.'_'. 'css';

        $modelClass = Mage::getConfig()->getModelClassName($model);

        $instance = null;

        if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
            $instance = Mage::getSingleton($model, array($this->getFinalCssDefinitionFile()));
        }

        if ($instance instanceof Blugento_DesignCustomiser_Model_File_Definition_Interface) {
            return $instance;
        }

        return null;
    }

    /**
     * Get Store theme ID where final css definition file is located
     * @return int
     */
    public function getFinalCssDefinitionStore()
    {
        if (Mage::getStoreConfig($this->_finalCssDefinitionStore)) {
            return Mage::getStoreConfig($this->_finalCssDefinitionStore);
        }

        return $this->_getDefaultDefinitionStore();
    }

    /**
     * Get filename of final css definition filename
     * @return string|null
     */
    public function getFinalCssDefinitionFile()
    {
        $file = $this->_defaultFinalCssFileName;
        if (Mage::getStoreConfig($this->_finalCssDefinitionFileName)) {
            $file = Mage::getStoreConfig($this->_finalCssDefinitionFileName);
        }

        $extension = 'css';
        if (Mage::getStoreConfig($this->_finalCssDefinitionFileExtension)) {
            $extension = Mage::getStoreConfig($this->_finalCssDefinitionFileExtension);
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            $this->getFinalCssDefinitionStore()
        );

        $fileSkinPath = $this->getUserDirectoryName() . DS . 'css' . DS . $file . '.' . $extension;

        $path = Mage::getDesign()->validateFile($fileSkinPath,  array('_type' => 'skin'));

        if (!$path) {
            $dir = Mage::getDesign()->getSkinBaseDir() . DS . $this->getUserDirectoryName();

            if (!is_dir($dir)) {
                @mkdir($dir, 0777);
                $dir .= DS . 'css';
                @mkdir($dir, 0777);
            } else {
                $dir .= DS . 'css';
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777);
                }
            }

            $path = Mage::getDesign()->getSkinBaseDir() . DS . $fileSkinPath;
            $fileHandle = fopen($path, 'a');
            fclose($fileHandle);
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $path;
    }

    /**
     * Get path of templates directory
     * @param string|null $templateName
     * @return string
     */
    public function getTemplateDefinitionPath($templateName = null)
    {
        $directoryName = $this->_defaultTemplateDirectoryName;
        if (Mage::getStoreConfig($this->_templateDefinitionDirectoryName)) {
            $directoryName = Mage::getStoreConfig($this->_templateDefinitionDirectoryName);
        }

        $directoryPath = $directoryName;
        if ($templateName !== null) {
            $directoryPath .= DS . $templateName;
        }

        return $directoryPath;
    }

    /**
     * Intance of Blugento_DesignCustomiser_Model_File_Definition_Interface
     * @todo Improve the determination of definition model class to use custom definition class from other modules
     * @return null|Blugento_DesignCustomiser_Model_File_Definition_Interface
     */
    public function getGruntLogsDefinitionModel()
    {
        $model = $this->_defaultDefinitionGruntLogsModel.'_'. 'log';

        $modelClass = Mage::getConfig()->getModelClassName($model);

        $instance = null;

        if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
            try {
                $instance = Mage::getSingleton($model, array(
                    $this->getGruntLogsDefinitionFile()
                ));
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        if ($instance instanceof Blugento_DesignCustomiser_Model_File_Definition_Interface) {
            return $instance;
        }

        return null;
    }

    /**
     * Intance of Blugento_DesignCustomiser_Model_File_Definition_Interface
     * @todo Improve the determination of definition model class to use custom definition class from other modules
     * @return null|Blugento_DesignCustomiser_Model_File_Definition_Interface
     */
    public function getGruntLogsImageDefinitionModel()
    {
        $model = $this->_defaultDefinitionGruntLogsModel.'_'. 'log';

        $modelClass = Mage::getConfig()->getModelClassName($model);

        $instance = null;

        if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
            try {
                $filePath = $this->getGruntLogsImageDefinitionFile();
                $instance = Mage::getSingleton($model, array(
                    $filePath
                ));
                $instance->setPath($filePath);
                $instance->resetContent();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        if ($instance instanceof Blugento_DesignCustomiser_Model_File_Definition_Interface) {
            return $instance;
        }

        return null;
    }

    /**
     * Get filename of grunt logs file
     * @return string|null
     */
    public function getGruntLogsDefinitionFile()
    {
        $file = $this->_defaultGruntLogsFileName;
        if (Mage::getStoreConfig($this->_gruntLogsDefinitionFileName)) {
            $file = Mage::getStoreConfig($this->_gruntLogsDefinitionFileName);
        }

        $directory = 'var';
        if (Mage::getStoreConfig($this->_gruntLogsDefinitionDirectory)) {
            $directory = Mage::getStoreConfig($this->_gruntLogsDefinitionDirectory);
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            $this->getGruntLogsDefinitionStore()
        );
        $filePath = Mage::getBaseDir($directory) . DS . 'log' . DS . $file;
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        if (!file_exists($filePath)) {
            return null;
        }

        return $filePath;
    }

    /**
     * Get filename of grunt logs file
     * @return string|null
     */
    public function getGruntLogsImageDefinitionFile()
    {
        $file = $this->_defaultGruntLogsImageFileName;
        if (Mage::getStoreConfig($this->_defaultGruntLogsImageFileName)) {
            $file = Mage::getStoreConfig($this->_defaultGruntLogsImageFileName);
        }

        $directory = 'var';
        if (Mage::getStoreConfig($this->_gruntLogsDefinitionDirectory)) {
            $directory = Mage::getStoreConfig($this->_gruntLogsDefinitionDirectory);
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            $this->getGruntLogsDefinitionStore()
        );
        $filePath = Mage::getBaseDir($directory) . DS . 'log' . DS . $file;
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        if (!file_exists($filePath)) {
            return null;
        }

        return $filePath;
    }

    /**
     * Get Store theme ID where final css definition file is located
     * @return int
     */
    public function getGruntLogsDefinitionStore()
    {
        if (Mage::getStoreConfig($this->_gruntLogsDefinitionStore)) {
            return Mage::getStoreConfig($this->_gruntLogsDefinitionStore);
        }

        return $this->_getDefaultDefinitionStore();
    }
    
    /**
     * Get Scss XML file with values
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Interface
     */
    public function getScssXMLFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/scss_save_xml');
    }

    /**
     * Get Scss file with values
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Interface
     */
    public function getScssFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/scss_save_scss', array('no_param'));
    }

    /**
     * Get Final css file
     * @return Blugento_DesignCustomiser_Model_File_Save_Interface
     */
    public function getFinalCssFile()
    {
        return Mage::getSingleton('blugento_designcustomiser/file_save_css');
    }

    /**
     * Get variable auto value; will be skipped from sass file
     * @return string
     */
    public function getVariableAutoValue()
    {
        return $this->_autoValue;
    }

    /**
     * Get Store theme ID where scss definition file is located
     * @return int
     */
    public function getLayoutDefinitionStore()
    {
        if (Mage::getStoreConfig($this->_layoutDefinitionStore)) {
            return Mage::getStoreConfig($this->_layoutDefinitionStore);
        }

        return $this->_getDefaultDefinitionStore();
    }

    /**
     * Get filename of layout defintion variables
     * @return string
     */
    public function getLayoutDefinitionFile()
    {
        $file = $this->_defaultLayoutFileName;

        if (Mage::getStoreConfig($this->_layoutDefinitionFileName)) {
            $file = Mage::getStoreConfig($this->_layoutDefinitionFileName);
        }

        $extension = 'xml';

        if (Mage::getStoreConfig($this->_layoutDefinitionFileExtension)) {
            $extension = Mage::getStoreConfig($this->_layoutDefinitionFileExtension);
        }

        $filename = $file . '.' . $extension;

        $folderTheme = 'etc';

        if (Mage::getStoreConfig($this->_layoutDefinitionDirectory)) {
            $folderTheme = Mage::getStoreConfig($this->_layoutDefinitionDirectory);
        }

        return Mage::getDesign()->validateFile(
            $filename,
            array('_type' => $folderTheme)
        );
    }

    /**
     * Get Layout XML file with values
     * @return Blugento_DesignCustomiser_Model_Layout_Save_Interface
     */
    public function getLayoutXMLFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/layout_save_xml');
    }

    /**
     * Get Layout Updates XML file with values
     * @return Blugento_DesignCustomiser_Model_Layout_Save_Interface
     */
    public function getLayoutUpdateXMLFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/layout_save_layoutUpdate');
    }

    /**
     * Get Scss file with values
     * @return Blugento_DesignCustomiser_Model_Layout_Save_Interface
     */
    public function getLayoutScssFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/layout_save_scss', array('no_param'));
    }

    /**
     * Get Layout XML file with disable modules values
     * @return Blugento_DesignCustomiser_Model_Layout_Save_Interface
     */
    public function getLayoutXMLDisableModuleFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/layout_save_disableModule');
    }

    /**
     * Instance of Blugento_DesignCustomiser_Model_Layout_Definition_Interface
     * @todo Improve the determination of definition model class to use custom definition class from other modules
     * @return null/Blugento_DesignCustomiser_Model_Layout_Definition_Interface
     */
    public function getLayoutDefinitionModel()
    {
        $model = $this->_defaultDefinitionLayoutModel.'_'. 'xml';

        $modelClass = Mage::getConfig()->getModelClassName($model);

        $instance = null;

        if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
            $instance = Mage::getSingleton($model);
        }

        if ($instance instanceof Blugento_DesignCustomiser_Model_Layout_Definition_Interface) {
            return $instance;
        }

        return null;
    }

    /**
     * Get instance of variable model
     * @param mixed $variable
     * @return Blugento_DesignCustomiser_Model_Layout_Variable_Abstract
     */
    public function getLayoutVariableModel($variable)
    {
        $definition = $this->getLayoutDefinitionModel();

        if (is_null($definition)) {
            return null;
        }

        $type = $definition->getVariableType($variable);

        $className = $this->_getLayoutVariableClassName($type);

        try {
            $objectVariable = Mage::getModel($className, $variable);
        } catch (Exception $e) {
            $objectVariable = null;
            Mage::logException($e);
        }

        return $objectVariable;
    }

    /**
     * Get variable type code
     * @param string $type
     * @return string
     */
    public function getLayoutAllowedVariableType($type)
    {
        $typeCode = (string)$type;

        if (!count($this->_allowedVariableTypesLayout) && !empty($typeCode)) {
            $allowedTypesNodes = Mage::getConfig()->getNode($this->_globalNodeAllowLayoutTypes)->children();

            foreach ($allowedTypesNodes as $typeNode) {
                $this->_allowedVariableTypesLayout[] = $typeNode->getName();
            }
        }

        if (empty($typeCode) || !in_array($typeCode, $this->_allowedVariableTypesLayout)) {
            $typeCode = 'default';
        }

        return $typeCode;
    }

    /**
     * Get model class name
     * @param string $type
     * @return string
     */
    protected function _getLayoutVariableClassName($type)
    {
        $model = str_replace('default', (string)$type, $this->_defaultLayoutVariableModel);

        $modelClass = Mage::getConfig()->getModelClassName($model);

        if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
            return $model;
        }

        return $this->_defaultVariableModel;
    }

    /**
     * Get Layout file with values
     * @return Blugento_DesignCustomiser_Model_Layout_Save_Interface
     */
    public function getLayoutFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/layout_save_xml', array('no_param'));
    }

    /**
     * Get filename of scss defintion variables
     * @return string
     */
    public function getUserDirectoryName()
    {
        $directory = 'blugento';

        if (Mage::getStoreConfig($this->_userDirectory)) {
            $directory = Mage::getStoreConfig($this->_userDirectory);
        }

        return $directory;
    }

    /**
     * Get and return API token from var/wAuthToken
     *
     * @return bool|string
     */
    public function getwAuthToken()
    {
        $filePath = Mage::getBaseDir('var') . DS . 'wAuthToken';

        if (file_exists($filePath)) {
            $file = fopen($filePath, 'r');
            $token = fgets($file);
            fclose($file);

            return $token;
        }

        return false;
    }

    /**
     * Make an API call to run Grunt manually
     *
     * @param string $token
     * @return int
     */
    public function runGruntRelease($token)
    {
        $ch = curl_init('http://watcher:8080/api/release');

        header('Content-Type: application/json');
        $authorization = "Authorization: Bearer " . $token;

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $status;
    }
}
