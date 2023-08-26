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

class Blugento_DesignCustomiser_Model_Layout_Save_DisableModule extends Varien_Simplexml_Config
    implements Blugento_DesignCustomiser_Model_Layout_Save_Interface
{

    /**
     * XML file name
     * @var string
     */
    private $_xmlFileName = 'disable-module.xml';

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
            $module = $variable->getDisableModule();
            if (!$module) {
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
                    $code = '<module><name>' . $module . '</name><disable>' . $savedValue . '</disable></module>';
                    $outputXML .= $code;

                    $this->_disableModule($module, $savedValue);
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

    public function saveFromConfig()
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getLayoutXMLFileValues();

        $outputXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<layout version=\"0.1.0\">";

        $collectionLayout = Mage::getModel('blugento_designcustomiser/layout_variable_collection')->load();
        foreach ($collectionLayout as $variable) {
            $module = $variable->getDisableModule();
            if (!$module) {
                $variable->setSaveValue($xmlSaveValues->getVariableValue($variable));
                continue;
            }

            $value = Mage::getStoreConfig('advanced/modules_disable_output/' . $module);

            if ($module == 'Blugento_Compare') {
                $disableModule_ = 1 - $value;
                if ($disableModule_ == 1) {
                    $disableModule_ = 4;
                }
                try {
                    Mage::helper('blugento_compare')->setState($disableModule_);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $value = $value == 1 ? 2 : 1;

            $code = '<module><name>' . $module . '</name><disable>' . $value . '</disable></module>';
            $outputXML .= $code;

            $variable->setSaveValue($value);

        }

        $outputXML .= "\n</layout>";

        $fileLink = fopen($this->getFile(), 'w');
        if ($fileLink) {
            fwrite($fileLink, $outputXML);
            fclose($fileLink);
        }

        $xmlSaveValues->save($collectionLayout);

        return true;
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

    protected function _disableModule($module, $disableEnable)
    {
        if ($disableEnable == 'disabled' || $disableEnable == 'enabled') {
            $disableEnable == 'disabled' ? 2 : 1;
        }

        // Module should be enabled
        $disableModule = 1;
        if ($disableEnable == 1) {
            $disableModule = 0;
        }

        if ($module == 'Blugento_Compare') {
            $disableModule_ = 1-$disableModule;
            if ($disableModule_ == 1) {
                $disableModule_ = 4;
            }
            try {
                Mage::helper('blugento_compare')->setState($disableModule_);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        try {
            $configModel = new Mage_Core_Model_Config();
            $configModel->saveConfig('advanced/modules_disable_output/' . $module, $disableModule, 'default', 0);

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }
}
