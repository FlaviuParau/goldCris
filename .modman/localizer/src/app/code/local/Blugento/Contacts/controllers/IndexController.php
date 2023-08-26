<?php
/**
 * Blugento Contacts
 * Contacts index controller rewrites Mage_Contacts_IndexController
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Contacts_IndexController extends Mage_Core_Controller_Front_Action
{

    const XML_PATH_EMAIL_RECIPIENT = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE = 'contacts/email/email_template';
    const XML_PATH_ENABLED = 'contacts/contacts/enabled';
    const XML_PATH_TEMPLATE = 'contacts/content/template';
    const XML_PATH_PAGE = 'contacts/content/cms_page';

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            $this->norouteAction();
        }
    }

    public function indexAction()
    {
        if (!$this->_redirectToContactPage()) {

            $this->loadLayout();

            $this->getLayout()->getBlock('contactForm')
                ->setFormAction(Mage::getUrl('*/*/post', array('_secure' => $this->getRequest()->isSecure())));

            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
        }
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ($post) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
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
	
	            $secret = Mage::getStoreConfig('magecomp/recaptcha_config/magecomp_recaptcha_validate');
	            if ($secret != '') {
		            $remoteIp = $_SERVER['REMOTE_ADDR'];
		            $url      = 'https://www.google.com/recaptcha/api/siteverify';
		            $response = $post['g-recaptcha-response'];
		
		            // Curl Request
		            $curl = curl_init();
		            curl_setopt($curl, CURLOPT_URL, $url);
		            curl_setopt($curl, CURLOPT_POST, true);
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		            curl_setopt($curl, CURLOPT_POSTFIELDS, array(
			            'secret' => $secret,
			            'response' => $response,
			            'remoteip' => $remoteIp
		            ));
		            $curlData = curl_exec($curl);
		            curl_close($curl);
		            $recaptcha = json_decode($curlData, true);
	            }
	
	            if ($recaptcha['success']) {
		            /* @var $mailTemplate Mage_Core_Model_Email_Template */
		            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
			            ->setReplyTo($post['email'])
			            ->sendTransactional(
				            Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
				            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
				            Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
				            null,
				            array('data' => $postObject)
			            );
	            }

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));

                if (!$this->_redirectToContactPage()) {
                    $this->_redirect('*/*/');
                }

                return;
            } catch (Exception $e) {
                Mage::logException($e);
                
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));

                if (!$this->_redirectToContactPage()) {
                    $this->_redirect('*/*/');
                }
                return;
            }

        } else {
            if (!$this->_redirectToContactPage()) {
                $this->_redirect('*/*/');
            }
        }
    }

    /**
     * Redirect to Contact CMS page if Blugento template is set
     */
    protected function _redirectToContactPage()
    {
        $template = Mage::getStoreConfig(self::XML_PATH_TEMPLATE);
        if (!$template) {
            $template = 'default';
        }

        if ($template == 'blugento') {
            $page = Mage::getStoreConfig(self::XML_PATH_PAGE);
            if (!$page) {
                $page = 'contact-page';
            }
            $this->_redirect($page);

            return true;
        }

        return false;
    }

}
