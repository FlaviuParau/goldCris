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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Adminhtml_HomepagemanagerController extends Mage_Adminhtml_Controller_Action
{
    const SLIDERSFILE = 'sliders.csv';
    const HOMEPAGE    = 'homepage.csv';
    const BANNERSFILE = 'banners.csv';
    const CMSBLOCK    = 'cms_blocks.csv';

    private $_adminSession = null;

    /**
     * Determine ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->loadLayout();

        $this->renderLayout();
    }

    /**
     * Save layout action
     */
    public function saveAction()
    {
        if (empty($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('adminhtml/session');
        }

        $this->_saveLayout();
        $this->_redirect('*/*/index');
    }

    /**
     * Ajax method for displaying the widget preview content
     */
    public function widgetpreviewAction()
    {
        $this->loadLayout();

        $block = $this->getLayout()->createBlock(
            'blugento_homepagemanager/adminhtml_widget_preview',
            'adminhtml_widget_preview'
        );

        $block->addData(array(
            'widget_type'   => $this->getRequest()->getParam('type'),
            'widget_params' => $this->getRequest()->getParam('params')
        ));

        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

    /**
     * Save layout
     * @return $this
     */
    private function _saveLayout()
    {
        $items = $this->getRequest()->getParam('form_values');
        if (!$items) {
            return false;
        }

        $items = @json_decode($items, true);
        if (!$items) {
            return false;
        }

        try {
            $helper = Mage::helper('blugento_homepagemanager');
            $xmlSaveValues = $helper->getLayoutXMLFileValues();
            $xmlSaveValues->save($items, $this->getRequest()->getParams());

            $this->_adminSession->addSuccess('Homepage layout saved successfully!');
        } catch (Exception $e) {
            $this->_adminSession->addError('Homepage layout could not be saved!' . $e->getMessage());
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Export files
     * @return $this
     */
    public function exportFiles()
    {
        if (!file_exists(Mage::getBaseDir('var') . DS . "export")) {
            mkdir(Mage::getBaseDir('var') . DS . "export", 0777);
        }

        $xmlModel = Mage::getModel('Blugento_HomepageManager_Model_Layout_Save_Xml');

        $zipfile = Mage::getBaseDir('var') . DS . "export" . DS . "backup_home" . time() . ".zip";
        $storeId = $this->getRequest()->getPost('storeId');

        $storeId = !is_null($storeId) ? $storeId : 1;

        $helper = Mage::helper('blugento_homepagemanager');

        $userDir = $helper->getUserDirectoryName();

        // Load store specific definition file

        $fileSkinPath = $userDir . DS . $xmlModel->getXmlSkinPath() . DS . 'store_' . $storeId . '_' .
            $xmlModel->getXmlFilename();

        $zip = new ZipArchive();
        if ($zip->open($zipfile, ZipArchive::CREATE) !== TRUE) {
            Mage::getSingleton('core/session')->addError('Error in export process!');
        } else {
            $zip->addFromString('xmlfile.txt',basename($fileSkinPath));
            $zip->addFromString(basename($fileSkinPath), file_get_contents(Mage::getBaseDir('skin') . DS .
                'frontend' . DS . 'blugento' . DS . 'default' . DS . $fileSkinPath));

            $zip->close();
        }

        $xml = simplexml_load_string(file_get_contents(Mage::getBaseDir('skin') . DS . 'frontend' . DS .
            'blugento' . DS . 'default' . DS . $fileSkinPath), null, LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        $idsStaticBlock = array();

        $slidersGroupName = array();
        if (isset($array['nodes']['row'])) {
            foreach ($array['nodes']['row'] as $k => $v) {
                if (isset($v['cols'])) {
                    $validBlock = false;
                    foreach ($v['cols'] as $key => $val) {
                        if (isset($val['type']) && $val['type'] == 'cms/widget_block') {
                            $validBlock = true;
                            if( (strlen($val['params']) > 20) || (is_array($val['params']) && count($val['params']))) {
                                $params = Mage::helper('core')->jsonDecode($val['params']);
                                foreach ($params as $kp => $vp) {
                                    if ($vp['name'] == 'parameters[block_id]') {
                                        $idsStaticBlock[] = $vp['value'];
                                    }
                                }
                            }
                        }
                        if (isset($val['type']) && $val['type'] == 'blugento_sliders/widget_slider') {
                            if( (strlen($val['params']) > 20) || (is_array($val['params']) && count($val['params']))) {
                                $params = Mage::helper('core')->jsonDecode($val['params']);
                                foreach ($params as $kp => $vp) {
                                    if ($vp['name'] == 'parameters[banner_code]') {
                                        $slidersGroupName[] = $vp['value'];
                                    }
                                }
                            }
                        }
                    }
                    if (!$validBlock) {
                        foreach ($v['cols']['col'] as $key => $val) {
                            if (isset($val['type']) && $val['type'] == 'cms/widget_block') {
                                if( (strlen($val['params']) > 20) || (is_array($val['params']) && count($val['params']))) {
                                    $params = Mage::helper('core')->jsonDecode($val['params']);
                                    foreach ($params as $kp => $vp) {
                                        if ($vp['name'] == 'parameters[block_id]') {
                                            $idsStaticBlock[] = $vp['value'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         * Add home CMS page layout details
         */
        $vars = array('root_template', 'meta_keywords', 'meta_description');
        $csv = implode(',', $vars) . "\n";

        $homepage = Mage::getModel('cms/page')->load('home', 'identifier');
        $homePageDetails = new Varien_Object();
        $homePageDetails->setData('root_template', $homepage->getData('root_template'));
        $homePageDetails->setData('meta_keywords', $homepage->getData('meta_keywords'));
        $homePageDetails->setData('meta_description', $homepage->getData('meta_description'));

        $sliderData = $homePageDetails->getData();
        $csv .= implode(',', $sliderData) . "\n";
        if (count($sliderData)) {
            $zip = new ZipArchive();
            if ($zip->open($zipfile, ZipArchive::CREATE) !== TRUE) {
                Mage::getSingleton('core/session')->addError('Error in export process!');
            } else {
                $zip->addFromString(self::HOMEPAGE, $csv);
                $zip->close();
            }
        }
        // END Add home CMS page layout details

        /**
         * Export Sliders
         */
        $vars = array('group_id', 'store_id', 'title', 'code', 'is_enabled', 'carousel_animate', 'carousel_duration',
            'carousel_auto', 'carousel_effect', 'controls_position', 'is_wide', 'carousel_autospeed');
        $csv = implode(',', $vars) . "\n";

        $sliderIds = array();
        foreach ($slidersGroupName as $sliderGroupName) {
            $slider = Mage::getModel('blugento_sliders/group')->load($sliderGroupName, 'code');
            $sliderData = $slider->getData();

            $csv .= implode(',', $sliderData) . "\n";
            $sliderIds[] = $slider->getGroupId();
        }

        if (count($slidersGroupName)) {
            $zip = new ZipArchive();
            if ($zip->open($zipfile, ZipArchive::CREATE) !== TRUE) {
                Mage::getSingleton('core/session')->addError('Error in export process!');
            } else {
                $zip->addFromString(self::SLIDERSFILE, $csv);
                $zip->close();
            }
        }
        // END Export Sliders

        /**
         * Export Banners
         */
        $res = $result = array();

        $vars = array('banner_id', 'group_id', 'slider_code', 'title', 'url', 'url_target', 'image', 'alt_text', 'html',
            'sort_order', 'is_enabled','background_color', 'tablet_image', 'mobile_image', 'tablet_banner_width', 'tablet_banner_height',
	        'mobile_banner_width', 'mobile_banner_height');

        $csv = implode(',', $vars) . "\n";
        $model = new Blugento_Sliders_Model_Banner();

        $banners = $model->getCollection();
        if (count($banners) > 0) {
            foreach ($banners as $banner) {

                if (!in_array($banner->getGroupId(), $sliderIds)) {
                    continue;
                }

                foreach ($vars as $a) {
                    $camelCase = preg_replace_callback('/_(.?)/', function ($matches) {
                        return ucfirst($matches[1]);
                    }, 'get_' . $a);
                    if ($a == 'html') {
                        $res[$a] = base64_encode($banner->$camelCase());
                    } elseif($a=='background_color') {
                        $res[$a] = $banner->getData('background_color');
                    } else {
                        $res[$a] = $banner->$camelCase();
                    }
                }

                if(isset($res['group_id'])) {
                    $slider = Mage::getModel('blugento_sliders/group')->load($res['group_id'], 'group_id');
                    $res['slider_code'] = $slider->getCode();
                }

                if($res['is_enabled']==1) {
                    $csv .= implode(',', $res) . "\n";

                    $result[] = $res;
                }
            }
        }

        if ($zip->open($zipfile, ZipArchive::CREATE) !== TRUE) {
            Mage::getSingleton('core/session')->addError('Error in export process!');
        } else {
            $zip->addFromString(self::BANNERSFILE, $csv);
            $zip->close();
        }
        // END Export Banners

        $csv = '';
        $images = array();
        $i=0;

        foreach ($idsStaticBlock as $idStaticBlock) {
//            if ($idStaticBlock == 17) {
//                continue;
//            }
            $block = Mage::getModel('cms/block')->load($idStaticBlock);

            $blockContent = array();
            $blockContent['content']    = base64_encode($block->getContent());
            $blockContent['title']      = $block->getTitle();
            $blockContent['id']         = $block->getId();
            $blockContent['identifier'] = $block->getIdentifier();
            $blockContent['is_active']  = $block->getIsActive();
            $blockContent['store']      = $block->getStore();

            $images[] = $this->extractImages($block->getContent());
            $images[] = $this->extractImagesNewType($block->getContent());

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
            $zip->addEmptyDir('sliders');

            $dir = Mage::getBaseDir('media') . DS . 'blugento_sliders';
            $images = $this->_scanDir($dir);
            foreach ($images as $img) {
                $im = $dir . DS . $img;
                if (file_exists($im)) {
                    $zip->addFromString('sliders' . DS . $img, file_get_contents($im));
                }
            }
            $zip_created = false;
            if ($zip->close()) {
                $zip_created = true;
            }
            ob_clean();

            if ($zip_created && file_exists($zipfile)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($zipfile) . '"');
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
        return;
    }

    private function _scanDir($dir, $dirPath = null) {
        if ($dir == '') {
            return array();
        } else {
            $files = array();
            $subresults = array();
        }
        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }
        if (!$dirPath) {
            $dirPath = realpath($dir) . DS;
        }

        $scanFiles = scandir($dir);
        foreach ($scanFiles as $key => $value) {
            if (($value != '.') && ($value != '..')) {
                $path = realpath($dir . DS . $value);
                if (is_dir($path)) {
                    $subdirresults = $this->_scanDir($path, $dirPath);
                    $files = array_merge($files, $subdirresults);
                } else {
                    $subresults[] = str_replace($dirPath,'', $path);
                }
            }
        }
        if (count($subresults) > 0) {
            $files = array_merge($subresults, $files);
        }

        return $files;
    }

    /**
     * Export Action
     */
    public function exportAction(){
        $action = $this->getRequest()->getParam('export','none');
        if($action == 'Export'){
            $this->exportFiles();
            exit();
        }else{
            $action = $this->getRequest()->getParam('import','none');
            if($action == 'Import'){
                $this->importFile();
            }
        }
    }
    public function importFile()
    {
        $files = $_FILES;
        if(isset($files['filezip']) && isset($files['filezip']['size']) && $files['filezip']['size']>0) {
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
            $filename = 'import_home'.time().'.zip';
            if(!file_exists(Mage::getBaseDir('var').DS."export")){
                mkdir(Mage::getBaseDir('var').DS."export");
            }
            $destFilePath = Mage::getBaseDir('var').DS."export".DS.$filename;
            $extractPath = Mage::getBaseDir('var').DS."extract".DS.time();

            if(move_uploaded_file($tmp_name, $destFilePath)){
                $zip = new ZipArchive();
                if ($zip->open($destFilePath) === TRUE) {

                    @mkdir($extractPath,0777,true);
                    array_map('unlink', glob($extractPath.DS."images".DS."*"));
                    $zip->extractTo($extractPath);
                    $zip->close();
                   
                    $folderName = explode(".", $_FILES['filezip']["name"])[0];
	                if (in_array($folderName, scandir($extractPath))) {
		                $this->importDataFrom($extractPath . DS . $folderName);
	                } else {
		                $this->importDataFrom($extractPath);
	                }
	                
                    Mage::getSingleton('core/session')->addSuccess('Import succeeded!');
                } else {
                    Mage::getSingleton('core/session')->addError('Invalid files in archive!');
                }
            }else{
                Mage::getSingleton('core/session')->addError('Invalid files in archive!');
            }
        } else {
            //echo "<pre>";var_dump('$path::');die();
            Mage::getSingleton('adminhtml/session')->addError('No file submitted!');

            Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
            Mage::app()->getResponse()->sendResponse();
            exit;
        }
    }

    public function importDataFrom($path)
    {
        $storeId = $this->getRequest()->getPost('storeId');
        $storeId = !is_null($storeId) ? $storeId : 1;

        $helper = Mage::helper('blugento_homepagemanager');
        $userDir = $helper->getUserDirectoryName();
        $xmlModel = Mage::getModel('Blugento_HomepageManager_Model_Layout_Save_Xml');
        $fileSkinPath = $userDir . DS . $xmlModel->getXmlSkinPath() . DS . 'store_' . $storeId . '_' .
            $xmlModel->getXmlFilename();


        $xmlFile = file_get_contents($path.DS.'xmlfile.txt');

        unlink(Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'blugento' . DS . 'default' . DS . $fileSkinPath);

        if ( !file_exists(Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'blugento' . DS . 'default' . DS . $userDir . DS . $xmlModel->getXmlSkinPath()) ) {
            mkdir(Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'blugento' . DS . 'default' . DS . $userDir . DS . $xmlModel->getXmlSkinPath(), 0777);
        }

        copy($path.DS.$xmlFile,Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'blugento' . DS . 'default' . DS . $fileSkinPath); //uncomment

        $sliders = file($path.DS.self::SLIDERSFILE);

        //Banner groups import
        $vars = array('group_id', 'store_id','title', 'code', 'is_enabled', 'carousel_animate', 'carousel_duration',
            'carousel_auto', 'carousel_effect', 'controls_position', 'is_wide', 'carousel_autospeed');
        $model = new Blugento_Sliders_Model_Group();

        $groups = $model->getCollection();
        foreach ($groups as $g){
            $g->delete();
        }
        $sql = 'ALTER TABLE blugento_sliders_group AUTO_INCREMENT = 1';
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->query($sql);

        foreach ($sliders as $k=>$v){
            if($k>0) {
                $values = array_combine(explode(',',$sliders[0]),explode(',',$v));
                $group = new Blugento_Sliders_Model_Group();
                foreach ($vars as $a) {
                    $camelCase = preg_replace_callback('/_(.?)/', function ($matches) {
                        return ucfirst($matches[1]);
                    }, 'set_' . $a);
                    if($a!='group_id') {
                        if (isset($values[$a])) {
                            $group->$camelCase($values[$a]);
                        }
                    }
                }
                $group->setStoreId($storeId);
                $group->getCarouselAutospeed();

                $group->save();
            }
        }

        //Banners import
        $model = new Blugento_Sliders_Model_Banner();
        $bann = $model->getCollection();
        foreach ($bann as $b){
            $b->delete();
        }

        $sql = 'ALTER TABLE blugento_sliders_banner AUTO_INCREMENT = 1';
        $connection->query($sql);

        $banners = file($path.DS.self::BANNERSFILE);
        $vars = array('title', 'url', 'url_target', 'image', 'alt_text', 'html',
            'sort_order', 'is_enabled','background_color', 'tablet_image', 'mobile_image', 'tablet_banner_width',
	        'tablet_banner_height', 'mobile_banner_width', 'mobile_banner_height');
        foreach ($banners as $key=>$v){
            if($key>0) {
                $values = array_combine(explode(',',trim($banners[0])),explode(',',trim($v)));
                unset($values['banner_id']);
                $values['html'] = base64_decode($values['html'],false);

                if (isset($values['slider_code'])) {
                    $slider = Mage::getModel('blugento_sliders/group')->load($values['slider_code'], 'code');
                    $values['group_id'] = $slider->getGroupId();
                }

                $banner = new Blugento_Sliders_Model_Banner();
                $banner->addData($values);
                $banner->save();
            }
        }

        if(!file_exists(Mage::getBaseDir('media') . DS . "wysiwyg")){
            mkdir(Mage::getBaseDir('media' ). DS . "wysiwyg");
        }

        //Import Images used in cms blocks
        $files = scandir($path . DS . 'images');
        foreach ($files as $f){
            if(!in_array($f,array('.','..'))){
                copy($path.DS.'images'.DS.$f,Mage::getBaseDir('media') . DS . 'wysiwyg' . DS .basename($f));
            }
        }
        if(!file_exists(Mage::getBaseDir('media') . DS . 'blugento_sliders')){
            mkdir(Mage::getBaseDir('media') . DS . 'blugento_sliders');
        }
        //Import Images used in banners
        $files = $this->_scanDir($path . DS . 'sliders');

        foreach ($files as $f){
            copy($path . DS . 'sliders' . DS . $f,Mage::getBaseDir('media') . DS . 'blugento_sliders' . DS . $f);
        }

        //Import cms Blocks
        Mage::app()->setCurrentStore($storeId);
        $cms = file($path.DS.self::CMSBLOCK);

        foreach ($cms as $key=>$v){
            if($key>0) {
                $values = array_combine(explode(',', trim($cms[0])), explode(',', trim($v)));

                $block = Mage::getModel('cms/block')->load($values['identifier'], 'identifier');

                if ($block->getId()){
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
                    $block->setData($staticBlock)->save();

                    $this->updateIdXml($fileSkinPath, $values['id'], $block->getBlockId());
                } catch (Exception $e) {
                    Mage::logException($e);
                    //echo "<pre>"; var_dump($values['identifier'], $e->getMessage()); die();
                }
            }
        }

        //Import homepage data
        $homepage = file($path . DS . self::HOMEPAGE);
        foreach ($homepage as $key=>$v){
            if($key>0) {
                $values = array_combine(explode(',', trim($homepage[0])), explode(',', trim($v)));
                try {
                    $data = array(
                        'root_template' => $values['root_template'],
                        'meta_keywords' => $values['meta_keywords'],
                        'meta_description' => $values['meta_description']
                    );

                    $page = Mage::getModel('cms/page')->load('home', 'identifier');

                    foreach ($data as $key=>$val) {
                        $page->setData($key, $val);
                    }

                    $page->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        $this->_redirectReferer();
    }

    public function updateIdXml($xmlFile,$oldId,$newId)
    {
        $xml = simplexml_load_string(file_get_contents(Mage::getBaseDir('skin') . DS . 'frontend' . DS .
            'blugento' . DS . 'default' . DS . $xmlFile), null, LIBXML_NOCDATA);
        foreach($xml->nodes->row as $x) {
            if($x->cols->col->type=='cms/widget_block') {
                $x->cols->col->text=str_replace('block_id="'.$oldId.'"','block_id="'.$newId.'"',$x->cols->col->text);
                $x->cols->col->params=str_replace('parameters[block_id]","value":"'.$oldId.'"','parameters[block_id]","value":"'.$newId.'"',$x->cols->col->params);
            }
        }
        if ($xml) {
            $xml->asXML(Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'blugento' . DS . 'default' . DS . $xmlFile);
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
    /**
     * Extract images from wysiwyg folder from html string
     * @param $html string
     * @return array|void
     */
    public function extractImagesNewType($str)
    {

        $startString = 'src="{{media url="';
        $endString   = '"';

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
            $contents[] = Mage::getBaseDir('media') . DS . substr($str, $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endDelimiterLength;
        }

        return $contents;
    }
}
