<?php
/**
 * Blugento Form
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Form
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Form_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Send email Action
     */
    public function sendemailAction()
    {
        // Fetch submitted params
        $params = $this->getRequest()->getParams();

        $formType = isset($params['form_type']) ? $params['form_type'] : 'order';

        $incrementId = null;
        if ($formType == 'contact') {
            $incrementId = 'C' . Mage::getSingleton('eav/config')
                ->getEntityType('contact')
                ->fetchNewIncrementId();
        } else if ($formType == 'order') {
            $incrementId = 'O' . Mage::getSingleton('eav/config')
                ->getEntityType('orderform')
                ->fetchNewIncrementId();
        } else if ($formType == 'website') {
            $incrementId = 'W' . Mage::getSingleton('eav/config')
                ->getEntityType('websiteform')
                ->fetchNewIncrementId();
        } else {
            $incrementId = 'CU' . Mage::getSingleton('eav/config')
                ->getEntityType('custominc')
                ->fetchNewIncrementId();
        }

        $params['id'] = $incrementId;
	
	    $secret = Mage::getStoreConfig('Blugento_Form/recaptcha/recaptcha_hey')
		    ?: Mage::getStoreConfig('magecomp/recaptcha_config/magecomp_recaptcha_validatesite');
        if ($secret != '') {
	        $remoteIp = $_SERVER['REMOTE_ADDR'];
	        $url      = 'https://www.google.com/recaptcha/api/siteverify';
	        $response = $params['g-recaptcha-response'];
	
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

        if (isset($params['template_id'])) {
            $templateId = $params['template_id'];
        } else {
            $templateId = Mage::getStoreConfig('Blugento_Form/template/template_id');
        }

        if (!$templateId) {
            $templateId = 1;
        }

        // Sender
        $sender = array(
            'name'  => Mage::getStoreConfig('Blugento_Form/sender/sender_name'),
            'email' => Mage::getStoreConfig('Blugento_Form/sender/sender_email')
        );
        if (!$sender['name']) {
            $sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
            if (!$sender['name']) {
                $sender['name'] = 'Blugento';
            }
        }
        if (!$sender['email']) {
            $sender['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
            if (!$sender['email']) {
                $sender['email'] = 'contact@blugento.com';
            }
        }

        // Recepient
        $email      = Mage::getStoreConfig('Blugento_Form/recipient/recipient_email');
        $emailName  = Mage::getStoreConfig('Blugento_Form/recipient/recipient_name');

        if (!$email) {
            $email = $sender['email'];
        }
        if (!$emailName) {
            $emailName = $sender['name'];
        }
        $vars = $params;

        try {
            $storeId = Mage::app()->getStore()->getId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(true);

            $text = array();
            foreach ($params as $key => $val) {
                if ($key == 'hideit' || $key == 'redirect_to') {
                    continue;
                }
                if (is_array($val)) {
                    $val = implode(', ', $val);
                }
                $text[] = "$key: $val";
            }
            $vars['form_text'] = implode("\n", $text);

            $transactionalEmail = Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

            foreach ($_FILES as $key => $val) {
                if ($_FILES[$key]['tmp_name']) {
                    $transactionalEmail
                        ->getMail()
                        ->createAttachment(
                            file_get_contents($_FILES[$key]['tmp_name']),
                            Zend_Mime::TYPE_OCTETSTREAM,
                            Zend_Mime::DISPOSITION_ATTACHMENT,
                            Zend_Mime::ENCODING_BASE64,
                            $_FILES[$key]['name']
                        );
                }
            }
            
            if ($recaptcha['success']) {
	            $transactionalEmail->sendTransactional($templateId, $sender, $email, $emailName, $vars, $storeId);
	            Mage::getSingleton('core/session')->addSuccess($this->__('Message sent.'));
            } else {
	            $session = Mage::getSingleton('core/session');
	            $session->addError($this->__('Message not sent.'));
	            $this->_redirectReferer();
            }
        } catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError($this->__('Message not sent.'));
        }

        $this->_logEmail($params);

        if (isset($params['redirect_to']) && $params['redirect_to'] && $recaptcha['success']) {
            $url = Mage::getUrl($params['redirect_to']);
            $this->_redirectUrl($url . '?id=' . $incrementId);
        } else {
            $this->_redirect('/' . '?id=' . $incrementId);
        }
    }

    private function _getFormId()
    {
        $storeId = Mage::app()->getStore()->getId();
        $nextNum = Mage::getSingleton('eav/config')->getEntityType('customer')->fetchNewIncrementId($storeId);

        return $nextNum;
    }

    protected function _logEmail($params)
    {
        if (!Mage::getStoreConfig('Blugento_Form/logs/logs_enabled')) {
            return false;
        }

        $text = "\n------ " . date('Y-m-d H:i:s') . " ------\n";
        foreach ($params as $key => $val) {
            $text .= "$key => $val\n";
        }

        $logFile = Mage::getStoreConfig('Blugento_Form/logs/logs_filename');
        if (!$logFile) {
            $logFile = 'forms.log';
        }

        Mage::log($text, null, $logFile, true);

        return true;
    }
}
