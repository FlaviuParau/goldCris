<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Giftwrap_Model_Observer_Type
{

    /**
     * Observer for save gift wrap configuration
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function saveTypeOnConfigSectionChange($observer)
    {
        $ruleConditionsArray = Mage::app()->getRequest()->getParam('rule', array());
        Mage::helper('aw_giftwrap/config')->saveExcludeProductsRule($ruleConditionsArray);
        $this->_saveTypeCollection(Mage::app()->getRequest()->getParam('type'));
        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    protected function _saveTypeCollection($data)
    {
        if (!$data || !is_array($data)) {
            return $this;
        }
        $failedFiles = array();
        foreach ($data as $typeId => $typeData) {
            $typeModel = Mage::getModel('aw_giftwrap/type');
            $typeModel->load(intval($typeId));
            if (array_key_exists('delete', $typeData) && $typeData['delete']) {
                if (!is_null($typeModel->getId())) {
                    $typeModel->delete();
                }
            } else {
                if (array_key_exists('image', $typeData) && $typeData['image']) {
                    if (array_key_exists('delete', $typeData['image']) && $typeData['image']['delete']) {
                        $typeData['image'] = null;
                    } else {
                        unset($typeData['image']);
                    }
                }
                $typeModel->addData($typeData);
                $typeModel->save();

                $uploadedFileName = null;
                try {
                    $uploadedFileName = Mage::helper('aw_giftwrap/image')->uploadImage($typeModel, $typeId);
                    if (!is_null($uploadedFileName)) {
                        $typeModel->setData('image', $uploadedFileName)->save();
                    }
                } catch (Exception $e) {
                    if ($e->getCode() == AW_Giftwrap_Helper_Image::EXCEPTION_CODE_UNSUPPORTED_IMAGE_TYPE) {
                        $failedFiles[] = $e->getMessage();
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            }
        }
        if (count($failedFiles)) {
            $fileNameList = '"' . implode('", "', $failedFiles) . '"';
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('aw_giftwrap')->__(
                    'File(s) %s are invalid. Supported file types are: %s.',
                    $fileNameList,
                    implode(', ', Mage::helper('aw_giftwrap/image')->getAllowedFileExtensions())
                )
            );
        }
        return $this;
    }
}