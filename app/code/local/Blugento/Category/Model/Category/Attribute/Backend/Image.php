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
 * @package     Blugento_Category
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Category_Model_Category_Attribute_Backend_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Base folder
     *
     * @var string
     */
    protected $_baseFolder = 'catalog/category';

    /**
     * Specific folder
     *
     * @var string
     */
    protected $_specificFolder = '';

    /**
     * Get media folder path
     *
     * @return string
     */
    public function _getFolderPath()
    {
        return Mage::getBaseDir('media') . DS . $this->_baseFolder . DS . $this->_specificFolder . DS; // todo
    }

    /**
     * After save
     *
     * @param Varien_Object $object
     * @return Blugento_Category_Model_Category_Attribute_Backend_Image $this
     * @throws Exception
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }

        $path = $this->_getFolderPath();
        try {
            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array());
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);
            $object->setData($this->getAttribute()->getName(), $this->_specificFolder . '/' . $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }
            return $this;
        }
    }

    /**
     * Get base folder from media
     *
     * @return string
     */
    public function getMediaBaseFolder()
    {
        return $this->_baseFolder;
    }

    /**
     * Get specific folder from media
     *
     * @return string
     */
    public function getMediaSpecificFolder()
    {
        return $this->_specificFolder;
    }
}
