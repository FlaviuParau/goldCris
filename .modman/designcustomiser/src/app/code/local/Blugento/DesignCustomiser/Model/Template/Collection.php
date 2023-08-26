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

class Blugento_DesignCustomiser_Model_Template_Collection
    extends Varien_Data_Collection
{
    /**
     * Helper class to get info about template path
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper = null;

    protected $_infoFile = 'README.md';

    protected $_placeholders = array(
        '[ID]' => 'id',
        '[TITLE]' => 'title',
        '[DESCRIPTION]' => 'description'
    );

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->_setHelper();
    }

    /**
     * Load data
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Blugento_DesignCustomiser_Model_Template_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $helper = $this->_helper;

        $appEmulation = Mage::getSingleton('core/app_emulation');

        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            $helper->getScssDefinitionStore()
        );
        $directoryPath = Mage::getDesign()->validateFile($helper->getTemplateDefinitionPath(), array('_type' => 'skin'));

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        if ($directoryPath) {
            if ($handle = opendir($directoryPath)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && is_dir($directoryPath . DS . $entry)) {
                        $entryData = $this->_getDirectoryInfo($directoryPath . DS . $entry);
                        $template = $helper->getTemplateModel($entryData);
                        $this->addItem($template);
                    }
                }
                closedir($handle);
            }
        }

        $this->_setIsLoaded();

        // added for future development on printing messages based on bool operations with this 2 parameters
        if ($printQuery && $logQuery) { }

        return $this;
    }

    /**
     * Set helper class
     * Set this in $this->_helper
     * @var Mage_Core_Helper_Abstract 
     */
    protected function _setHelper() 
    {
        $this->_helper = Mage::helper('blugento_designcustomiser');
        return $this;
    }

    protected function _getDirectoryInfo($directoryPath)
    {
        $filePath = $directoryPath . DS . $this->_infoFile;
        if (!file_exists($filePath)) {
            return array();
        }

        $info = array();

        $contents = file_get_contents($filePath);
        $lines = explode(PHP_EOL, $contents);
        foreach ($lines as $line) {
            foreach ($this->_placeholders as $placeholder => $property) {
                if (strpos($line, $placeholder) !== false) {
                    $info[$property] = trim(str_replace($placeholder, '', $line));
                    break;
                }
            }
        }

        return $info;
    }
}
