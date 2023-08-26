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

class Blugento_DesignCustomiser_Helper_Scss_Image extends Blugento_DesignCustomiser_Helper_Data
{
    /**
     * System config path for image definition file extension
     * @var string
     */
    protected $_scssDefinitionFileExtension = 'blugento_designcustomiser/img/definition_file_extension';

    /**
     * System config path for image definition file name
     * @var string
     */
    protected $_scssDefinitionFileName = 'blugento_designcustomiser/img/definition_filename';

    /**
     * System config path for image definition directory place
     * @var string
     */
    protected $_scssDefinitionDirectory = 'blugento_designcustomiser/img/definition_directory_theme';

    /**
     * System config path for image definition store theme
     * @var string
     */
    protected $_scssDefinitionStore = 'blugento_designcustomiser/img/definition_store';
    /**
     * Default definition image model path
     * @var string
     */
    protected $_defaultDefinitionScssModel = 'blugento_designcustomiser/scss_definition_image';

    /**
     * Allowed product types config node
     * @var string 
     */
    protected $_globalNodeAllowProductTypes = 'global/scss/image/allowed_types';
    
    /**
     * Default file name for definition scss file
     * @var string 
     */
    protected $_defaultFileName = 'specs_images';
    
    /**
     * Default variable model
     * @var string 
     */
    protected $_defaultVariableModel = 'blugento_designcustomiser/scss_variable_image_default';

    /**
     * Get Image XML file with values
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Interface
     */
    public function getImageXMLFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/scss_save_image');
    }

    /**
     * Get Scss XML file with values
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Interface
     */
    public function getImageXMLSaveFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/scss_save_image_xml');
    }

    /**
     * Get Scss file with values
     * @return Blugento_DesignCustomiser_Model_Scss_Save_Interface
     */
    public function getImageScssFileValues()
    {
        return Mage::getSingleton('blugento_designcustomiser/scss_save_image_scss', array('no_param'));
    }

}
