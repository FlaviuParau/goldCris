<?php
/**
 * Blugento Design Customiser
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_DesignCustomiser
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_DesignCustomiser_Adminhtml_DesignController extends Mage_Adminhtml_Controller_Action
{
    protected $_adminSession = null;

    const CMSBLOCK = 'cms_blocks.csv';

    /**
     * Index Action
     */
    public function indexAction()
    {
	    Mage::getSingleton('admin/session')->setDesignCustomiserMode('simple');
        $this->loadLayout();
        $this->_setActiveMenu('blugento_adminmenu/blugento_designcustomiser/blugento_designcustomiser');
        $this->_addBreadcrumb(
            Mage::helper('blugento_designcustomiser')->__('Customise Design'),
            Mage::helper('blugento_designcustomiser')->__('Customise Design')
        );
        $this->_title('Customise Design');

        $this->renderLayout();
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        if (empty($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('adminhtml/session');
        }

        $params = $this->getRequest()->getParams();
        if (isset($params['key'])) {
            $params['key'] = null;
            unset($params['key']);
        }
        if (empty($params)) {
            Mage::getSingleton('adminhtml/session')->getMessages(true);
            $this->_redirect('*/*/index', array('_query' => array('a' => 1)));
            return;
        }

        $this->_saveStyleing();

        $this->_saveImages();

        $this->_saveLayout();

        $this->_saveFinalCss();

        $this->_saveTemplate();

        $this->clearCache();

        $result = $this->_runGruntRelease();
        if ($result['code'] == 'success') {
            $this->_adminSession->addSuccess($result['message']);
        } else {
            $this->_adminSession->addError($result['message']);
        }

        if ($activeTabId = $this->getRequest()->getParam('setup_active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        if ($activeTabTopId = $this->getRequest()->getParam('setup_active_tab_top_id')) {
            Mage::getSingleton('admin/session')->setActiveTabTopId($activeTabTopId);
        }

        $this->_redirect('*/*/index');
    }

    public function exportDesignAction()
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/importexport')) {
            Mage::getSingleton('core/session')->addError('You don\'t have acceess to perform this action');
            $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
            $this->getResponse()->setRedirect($refererUrl);
            return $this;
        }
        $action = $this->getRequest()->getParam('export','none');
        if(strtolower($action) == 'export'){
            $this->_exportFiles();
        } else {
            $this->_importFiles();
        }
    }

    private function _exportFiles()
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $finalCss = $helper->getFinalCssDefinitionFile();

//        $css = $helper->getLayoutXMLFileValues();
        $dataXml = Mage::getModel('Blugento_DesignCustomiser_Model_Layout_Save_Xml')->getFile();
        $img = Mage::getModel('Blugento_DesignCustomiser_Model_Scss_Save_Image_Xml');
