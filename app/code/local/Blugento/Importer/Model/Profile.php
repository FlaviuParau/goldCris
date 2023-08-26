<?php
/**
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
 * @package     Blugento_Importer
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile
 *
 * @method Blugento_Importer_Model_Mysql4_Profile _getResource()
 */

class Blugento_Importer_Model_Profile extends Mage_Core_Model_Abstract
{
    const DEFAULT_EXPORT_PATH = 'var/export';
    const DEFAULT_EXPORT_FILENAME = 'export_';

    protected function _construct()
    {
        $this->_init('blugento_importer/profile');
    }

    protected function _afterLoad()
    {
        $guiData = '';
        if (is_string($this->getMapAttributesData())) {
            try {
                $guiData = Mage::helper('core/unserializeArray')
                    ->unserialize($this->getMapAttributesData());
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        $this->setMapAttributesData($guiData);

        parent::_afterLoad();
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (is_array($this->getMap())) {
            $this->setMapAttributesData(serialize($this->getMap()));
        }

        if ($this->getDataSource() == 1) {
            if (isset($_FILES['file_uploaded']['tmp_name']) && $_FILES['file_uploaded']['tmp_name'] !='') {
                $uploader = new Mage_Core_Model_File_Uploader('file_uploaded');
                $uploader->setAllowedExtensions(array('csv', 'xml'));
                $path = Mage::app()->getConfig()->getTempVarDir() . '/importer/';
                $uploader->save($path);
                if ($uploadFile = $uploader->getUploadedFileName()) {
                    $newFilename = 'import-' . date('YmdHis') . '-file_uploaded_' . $uploadFile;
                    rename($path . $uploadFile, $path . $newFilename);
                }
            }

            if ($newFilename) {
                $this->setFileName($newFilename);
                $this->setFilePath('var/importer');
            } else {
                Mage::throwException(Mage::helper('blugento_importer')->__("No file has been uploaded or selected."));
            }
        }

        if ($this->getDataSource() == 2 && $this->getExistingFile() && $this->getExistingFile() !='') {
            $this->setFileName($this->getExistingFile());
            $this->setFilePath('var/importer');
        }

        if ($this->getDataSource() == 3 && $this->getFileFormat() == 'excel_xml') {
            Mage::helper('blugento_importer')->getRemoteXmlFileData($this);
        }

        if ($this->getFileFormat() == 'excel_xml' && trim($this->getXmlEntityNode()) == '') {
            $this->setXmlEntityNode('product');
        }

        if ($this->_getResource()->isProfileExists($this->getName(), $this->getId())) {
            Mage::throwException(Mage::helper('blugento_importer')->__("Profile with the same name already exists."));
        }
    }

    protected function _afterSave()
    {
        if (is_string($this->getGuiData())) {
            try {
                $guiData = Mage::helper('core/unserializeArray')
                    ->unserialize($this->getGuiData());
                $this->setGuiData($guiData);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $profileHistory = Mage::getModel('dataflow/profile_history');

        $adminUserId = $this->getAdminUserId();
        if($adminUserId) {
            $profileHistory->setUserId($adminUserId);
        }

        /*$profileHistory
            ->setProfileId($this->getId())
            ->setActionCode($this->getOrigData('profile_id') ? 'update' : 'create')
            ->save();*/

        parent::_afterSave();
    }

    /**
     * Test profile
     *
     * @return Mage_Dataflow_Model_Profile
     */
    public function test()
    {
        /** @var Blugento_Importer_Model_Importer $importer */
        $importer = Mage::getModel('blugento_importer/importer');
        $importer->setProfileData($this->getData());

        $result = $importer->test($this);

        $this->setExceptions($result->getExceptionsx());
        $this->setSampleData($result->getSampleData());

        return $this;
    }

    /**
     * Run profile
     *
     * @return Mage_Dataflow_Model_Profile
     */
    public function run()
    {
        /** @var Blugento_Importer_Model_Importer $importer */
        $importer = Mage::getModel('blugento_importer/importer');
        $importer->setProfileData($this->getData());

        $result = $importer->run($this);

        $this->setExceptions($result->getExceptions());

        return $this;
    }

    /**
     * Set Run Flag
     *
     * @param int $id
     * @param int $val
     */
    public function setProfileRunFlag($id, $val){
        $sqlBefore = "UPDATE blugento_importer_profile SET run_flag = 0;";
        $sql       = "UPDATE blugento_importer_profile SET run_flag = {$val} WHERE id = {$id};";
        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
            $conn->query($sqlBefore);
            $conn->query($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
