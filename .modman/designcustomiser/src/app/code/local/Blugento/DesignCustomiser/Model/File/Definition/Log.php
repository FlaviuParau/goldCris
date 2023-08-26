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

class Blugento_DesignCustomiser_Model_File_Definition_Log
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
    public function loadContent()
    {
        if ($this->_fileContent !== null || is_null($this->_helper)) {
            return $this->_fileContent;
        }

        try {
            if (!$this->_path) {
                $this->_path = $this->_helper->getGruntLogsDefinitionFile();
                if (!$this->_path) {
                    $this->setMessage('error', "No Grunt logs file is set or the file doesn't exist.");
                }
            }

            $this->_fileContent = $this->_path ? file_get_contents($this->_path) : null;

        } catch (Exception $e) {
            $this->_fileContent = null;
            Mage::logException($e);
        }

        return $this->_fileContent;
    }

    /**
     * Set file path
     * @param string $path
     * @return Blugento_DesignCustomiser_Model_File_Definition_Log
     */
    public function setPath($path)
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * Reset file content
     * @return Blugento_DesignCustomiser_Model_File_Definition_Log
     */
    public function resetContent()
    {
        $this->_fileContent = null;
        return $this;
    }

    /**
     * Clears logs file content
     * @return bool
     */
    public function clearContent()
    {
        try {
            if (!$this->_path) {
                $this->_path = $this->_helper->getGruntLogsDefinitionFile();

                if (!$this->_path) {
                    $this->setMessage('error', "No Grunt logs file is set or the file doesn't exist.");
                    return false;
                }
            }

            $fh = fopen($this->_path, 'w');
            if ($fh === false) {
                return false;
            }

            fwrite($fh, '');
            fclose($fh);

            $this->_fileContent = '';
            return true;

        } catch (Exception $e) {
            $this->_fileContent = null;
            Mage::logException($e);
        }

        return false;
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
