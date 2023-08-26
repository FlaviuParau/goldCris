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

class Blugento_DesignCustomiser_Model_Layout_Save_LayoutUpdate extends Varien_Simplexml_Config
    implements Blugento_DesignCustomiser_Model_Layout_Save_Interface
{

    /**
     * XML file name
     * @var string
     */
    private $_xmlFileName = 'layout-update.xml';

    /**
     * XML File Path
     * @var string
     */
    public $_xmlPath = '';

    /**
     * Get file path
     * @return string
     */
    public function getFile()
    {
        if (!empty($this->_xmlPath)) {
            return $this->_xmlPath;
        }

        try {
            $helper = Mage::helper('blugento_designcustomiser');

            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $helper->getLayoutDefinitionStore()
            );

            $directory = $helper->getUserDirectoryName();
            $fileSkinPath = $directory . DS . $this->_xmlFileName;

            $this->_xmlPath = Mage::getDesign()->validateFile($fileSkinPath,  array('_type' => $directory));

            if (!$this->_xmlPath) {
                $dir = Mage::getDesign()->getBaseDir(array('_type' => $directory));

                if (!is_dir($dir)) {
                    @mkdir($dir, 0777);
                }

                $this->_xmlPath = Mage::getDesign()->getBaseDir(array('_type' => $fileSkinPath));
            }

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_xmlPath;
    }

    /**
     * Save the collection data in file
     * @param Varien_Data_Collection $collection
     * @return Blugento_DesignCustomiser_Model_Layout_Save_Xml
     */
    public function save(Varien_Data_Collection $collection) 
    {
        $outputXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<layout version=\"0.1.0\">";

        foreach ($collection as $variable) {
            if ($variable->getLayout() != 1) {
                continue;
            }

            $savedValue = $variable->getSaveValue();
            if (!$savedValue) {
                continue;
            }

            $options = $variable->getOptions();
            if (!isset($options['option'])) {
                continue;
            }
            foreach ($options['option'] as $option) {
                if ((string) $option->value == $savedValue) {
                    $code = (string) $option->code;
                    if ($code) {
                        $outputXML .= $code;
                    }
                    break;
                }
            }
        }

        $outputXML .= "\n</layout>";

        $fileLink = fopen($this->getFile(), 'w');
        if ($fileLink) {
            fwrite($fileLink, $outputXML);
            fclose($fileLink);
        }

        return $this;
    }

    /**
     * Get Variable Value
     * @param Blugento_DesignCustomiser_Model_Layout_Variable_Abstract $variable
     * @return string
     */
    public function getVariableValue(Blugento_DesignCustomiser_Model_Layout_Variable_Abstract $variable)
    {
        return '';
    }
}