//        $home = Mage::getModel('Bl');
        $imgFile = $img->getFile();



        $filePath = Mage::getBaseDir('var') . DS . "export";
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777);
        }
        $zipfile  = $filePath . DS . "backup" . time() . ".zip";
        
        $xml = simplexml_load_file($imgFile);
        $zip = new ZipArchive();
        if ($zip->open($zipfile, ZipArchive::CREATE)!==TRUE) {
            Mage::getSingleton('core/session')->addError('Error in export process!');
        } else {
            $zip->addFromString(basename($finalCss), file_get_contents($finalCss));
            $zip->addFromString(basename($dataXml), file_get_contents($dataXml));
            $zip->addFromString(basename($imgFile), file_get_contents($imgFile));
            $zip->addEmptyDir('images');
            $zip->addEmptyDir('scss');

            $imagePath = dirname(dirname($imgFile));

            foreach ($xml as $a => $b) {
                if ($b->value->__toString() == 'var_page_brand_logo.png') { // skip the store logo
                   continue;
                }
                $zip->addFromString( 'images'.DS.basename((string)$b->value->__toString()), file_get_contents($imagePath.DS.'images'.DS.(string)$b->value->__toString()));
            }

            $scssPath = dirname($dataXml);
            $scssFiles = scandir($scssPath);
            foreach ($scssFiles as $a => $b) {
                if(!in_array($b,array('.','..'))) {
                    $zip->addFromString('scss' . DS . basename((string)$b), file_get_contents($scssPath . DS . (string)$b));
                }
            }

        }

        // Export CMS Static Blocks

        $csv = '';
        $images = array();
        $i=0;
        $exportBlocks = explode(',', $this->getRequest()->getParam('export_blocks'));

        if (count($exportBlocks)) {
            $variable = Mage::getModel('core/variable')->load('export-blocks', 'code');
            if ($variable->getId()) {
                $variable ->setPlainValue($this->getRequest()->getParam('export_blocks'))->save();
            } else {
                Mage::getModel('core/variable')
                    ->setCode('export-blocks')
                    ->setName('Designcustomiser Export Blocks')
                    ->setPlainValue($this->getRequest()->getParam('export_blocks'))
                    ->save();
            }
        }

        foreach ($exportBlocks as $idStaticBlock) {

            $block = Mage::getModel('cms/block')->load($idStaticBlock, 'identifier');

            if (!$block->getId()) {
                continue;
            }

            $blockContent = array();
            $blockContent['content']    = base64_encode($block->getContent());
            $blockContent['title']      = $block->getTitle();
            $blockContent['id']         = $block->getId();
            $blockContent['identifier'] = $block->getIdentifier();
            $blockContent['is_active']  = $block->getIsActive();
            $blockContent['store']      = $block->getStore();

            $images[] = $this->extractImages($block->getContent());

            if($i==0){
                $csv .= implode(',',array_keys($blockContent))."\n";
                $i++;
            }
            $csv .= implode(',', $blockContent) . "\n";
        }

        if ($zip->open($zipfile, ZipArchive::CREATE) !== TRUE) {
            Mage::getSingleton('core/session')->addError('Error in export process!');
        } else {
            $zip->addFromString(self::CMSBLOCK, $csv);
            $zip->close();
        }

        if ($zip->open($zipfile, ZipArchive::CREATE) !== TRUE) {
            Mage::getSingleton('core/session')->addError('Error in export process!');
        } else {
            $zip->addEmptyDir('images');
            foreach ($images as $img) {
                foreach ($img as $im) {
                    if (file_exists($im)) {
                        $zip->addFromString('images' . DS . basename($im), file_get_contents($im));
                    }
                }
            }
        }

        $zip_created = false;
        if($zip->close())
        {
            $zip_created = true;
        }
        ob_clean();

        if ($zip_created && file_exists($zipfile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($zipfile).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zipfile));
            readfile($zipfile);
        } else {
            Mage::getSingleton('core/session')->addError('Error in export process!');
            $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
            $this->getResponse()->setRedirect($refererUrl);

            return $this;
        }
    }

    /**
     * Extract images from wysiwyg folder from html string
     * @param $html string
     * @return array|void
     */
    public function extractImages($str)
    {
        $startString = 'src="/media';
        $endString   = '" alt';

        $contents = array();
        $startDelimiterLength = strlen($startString);
        $endDelimiterLength = strlen($endString);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($str, $startString, $startFrom))) {
            $contentStart += $startDelimiterLength;
            $contentEnd = strpos($str, $endString, $contentStart);
            if (false === $contentEnd) {
                break;
            }
            $contents[] = Mage::getBaseDir('media') . substr($str, $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endDelimiterLength;
        }

        return $contents;
    }

    private function _importFiles()
    {
        $files = $_FILES;

        if(isset($files['filezip']) && isset($files['filezip']['size']) && $files['filezip']['size']>0){
            $tmp_name = $_FILES["filezip"]["tmp_name"];

            if (extension_loaded('fileinfo')) {
                $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                $fileMime = $fileInfo->file($_FILES['filezip']['tmp_name']);
                $validMimes = array(
                    'zip' => 'application/zip',
                    'zips' => 'application/octet-stream'
                );

                $fileExt = array_search($fileMime, $validMimes, true);

                if ($fileExt != 'zip' && $fileExt != 'zips') {
                    Mage::getSingleton('core/session')->addError('Invalid file format!');
                    $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            } else {
                $temp = explode(".", $_FILES['filezip']["name"]);
                $extension = strtolower(end($temp));
                if($extension != 'zip'){
                    Mage::getSingleton('core/session')->addError('Invalid file format. Require zip!');
                    $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }
            $img = Mage::getModel('Blugento_DesignCustomiser_Model_Scss_Save_Image_Xml');
            $imgFile = $img->getFile();
            $imagePath = dirname(dirname($imgFile));
            $filename = 'import'.time().'.zip';
            if(!file_exists(Mage::getBaseDir('var').DS."export")){
                mkdir(Mage::getBaseDir('var').DS."export");
            }
            $destFilePath = Mage::getBaseDir('var').DS."export".DS.$filename;
            if(move_uploaded_file($tmp_name, $destFilePath)) {
                $zip = new ZipArchive();
                if ($zip->open($destFilePath) === TRUE) {
                    array_map('unlink', glob($imagePath.DS."images".DS."*"));
                    array_map('unlink', glob($imagePath.DS.'scss'.DS.'*'));
                    $zip->extractTo($imagePath);
                    $zip->close();

                    unlink($imagePath.DS.'css'.DS.'final.css');

                    try {
                        copy($imagePath.DS.'final.css', $imagePath.DS.'css'.DS.'final.css');
                        copy($imagePath.DS.'variable-image.xml', $imagePath.DS.'scss'.DS.'variable-image.xml');
                        copy($imagePath.DS.'variable-layout.xml', $imagePath.DS.'scss'.DS.'variable-layout.xml');
                    } catch (Exception $e) {
                        unlink($imagePath.DS.'variable-image.xml');
                        unlink($imagePath.DS.'variable-layout.xml');
                        unlink($imagePath.DS.'final.css');
                        unlink($imagePath.DS.'cms_blocks.csv');

                        Mage::throwException($e->getMessage());
                    }

                    Mage::getSingleton('core/session')->addSuccess('Import succeeded!');
                } else {
                    Mage::getSingleton('core/session')->addError('Invalid files in archive!');
                }
            } else {
                Mage::getSingleton('core/session')->addError('Invalid files in archive!');
            }
        } else {
            Mage::getSingleton('core/session')->addError('No file submitted!');
        }

        // Import CMS Static Blocks
        if(!file_exists(Mage::getBaseDir('var') . DS . "extract")){
            mkdir(Mage::getBaseDir('var') . DS . "extract");
        }
        $extractPath = Mage::getBaseDir('var') . DS . "extract" . DS . time();
        $zip = new ZipArchive();
        if ($zip->open($destFilePath) === TRUE) {
            @mkdir($extractPath, 0777, true);
            $zip->extractTo($extractPath);
            $zip->close();
        }

        $blocks = file($extractPath. DS . self::CMSBLOCK);

        foreach ($blocks as $key=>$v){
            if($key>0) {
                $values = array_combine(explode(',', trim($blocks[0])), explode(',', trim($v)));

                $block = Mage::getModel('cms/block');
                $blockId = $block->load($values['identifier'], 'identifier')->getId();
                if($blockId) {
                    $block->delete();
                }
                try {
                    $staticBlock = array(
                        'title' => $values['title'],
                        'identifier' => $values['identifier'],
                        'content' => base64_decode($values['content']),
                        'is_active' => $values['is_active'],
                        'stores' => explode(',',$values['store'])
                    );
                    $block = Mage::getModel('cms/block');
                    $block->setData($staticBlock);
                    $block->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        if (empty($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('adminhtml/session');
        }

        //$this->_saveLayout(true);

        $this->_saveTemplate();

        $this->clearCache();

        $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
        Mage::app()->getResponse()->sendResponse();
        exit;
    }
    /**
     * Upload image file if valid
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveImages()
    {
        $collectionImages = Mage::getModel('blugento_designcustomiser/scss_variable_image_collection')->load();
        $helper = Mage::helper('blugento_designcustomiser/scss_image');

        $xmlSaveValues = $helper->getImageXMLSaveFileValues();
        $scssSaveValues = $helper->getImageScssFileValues();

        /**
         * @var $imageSave Blugento_DesignCustomiser_Model_Scss_Save_Image
         */
        $imageSave = $helper->getImageXMLFileValues();

        $collectionImagesScss = Mage::getModel('blugento_designcustomiser/scss_variable_image_collection')->load();
        foreach ($collectionImages as $variable) {
            $imageType = $variable->getType();
            $variableTitle = $this->__($variable->getTranslationPrefix() . '/title/' . $variable->getId());

            if (!$variable->getDefault()) {
                $collectionImages->removeItemByKey($variable->getId());
                $collectionImagesScss->removeItemByKey($variable->getId());
                continue;
            }

            $reset = $this->getRequest()->getParam('reset-image-' . $variable->getId());
            if (!empty($reset) && $variable->getDefault() == $reset) {
                $collectionImages->removeItemByKey($variable->getId());
                $imageSave->resetImage($variable);
                $variable->setSaveValue(array('name' => $variable->getDefault()));
                $variableScss = $collectionImagesScss->getVariableById($variable->getId());
                $variableScss->setSaveValue(array('name' => $variable->getDefault()));
                continue;
            }

            if (!isset($_FILES[$variable->getId()]) || $_FILES[$variable->getId()]['error'] == 4) {
                $variableOldValue = $xmlSaveValues->getVariableValue($variable);
                if (!$variableOldValue) {
                    $variable->setSaveValue(array('name' => $variable->getDefault()));
                    $variableScss = $collectionImagesScss->getVariableById($variable->getId());
                    $variableScss->setSaveValue(array('name' => $variable->getDefault()));
                } else {
                    $collectionImagesScss->removeItemByKey($variable->getId());
                }
                $collectionImages->removeItemByKey($variable->getId());
                continue;
            }

            if ($_FILES[$variable->getId()]['error'] != 0) {
                $this->_adminSession->addError($this->__('%s file is not uploaded ok. Please try again!', $variableTitle));
                $collectionImages->removeItemByKey($variable->getId());
                $collectionImagesScss->removeItemByKey($variable->getId());
                continue;
            }

            if ($imageType == 'default') {
                $arr = explode('.', $_FILES[$variable->getId()]['name']);
                $uploadType = strtolower(array_pop($arr));
                $uploadType = $helper->getAllowedVariableType($uploadType);

                if (!$variable->validateType($uploadType)) {
                    // uploaded file type not allowed
                    $this->_adminSession->addError($this->__('%s file type is not allowed. Allowed file types: %s.', $variableTitle, $variable->getXMLTypeString()));
                    $collectionImages->removeItemByKey($variable->getId());
                    $collectionImagesScss->removeItemByKey($variable->getId());
                    continue;
                }

                $imageType = $uploadType;
                $variable->uploadType = $imageType;
            }

            $saveValue = $_FILES[$variable->getId()];
            $variable->setSaveValue($saveValue);
            $variable->setUploadSizes();

            $nameArray = array_filter(explode('.', $saveValue['name']));
            $uploadType = strtolower(array_pop($nameArray));
            $nameArray = array_filter(explode('.', $variable->getDefault()));
            $saveValue['name'] = $nameArray[0] . '.' . $uploadType;
            $variableScss = $collectionImagesScss->getVariableById($variable->getId());
            $variableScss->setSaveValue($saveValue);
            $variableScss->setUploadSizes();

            $imageSize = explode('x', $variable->getSize());

            if ($imageType != 'svg' && (!is_array($imageSize) || !count($imageSize) == 2 || !$variable->validateRatio($imageSize))) {
                $collectionImages->removeItemByKey($variable->getId());
                $collectionImagesScss->removeItemByKey($variable->getId());
                continue;
            }
        }

        $result = $imageSave->save($collectionImages);
        foreach ($result['success'] as $variableId => $variableTitle) {
            $this->_adminSession->addSuccess($this->__('%s upload successfully.', $variableTitle));
        }
        foreach ($result['error'] as $variableId => $variableTitle) {
            $this->_adminSession->addError($this->__('%s upload error!', $variableTitle));
        }

        $xmlSaveValues->save($collectionImagesScss);
        $scssSaveValues->save($collectionImagesScss);

        return $this;
    }

    /**
     * Save data from Style Tab
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveStyleing()
    {
        $collectionScss = Mage::getModel('blugento_designcustomiser/scss_variable_collection')->load();

        $newValues = array();

        $autoValue = Mage::helper('blugento_designcustomiser')->getVariableAutoValue();
        foreach ($collectionScss as $variable) {
            $value = $this->getRequest()->getParam('set-inherit-' . $variable->getId());
            if ($variable->getAllowInherit()) {
                if ($value == 'yes') {
                    $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId() . '-inherit');
                } elseif ($value == $autoValue) {
                    $newValues[$variable->getId()] = $autoValue;
                } else {
                    $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId());
                }
            } elseif ($value == $autoValue) {
                $newValues[$variable->getId()] = $autoValue;
            } else {
                $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId());
            }
        }

        return $this->_saveVariables($newValues);
    }

    /**
     * Save data from Final CSS Tab
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveFinalCss()
    {
        $helper = Mage::helper('blugento_designcustomiser');

        try {
            $contentOld = $helper->getFinalCssDefinitionModel()->loadContent();
            $content = $this->getRequest()->getParam('final_css');
            if ($contentOld == $content || trim($content) == '') {
                return $this;
            }

            $model = Mage::getModel('blugento_designcustomiser/finalcss');
            $model->setData('css', $content);
            $model->save();

            $this->_adminSession->addSuccess($this->__('Final CSS saved successfully.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_adminSession->addError($this->__('Final CSS cannot be saved. ERROR: ') . $e->getMessage());
        }

        return $this;
    }

    public function deleteAction()
    {
        $versionId = $this->getRequest()->getParam('version_id');

        try {
            $model = Mage::getModel('blugento_designcustomiser/finalcss')->load($versionId);
            $model->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Final CSS Version deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Final CSS Version cannot be deleted. ERROR:: ') . $e->getMessage());
        }

        $refUrl = Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_design/index');
        $this->_redirectUrl($refUrl);
    }

    /**
     * Save style and images data from Template Tab
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveTemplate()
    {
        // Get preset values
        $template = $this->getRequest()->getParam('template');
        if (!$template) {
            return null;
        }

        $this->_saveTemplateStyleing($template);
        $this->_saveTemplateImages($template);

        return $this;
    }

    /**
     * Save style data from Template Tab
     * @param string $template
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveTemplateStyleing($template)
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getScssXMLFileValues();
        $scssSaveValues = $helper->getScssFileValues();

        $xmlSaveValues->setApplyTemplate($template);
        $collectionScss = Mage::getModel('blugento_designcustomiser/scss_variable_collection')->load();
        $presetValues = $collectionScss->getVariableValues();
        $xmlSaveValues->unsetApplyTemplate();

        return $this->_saveVariables($presetValues, true, $this->__('It was restored to %s.', $template));
    }

    /**
     * Save images data from Template Tab
     * @param string $template
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveTemplateImages($template)
    {
        $collectionImages = Mage::getModel('blugento_designcustomiser/scss_variable_image_collection')->load();
        $helper = Mage::helper('blugento_designcustomiser/scss_image');

        /**
         * @var $imageSave Blugento_DesignCustomiser_Model_Scss_Save_Image
         */
        $imageSave = $helper->getImageXMLFileValues();

        foreach ($collectionImages as $variable) {
            $imageSave->resetImage($variable, $template);
        }

        return $this;
    }

    /**
     * Save variable values
     *
     * @param array $newValues
     * @param bool $fromTemplate
     * @param string $messageAppend
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveVariables($newValues, $fromTemplate = false, $messageAppend = '')
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getScssXMLFileValues();
        $scssSaveValues = $helper->getScssFileValues();

        $collectionScss = Mage::getModel('blugento_designcustomiser/scss_variable_collection')->load();

        $alreadySendMessage = array();

        $titleParents = array();

        foreach ($collectionScss as $variable) {

            $variableId = $variable->getId();
            $variableTitle = $this->__($variable->getTranslationPrefix() . '/title/' . $variable->getId());

            $variableParentId = $variable->getParentId();
            if (!$variableParentId) {
                $titleParents[$variableId] = $variableTitle;
            }

            if (!$variable->getDefault()) {
                $collectionScss->removeItemByKey($variableId);
                continue;
            }

            $variableOldValue = $xmlSaveValues->getVariableValue($variable);

            if ($fromTemplate) {
                if (isset($newValues[$variableId])) {
                    $variableValue = $newValues[$variableId];
                } else {
                    $variableValue = $variableOldValue ? $variableOldValue : $variable->getDefault();
                }
            } else {
                if ($variable->getBasic() != 1) {
                    $variableValue = $variableOldValue ? $variableOldValue : $variable->getDefault();
                } else {
                    $variableValue = isset($newValues[$variableId]) ? $newValues[$variableId] : null;
                }
            }

            $variable->setSaveValue($variableValue);

            if (!$variable->validate()) {
                $collectionScss->removeItemByKey($variableId);
                $this->_adminSession->addError($this->__('%s is not valid.', $variableTitle));
                continue;
            }

            $changed = false;
            if (is_array($variableValue)) {
                $variableOldValueArr = explode('**', $variableOldValue);
                if (count($variableValue) != count($variableOldValueArr)) {
                    $changed = true;
                } else {
                    foreach ($variableValue as $value) {
                        if (!in_array($value, $variableOldValueArr)) {
                            $changed = true;
                            break;
                        }
                    }
                }
            } else {
                if ($variableValue != $variableOldValue) {
                    $changed = true;
                }
            }
            if ($changed) {
                if (!$variableParentId || !isset($alreadySendMessage[$variableParentId])) {

                    $title = (!$variableParentId) ?
                        $titleParents[$variableId] :
                        $titleParents[$variableParentId];

                    $alreadySendMessage[$variableParentId] = true;
                    $this->_adminSession->addSuccess($this->__('%s saved successfully. %s', $title, $this->__($messageAppend)));
                }
            }
        }

        $xmlSaveValues->save($collectionScss);

        // Remove auto values from collection
        $autoValue = Mage::helper('blugento_designcustomiser')->getVariableAutoValue();
        foreach ($collectionScss as &$variable) {
            if ($variable->getSaveValue() == $autoValue) {
                $variable->setSkipScss(true);
                if (!$variable->getAllowInherit()) {
                    $variableId = '$' . $variable->getId();
                    foreach ($collectionScss as &$variableChild) {
                        if ($variableChild->getSaveValue() == $variableId) {
                            $variableChild->setSkipScss(true);
                        }
                    }
                }
            }
        }

        $scssSaveValues->save($collectionScss);

        return $this;
    }

    /**
     * Save data from Layout Tab
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveLayout($hideResult=null)
    {
        $collectionLayout = Mage::getModel('blugento_designcustomiser/layout_variable_collection')->load();

        $newValues = array();

        foreach ($collectionLayout as $variable) {
            $newValues[$variable->getId()] = $this->getRequest()->getParam($variable->getId());
        }

        return $this->_saveVariablesLayout($newValues, $hideResult);
    }

    /**
     * Save variable values
     *
     * @param array $newValues
     * @param bool $forceUpdate
     * @param string $messageAppend
     * @return Blugento_DesignCustomiser_Adminhtml_DesignController
     */
    private function _saveVariablesLayout($newValues, $hideResult = false ,$forceUpdate = false, $messageAppend = '')
    {
        $helper = Mage::helper('blugento_designcustomiser');
        $xmlSaveValues = $helper->getLayoutXMLFileValues();
        $xmlSaveUpdateValues = $helper->getLayoutUpdateXMLFileValues();
        $scssSaveValues = $helper->getLayoutScssFileValues();
        $disableSaveValues = $helper->getLayoutXMLDisableModuleFileValues();

        $collectionLayout = Mage::getModel('blugento_designcustomiser/layout_variable_collection')->load();

        $alreadySendMessage = array();

        $titleParents = array();

        foreach ($collectionLayout as $variable) {

            $variableId = $variable->getId();
            $variableTitle = $this->__($variable->getTranslationPrefix() . '/title/' . $variable->getId());

            $titleParents[$variableId] = $variableTitle;

            $variableValue = isset($newValues[$variableId]) ? $newValues[$variableId] : null;

            if (!$variable->getDefault()) {
                $collectionLayout->removeItemByKey($variableId);
                continue;
            }

            $variable->setSaveValue($variableValue);

            if (!$variable->validate()) {
                $collectionLayout->removeItemByKey($variableId);
                if (!$hideResult) {
                    $this->_adminSession->addError($this->__('%s is not valid.', $variableTitle));
                }
                continue;
            }

            if ($variableValue != $xmlSaveValues->getVariableValue($variable)) {
                if (!isset($alreadySendMessage[$variableId])) {

                    $title = $titleParents[$variableId];

                    $alreadySendMessage[$variableId] = true;
                    if (!$hideResult){
                        $this->_adminSession->addSuccess($this->__('%s saved successfully. %s', $title, $this->__($messageAppend)));
                    }
                }
            }
        }

        $xmlSaveValues->save($collectionLayout);

        $scssSaveValues->save($collectionLayout);

        $xmlSaveUpdateValues->save($collectionLayout);

        $disableSaveValues->save($collectionLayout);

        return $this;
    }

    protected function clearCache()
    {
        $types = array('config', 'layout', 'block_html');
        $instance = Mage::app()->getCacheInstance();
        foreach ($types as $type) {
            $instance->cleanType($type);
            Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
        }

        if (!Mage::helper('core')->isModuleEnabled('MindMagnet_PageSpeed')) {
            return;
        }

        try {
            /* @var MindMagnet_PageSpeed_Helper_Data $helper */
            $helper = Mage::helper('mindmagnet_pagespeed');
            $helper->clearCache();
        } catch (Exception $e) {}
    }

    /**
     * Run grunt release trough Api call when designcustomiser is saved
     *
     * @return array
     */
    private function _runGruntRelease()
    {
        $helper = Mage::helper('blugento_designcustomiser');

        $token = $helper->getwAuthToken();

        if ($token) {
            $response = $helper->runGruntRelease($token);

            if ($response == 200) {
                $result['message'] = $helper->__('Grunt: Success!');
                $result['code'] = 'success';
            } elseif ($response == 401) {
                $result['message'] = $helper->__('Grunt: Authorization failed!');
                $result['code'] = 'error';
            } else {
                $releaseUrl = Mage::helper('adminhtml')->getUrl('*/*/grunt');
                $result['message'] = $helper->__('Grunt: Process error! Please try again later or') . ' <a href="' . $releaseUrl . '">' . $helper->__('Force Grunt') . ' </a>';
                $result['code'] = 'error';
            }
        } else {
            $result['message'] = $helper->__('Grunt: There is no token or "wAuthToken" file!');
            $result['code'] = 'error';
        }

        return $result;
    }

    /**
     * Force run grunt release trough Api call
     */
    public function gruntAction()
    {
        if (empty($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('adminhtml/session');
        }

        $result = $this->_runGruntRelease();

        if ($result['code'] == 'success') {
            $this->_adminSession->addSuccess($result['message']);
        } else {
            $this->_adminSession->addError($result['message']);
        }

        $this->_redirect('*/*/index');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/blugento_designcustomiser');
    }
}
