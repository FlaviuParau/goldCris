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

class Blugento_Importer_Model_Images extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('blugento_importer/images');
    }

    /**
     * Process remote images.
     *
     * @param int $profileId
     * @return array|string
     */
    public function processRemoteImages($profileId, $duplicateImages)
    {
        $result = array();

        /** @var Blugento_Importer_Helper_Data $helper */
        $helper = Mage::helper('blugento_importer');

        if (!$profileId) {
            $result['error'] = $helper->__('Missing Profile Id.');
            return $result;
        }

        try {
            $tableName = $this->_getTableName('blugento_importer_images');

            $query = "SELECT id,entity_id,image_path,image_label,image_type,store_id FROM $tableName WHERE profile_id ='$profileId'";
            $images = $this->_getReadConnection()->fetchAll($query);

            if(!count($images)) {
                $result['success'] = $helper->__('No images found to process for this Profile (ID: %s)', $profileId);
                return $result;
            }

            $imgProcessed = array();

            $count = 1;
            foreach ($images as $image) {
                $success = false;

                $id         = isset($image['id']) ? $image['id'] : null;
                $entityId   = isset($image['entity_id']) ? $image['entity_id'] : null;
                $imagePath  = isset($image['image_path']) ? $image['image_path'] : null;
                $imageLabel = isset($image['image_label']) ? $image['image_label'] : '';
                $imageType  = isset($image['image_type']) ? $image['image_type'] : null;
                $storeId    = isset($image['store_id']) ? $image['store_id'] : null;

                $imageName = $this->_getCatalogImageName($imagePath, $duplicateImages, $entityId);

                if ($imageName) {
                    $success = $this->_processImage($entityId, $imageName, $imageLabel, $imageType, $count, $storeId);
                    $count++;
                }

                if ($success) {
                    $imgProcessed[] = $id;
                }
            }

            if (count($imgProcessed)) {
                $this->_removeItemProcessed($imgProcessed);
            }

            $result['success'] = $helper->__('%s images from Profile (ID: %s) has been processed.', count($imgProcessed), $profileId);
            return $result;

        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Save the image to disk and return the path.
     *
     * @param string $imageName
     * @param int $duplicateImages
     * @param int $entityId
     * @return null|string
     */
    private function _getCatalogImageName($imageName, $duplicateImages, $entityId)
    {
        $_mediaBase = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS ;

        try {
            $fileName = explode('/', $imageName);
            $fileName = end($fileName);

            if ($duplicateImages){
                $fileName = explode('.', $fileName);
                $fileName[0] = $fileName[0] . '_' . $entityId;
                $fileName = implode('.', $fileName);
            }

            $firstDir  = strtolower(substr($fileName, 0, 1));
            $secondDir = strtolower(substr($fileName, 1, 1));
            $catalogDir = strtolower($_mediaBase . $firstDir . DS . $secondDir);

            $catalogDestination = $catalogDir . DS . $fileName;

            if (!file_exists($catalogDestination)) {
                if (!file_exists($catalogDir)) {
                    mkdir($catalogDir, 0775, true);
                }
                $file = file($imageName);

                //resize images greater than 3MB
                if (filesize($file) > 3145728) {
                    Mage::helper('blugento_importer')->compressFile($file, $catalogDestination, 50);
                } else {
                    file_put_contents($catalogDestination, $file);
                }
            }

            return DS . $firstDir . DS . $secondDir . DS . $fileName;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }

        return null;
    }

    /**
     * Remove the items that have been processed.
     *
     * @param array $imgProcessed
     */
    private function _removeItemProcessed($imgProcessed)
    {
        $imgProcessed = implode("','", $imgProcessed);
        try {
            $tableName = $this->_getTableName('blugento_importer_images');
            $query = "DELETE FROM  $tableName WHERE id IN ('$imgProcessed')";
            $this->_getWriteConnection()->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Process Image.
     *
     * @param int $entityId
     * @param varchar $imagePath
     * @param varchar $imageLabel
     * @param varchar $imageType
     * @return bool
     */
    private function _processImage($entityId, $imageName, $imageLabel, $imageType, $count, $storeId)
    {
        $imgAttrIds = $this->_getImageAttributeIds(array('image', 'small_image', 'thumbnail', 'image_hover', 'media_gallery'));

        $galleryAttrId = $this->_getImageAttributeIds(array('media_gallery'));
        $galleryAttrId = isset($galleryAttrId['media_gallery']) ? $galleryAttrId['media_gallery'] : null;

        try {
            if (!$imageName) {
                return false;
            }

            $imageType = $imageType == 'gallery' ? 'media_gallery' : $imageType;
            $imageAttributeId = $imgAttrIds[$imageType];
            $imageName = str_replace('\\', '/', $imageName);

            /*
             * INSERT image in catalog_product_entity_varchar
             */
            if ($galleryAttrId != $imageAttributeId) {
                $tableName = $this->_getTableName('catalog_product_entity_varchar');
                $sql = "INSERT INTO $tableName 
                    (entity_type_id,attribute_id,store_id,entity_id,value) 
                    VALUES (4,$imageAttributeId,$storeId,$entityId,'$imageName') ";
                $this->_getWriteConnection()->query($sql);
            }

            /*
             * DELETE and INSERT image in catalog_product_entity_media_gallery
             */
            $tableName = $this->_getTableName('catalog_product_entity_media_gallery');

            $sql = "DELETE FROM $tableName 
                WHERE attribute_id=$galleryAttrId AND entity_id=$entityId AND value = '$imageName' ";
            $this->_getWriteConnection()->query($sql);

            $sql = "INSERT INTO $tableName 
                    (attribute_id,entity_id,value) 
                    VALUES ($galleryAttrId,$entityId,'$imageName') ";
            $this->_getWriteConnection()->query($sql);

            /*
             * INSERT image in catalog_product_entity_media_gallery_value
             */
            $valueId = $this->_getWriteConnection()->fetchOne('SELECT last_insert_id()');
            $tableName = $this->_getTableName('catalog_product_entity_media_gallery_value');
            $sql = "INSERT INTO $tableName
                (value_id,store_id,label,position)
                VALUES  ($valueId,$storeId,'$imageLabel',$count) ";
//            $sql = "INSERT INTO $tableName
//                (value_id,store_id,label)
//                VALUES  ($valueId,$storeId,'$imageLabel') ";
            $this->_getWriteConnection()->query($sql);

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

        return false;
    }

    /**
     * Return the product image attribute ids.
     *
     * @param array $type
     * @return array
     */
    private function _getImageAttributeIds($type)
    {
        $codes = implode("','", $type);
        $tableName = $this->_getTableName('eav_attribute');
        $query = "SELECT attribute_id, attribute_code FROM $tableName WHERE attribute_code IN ('$codes') AND entity_type_id=4";

        $result = $this->_getWriteConnection()->fetchAll($query);

        $ids = array();
        foreach ($result as $attribute) {
            if (isset($attribute['attribute_id'])) {
                $attrCode = $attribute['attribute_code'];
                $ids[$attrCode] = $attribute['attribute_id'];
            }
        }

        return $ids;
    }

    /**
     * Return the read connection
     *
     * @return mixed
     */
    private function _getReadConnection()
    {
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_read');
    }

    /**
     * Return the write connection
     *
     * @return mixed
     */
    private function _getWriteConnection()
    {
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_write');
    }

    /**
     * Return the table name.
     *
     * @param string $tableName
     * @return mixed
     */
    private function _getTableName($tableName)
    {
        $resource = Mage::getSingleton('core/resource');
        return $resource->getTableName($tableName);
    }

    /**
     * Return the number of images that need to be processed.
     *
     * @param int $profileId
     * @return null|string
     */
    public function getImagesToProcess($profileId)
    {
        $helper = Mage::helper('blugento_importer');

        try {
            $tableName = $this->_getTableName('blugento_importer_images');
            $query = "SELECT id FROM $tableName WHERE profile_id ='$profileId'";
            $images = $this->_getReadConnection()->fetchAll($query);

            return $helper->__(' %s images need to be processed.', count($images));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return null;
    }
}
