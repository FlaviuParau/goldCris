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

class Blugento_DesignCustomiser_Model_Layout_Save_Scss extends Varien_File_Object
    implements Blugento_DesignCustomiser_Model_Layout_Save_Interface
{

    /**
     * SCSS file name
     * @var string
     */
    protected $_filenameScss = '_variable-layout.scss';

    /**
     * SCSS file name
     * @var string
     */
    protected $_scssSkinDir = 'scss';

    /**
     * Constructor
     */
    public function __construct()
    {
        try {
            $helper = Mage::helper('blugento_designcustomiser');

            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $helper->getLayoutDefinitionStore()
            );

            $fileSkinPath = $helper->getUserDirectoryName() . DS . $this->_scssSkinDir . DS . $this->_filenameScss;

            $path = Mage::getDesign()->validateFile($fileSkinPath,  array('_type' => 'skin'));

            if (!$path) {
                $dir = Mage::getDesign()->getSkinBaseDir() . DS . $helper->getUserDirectoryName();

                if (!is_dir($dir)) {
                    @mkdir($dir, 0777);
                    $dir .= DS . $this->_scssSkinDir;
                    @mkdir($dir, 0777);
                } else {
                    $dir .= DS . $this->_scssSkinDir;
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777);
                    }
                }

                $path = Mage::getDesign()->getSkinBaseDir() . DS . $fileSkinPath;
                $fileHandle = fopen($path, 'a');
                fclose($fileHandle);
            }

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        } catch (Exception $e) {
            Mage::logException($e);
        }

        parent::__construct($path);
    }

    /**
     * Save the collection data in file
     * @param Varien_Data_Collection $collection
     * @return Blugento_DesignCustomiser_Model_Layout_Save_Scss
     */
    public function save(Varien_Data_Collection $collection) 
    {
        $result = array();
        
        $index = 0;
        
        while (!$this->eof()) {

            $data = $this->fgets();

            if (empty($data)) {
                continue;
            }
            
            $result[$index] = $data;

            foreach ($collection as $variable) {
                if (!$variable->getScss()) {
                    continue;
                }
                $scssDef = str_replace(' [value]', '', $variable->getScss());

                if ($data != str_replace($scssDef, '', $data) && !$variable->getScssUpdate())  {
                    if ($variable->getSkipScss()) {
                        $result[$index] = '';
                    } else {
                        $result[$index] = $this->_setVariableValue($variable);
                    }
                    $variable->setScssUpdate(true);
                }
            }
            
            $index++;
        }
        
        foreach ($collection as $variable) {
            if (!$variable->getScss()) {
                continue;
            }
            if (!$variable->getScssUpdate() && !$variable->getSkipScss()) {
                $result[$index++] = $this->_setVariableValue($variable);
            }
        }

        $fh = fopen($this->_path, 'w');
        foreach ($result as $line) {
            if (trim($line)) {
                fwrite($fh, $line);
            }
        }
        fclose($fh);
        
        return $this;
    }

    /**
     * Get Variable Value
     * @TODO: Get Value from scss file
     * @param Blugento_DesignCustomiser_Model_Layout_Variable_Abstract $variable
     * @return string
     */
    public function getVariableValue(Blugento_DesignCustomiser_Model_Layout_Variable_Abstract $variable)
    {
        $value = '';
        
        return $value;
    }

    /**
     * Set bariable value for a line
     * @param Blugento_DesignCustomiser_Model_Layout_Variable_Abstract $variable
     * @return $this
     */
    private function _setVariableValue(Blugento_DesignCustomiser_Model_Layout_Variable_Abstract $variable)
    {
        return str_replace('[value]', $variable->getSaveValue(), $variable->getScss()) . ';' . PHP_EOL;
    }
}
