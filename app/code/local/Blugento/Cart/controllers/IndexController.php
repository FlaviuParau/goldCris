<?php
/**
 * Blugento Cart Settings
 * Contacts index controller rewrites Mage_Contacts_IndexController
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Cart
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Cart_IndexController extends Mage_Core_Controller_Front_Action
{
	const XML_PATH_ENABLED         = 'blugento_cart/global_config/enable';
	const XML_PATH_EMAIL_RECIPIENT = 'blugento_cart/email/recipient_email';
	const XML_PATH_EMAIL_SENDER    = 'blugento_cart/email/sender_email_identity';
	const XML_PATH_EMAIL_TEMPLATE  = 'blugento_cart/email/email_template';
	const XML_PATH_PAGE            = 'blugento_cart/global_config/cms_page';
	const XML_LOG_EMAIL            = 'blugento_cart/email/logs';
	const XML_LOG_EMAIL_FILE       = 'blugento_cart/email/logs_file';
	
	public function preDispatch()
	{
		parent::preDispatch();
		
		if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
			$this->norouteAction();
		}
	}
	
	public function indexAction()
	{
		$post = $this->getRequest()->getPost();
		
		if ($post) {
			$translate = Mage::getSingleton('core/translate');
			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);
			
			$this->_logEmail($post);
			
			try {
				$postObject = new Varien_Object();
				$postObject->setData($post);
				
				$error = false;
				if (!Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
					$error = true;
				}
				if (!Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
					$error = true;
				}
				if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
					$error = true;
				}
				if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
					$error = true;
				}
				if ($error) {
					throw new Exception();
				}
				
				$mailTemplate = Mage::getModel('core/email_template');

                if (isset($_FILES['blugento_files']['name']) && $_FILES['blugento_files']['name'] != '') {
                    try {
                        $fileName		= $_FILES['blugento_files']['name'];

                        $uploader		= new Varien_File_Uploader('blugento_files');
                        $uploader->setAllowedExtensions(array('doc', 'docx','pdf', 'jpg', 'png', 'zip', 'csv', 'txt')); //add more file types you want to allow
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'inquiry';
                        if(!is_dir($path)){
                            mkdir($path, 0777, true);
                        }
                        $uploader->save($path . DS, $fileName );

                        //sending file as attachment
                        $attachmentFilePath = Mage::getBaseDir('media') . DS . 'inquiry' . DS . $fileName;
                        if(file_exists($attachmentFilePath)){
                            $fileContents = file_get_contents($attachmentFilePath);
                            $attachment   = $mailTemplate->getMail()->createAttachment($fileContents);
                            $attachment->filename = $fileName;
                        }

                    } catch (Exception $e) {
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                    }
                }

                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($post['email'])
					->sendTransactional(
						Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
						Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
						Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
						null,
						array('data' => $postObject)
					);
				
				if (!$mailTemplate->getSentSuccess()) {
					throw new Exception();
				}
				
				Mage::getSingleton('core/session')->setCartSettingsSession($postObject->getData());
				$translate->setTranslateInline(true);
				$backUrl = $this->_getProductUrl();
				Mage::getSingleton('customer/session')->addSuccess('<a href="' . $backUrl . '">' . Mage::helper('blugento_cart')->__('Continue shopping') . '</a>');
				
				if (isset($post['redirect_to']) && $post['redirect_to'] != '') {
					$url = Mage::getUrl($post['redirect_to']);
					Mage::app()->getFrontController()->getResponse()->setRedirect($url);
					return;
				}
				return;
			} catch (Exception $e) {
				Mage::logException($e);
				$translate->setTranslateInline(true);
				Mage::getSingleton('customer/session')->addError(Mage::helper('blugento_cart')->__('Unable to submit your request. Please, try again later'));
				if (!$this->_redirectToPage()) {
					$this->_redirect('/');
				}
				return;
			}
		} else {
			if (!$this->_redirectToPage()) {
				$this->_redirect('/');
			}
		}
	}
	
	/**
	 * Redirect to CMS page if Blugento template is set
	 */
	protected function _redirectToPage()
	{
		$page = Mage::getStoreConfig(self::XML_PATH_PAGE);
		if (!$page) {
			$page = '/';
		}
		
		$productID = $this->getRequest()->getPost('inquiry_product_id');
		if ($productID) {
			$this->_redirect($page, array('_query' => array('product' => $productID)));
			return true;
		}
		
		$this->_redirect($page);
		return true;
	}
	
	protected function _getProductUrl()
	{
		$page = Mage::getStoreConfig(self::XML_PATH_PAGE);
		if (!$page) {
			$page = '/';
		}
		
		$productID = $this->getRequest()->getPost('inquiry_product_id');
		if ($productID) {
			try {
				$product = Mage::getModel('catalog/product')->load($productID);
				$url = $product->getProductUrl();
				$base = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
				$url = explode($base, $url);
				$page = '/' . $url[1];
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
		
		return $page;
	}
	
	protected function _logEmail($params)
	{
		if (!Mage::getStoreConfig(self::XML_LOG_EMAIL)) {
			return false;
		}
		
		$text = "\n------ " . date('Y-m-d H:i:s') . " ------\n";
		foreach ($params as $key => $val) {
			$text .= "$key => $val\n";
		}
		
		$logFile = Mage::getStoreConfig(self::XML_LOG_EMAIL_FILE);
		if (!$logFile) {
			$logFile = 'custom_cart.log';
		}
		
		Mage::log($text, null, $logFile, true);
		
		return true;
	}
}
