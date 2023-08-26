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

class Blugento_DesignCustomiser_Model_File_Definition_Css
    extends Blugento_DesignCustomiser_Model_File_Definition_File_Abstract
{
    /**
     * Final css file content
     * @var string
     */
    protected $_fileContent = null;

    /**
     * Get file content as string
     * @return string|null
     */
    public function loadContent($backupRevisionId=null)
    {
        if ($backupRevisionId) {
            $dbCss = Mage::getModel('blugento_designcustomiser/finalcss')->load($backupRevisionId);
            return $dbCss->getCss();
        }

        $dbCssCollection = Mage::getModel('blugento_designcustomiser/finalcss')->getCollection();
        $lastCss = $dbCssCollection->getLastItem();
        if (count($dbCssCollection) && $lastCss->getCss() !='') {
            return $lastCss->getCss();
        }

        if ($this->_fileContent !== null || is_null($this->_helper)) {
            return $this->_fileContent;
        }

        try {
            $appEmulation = Mage::getSingleton('core/app_emulation');

            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $this->_helper->getFinalCssDefinitionStore()
            );

            $fileSpecsDefinitionCSS = $this->_helper->getFinalCssDefinitionFile();

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

            if (!$fileSpecsDefinitionCSS) {
                $this->setMessage('error', "No specification CSS file is set on the default theme.");
            }

            $this->_fileContent = file_get_contents($fileSpecsDefinitionCSS);

        } catch (Exception $e) {
            $this->_fileContent = null;
            Mage::logException($e);
        }

        if ($this->_fileContent && trim($this->_fileContent) !='') {
            $dbCss = Mage::getModel('blugento_designcustomiser/finalcss');
            $dbCss->setData('css', $this->_fileContent);
            $dbCss->save();
        }

        return $this->_fileContent;
    }

    /**
     * Helper class to get info about definition file and variable models
     * Set this in $this->_helper
     * @var Mage_Core_Helper_Abstract
     * @return Blugento_DesignCustomiser_Model_File_Definition_Css
     */
    protected function _setHelper() 
    {
        $this->_helper = Mage::helper('blugento_designcustomiser');
        return $this;
    }
}

