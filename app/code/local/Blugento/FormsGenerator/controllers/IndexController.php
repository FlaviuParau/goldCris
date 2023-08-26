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
 * @package     Blugento_FormsGenerator
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once(Mage::getModuleDir('controllers','Blugento_Form').DS.'IndexController.php');

class Blugento_FormsGenerator_IndexController extends Blugento_Form_IndexController
{
    /**
     * @var array
     */
    protected $hiddenFields = ['form_id', 'form_id_spam'];

    /**
     * Sends an email with form data.
     *
     */
    public function sendemailAction(){
        // Fetch submitted params
        $params = $this->getRequest()->getParams();

        // check if form is empty
        $emptyForm = true;
        foreach ($params as $paramKey => $paramValue) {
            if (!in_array($paramKey, $this->hiddenFields)) {
                if ($paramValue != null && $paramValue != '') {
                    $emptyForm = false;
                    break;
                }
            }
        }

        if ($emptyForm) {
            Mage::getSingleton('core/session')->addError($this->__('You cannot submit an empty form.'));
            $this->_redirectReferer();
            return;
        }

        /** @var Blugento_FormsGenerator_Model_Forms $model */
        $form = Mage::getModel('blugento_formsgenerator/forms')->load($params['form_id']);

        if (isset($params['template_id'])) {
            $templateId = $params['template_id'];
        } else {
            $templateId = $form->getEmailTemplateId();
        }

        if (!$templateId) {
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

        // Recipient
        $email = $form->getRecipientEmail();
        $emailName = $form->getRecipient();

        if(!$email){
            $email = Mage::getStoreConfig('Blugento_Form/recipient/recipient_email');
        }

        if(!$emailName){
            $emailName = Mage::getStoreConfig('Blugento_Form/recipient/recipient_name');
        }

        if (!$email) {
            $email = $sender['email'];
        }
        if (!$emailName) {
            $emailName = $sender['name'];
        }
        $vars = $params;
        $validation = true;

        try {
            $storeId = Mage::app()->getStore()->getId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(true);

            $transactionalEmail = Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

            foreach ($_FILES as $key => $file) {
                if(is_array($file['name'])){
                    $totalSize = 0;
                    foreach ($file['size'] as $size) {
                        $totalSize += $size;
                    }
                    if ($totalSize < 20000000) {
                        foreach ($file['tmp_name'] as $keyName => $fileName) {
                            if ($file['size'][$keyName] > 0 && $file['error'][$keyName] == 0) {
                                $transactionalEmail
                                    ->getMail()
                                    ->createAttachment(
                                        file_get_contents($fileName),
                                        Zend_Mime::TYPE_OCTETSTREAM,
                                        Zend_Mime::DISPOSITION_ATTACHMENT,
                                        Zend_Mime::ENCODING_BASE64,
                                        $file['name'][$keyName]
                                    );
                            }
                        }
                    } else {
                        $validation = false;
                    }
                }else{
                    if ($file['size'] < 20000000) {
                        if($file['size'] > 0 && $file['error'] == 0){
                            $transactionalEmail
                                ->getMail()
                                ->createAttachment(
                                    file_get_contents($file['tmp_name']),
                                    Zend_Mime::TYPE_OCTETSTREAM,
                                    Zend_Mime::DISPOSITION_ATTACHMENT,
                                    Zend_Mime::ENCODING_BASE64,
                                    $file['name']
                                );
                        }
                    } else {
                        $validation = false;
                    }
                }
            }

	        $secret = Mage::getStoreConfig('magecomp/recaptcha_config/magecomp_recaptcha_validate', $storeId);
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
            
            if ($validation && $recaptcha['success']) {
                $transactionalEmail->sendTransactional($templateId, $sender, $email, $emailName, $vars, $storeId);
                Mage::getSingleton('core/session')->addSuccess($this->__('Message sent.'));
            } else if ($validation) {
	            $transactionalEmail->sendTransactional($templateId, $sender, $email, $emailName, $vars, $storeId);
	            Mage::getSingleton('core/session')->addSuccess($this->__('Message sent.'));
            } else {
	            $session = Mage::getSingleton('core/session');
	            $session->addError($this->__('Message not sent.'));
	            $this->_redirectReferer();
            }
        } catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError($this->__('Message not sent.'));
            Mage::logException($ex);
        }

        $this->_logEmail($params);

        if (!$validation && !$recaptcha['success']) {
            Mage::getSingleton('core/session')->addError($this->__('You have reached the maximum upload size limit of 20MB.'));
            $this->_redirectReferer();
        }elseif ($form->getSuccessPage()) {
            $url = Mage::getUrl($form->getSuccessPage(), array('_store' => Mage::app()->getStore()->getId()));
            $this->_redirectUrl($url);
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * Create HTML field.
     *
     */
    public function createFieldAction(){
        /** @var Blugento_FormsGenerator_Helper_Data $helper */
        $helper = Mage::helper('blugento_formsgenerator');
        $params = Mage::app()->getRequest()->getParams();

        if (trim($params['label']) == '' || $params['inputType'] == '0') {
            return false;
        }

        $textareaValue = ['select', 'radio'];

        if(in_array($params['inputType'], $textareaValue) && trim($params['values'] == '')){
            return false;
        }

        $fieldType     = isset($params['inputType']) ? $params['inputType'] : null;
        $fieldLabel    = isset($params['label']) ? ucwords(trim($params['label'])) : null;
        $placeholder   = isset($params['placeholder']) ? trim($params['placeholder']) : null;
        $required      = isset($params['required']) ? $params['required'] : null;
        $defaultValue  = isset($params['defaultValue']) ? trim($params['defaultValue']) : null;
        $values        = isset($params['values']) ? $params['values'] : null;
        $selectedValue = isset($params['selected']) ? trim($params['selected']) : null;
        $checked       = isset($params['checked']) ? $params['checked'] : null;
        $multiple      = isset($params['multiple']) ? $params['multiple'] : null;
        $comment       = isset($params['comment']) ? trim($params['comment']) : null;

        if ($comment != '') {
            $comment = $helper->createHtmlElement(
                'span',
                [
                    'start' => true,
                    'class' => 'comment',
                    'end' => true,
                    'text' => $comment
                ]
            );
        }

        $formCode = '';
        $fieldCode = $this->_getFieldCode($fieldType, $fieldLabel, $required, $placeholder, $values, $defaultValue, $selectedValue, $checked, $multiple, $comment);
        $formCode .= $fieldCode;

        echo $formCode;
    }

    /**
     * Gets the field code.
     *
     * @param string $type Field type.
     * @param string $label Field label.
     * @param string $required Required.
     * @param string $placeholder Field placeholder.
     * @param string $options Select/Radio values.
     * @param string $defaultValue Default value.
     * @param string $selectedValue Selected value by default.
     * @param string $checked Checked or not for checkboxes.
     * @param string $multiple Multiple file attachment.
     * @param string $comment Filed comment/hint.
     *
     * @return string
     */
    private function _getFieldCode($type, $label, $required, $placeholder, $options, $defaultValue, $selectedValue, $checked, $multiple, $comment){
        /** @var Blugento_FormsGenerator_Helper_Data $helper */
        $helper = Mage::helper('blugento_formsgenerator');

        $optionsArr = [];
        if($options != ''){
            $options = explode(',', $options);
            foreach($options as $option){
                $optionsArr[] = strtolower(trim($option));
            }
        }

        $name = str_replace(' ','-', strtolower($label));
        if($name == 'email'){
            $name = 'email-contact';
        }elseif($name == 'name'){
            $name = 'sender-name';
        }

        $id = $name.'-id';

        $fieldCode = '';
        $fieldCode .= '<!-- '.$label.' -->'.PHP_EOL;
        $fieldCode .= $helper->createHtmlElement(
            'li',
                [
                    'start' => true
                ]).PHP_EOL;

        $class = '';
        if($required){
            $class .= 'required-entry ';
            $label .= ' *';
        }

        /**
         * Create input type: text, password, hidden
         */
        $input = ['text', 'password', 'hidden'];

        if(in_array($type, $input)){

            if($type != 'hidden'){
                $fieldCode .= $helper::TAB1.$helper->createLabelElement($label, $id).PHP_EOL;
            }
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'div',
                [
                    'start' => true,
                    'class' => 'input-box'
                ]).PHP_EOL;
            $fieldCode .= $helper::TAB2.$helper->createHtmlElement(
                'input',
                [
                    'start'       => true,
                    'type'        => $type,
                    'id'          => $id,
                    'class'       => $class.'input-text',
                    'name'        => $name,
                    'default'     => $defaultValue,
                    'placeholder' => $placeholder,
                    'comment'     => $comment,
                ]).PHP_EOL;
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'div',
                [
                    'end'     => true
                ]).PHP_EOL;
        }

        /**
         * Create input type: submit, reset and add recaptcha if enabled
         */
	    if ($type == 'submit' || $type == 'reset') {
		
		    $fieldCode .= $helper::TAB1 . $helper->addGDPRConsent() . PHP_EOL;
	    	
		    $fieldCode .= $helper::TAB1 . $helper->createHtmlElement(
				    'input',
				    [
					    'start'   => true,
					    'type'    => 'checkbox',
					    'id'      => 'gdpr-conditions',
					    'class'   => 'checkbox required-entry',
					    'name'    => 'gdpr_consent',
				    ]) . PHP_EOL;

		    $gdprLabel = $this->getLayout()->createBlock('cms/block')->setBlockId('blugento-checkout-gdpr-acknowledgement')->toHtml();
		    
		    $fieldCode .= $helper::TAB1.$helper->createLabelElement($gdprLabel, 'gdpr-conditions', 'required', 'formsgenerator-gdpr', false) . PHP_EOL;
		
		    if (Mage::getStoreConfig('blugento_gdpruserdata/consent/read_more')) {
		    	$fieldCode .= '<a href="#popup-conditions" class="show-more">' . Mage::helper('blugento_formsgenerator')->__('[Show]') . '</a></label>';
			    $fieldCode .= '</div></div>' . PHP_EOL;
		    } else {
			    $fieldCode .= '</label></div></div>' . PHP_EOL;
            }

		    $fieldCode .= $helper::TAB1 . $helper->createButton($type, $label, 'g-recaptcha-forms button') . PHP_EOL;
	    }

        /**
         * Create input type: select
         */
        if($type == 'select'){
            $fieldCode .= $helper::TAB1.$helper->createLabelElement($label, $id).PHP_EOL;
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'div',
                [
                    'start' => true,
                    'class' => 'input-box'
                ]).PHP_EOL;
            $fieldCode .= $helper::TAB2.$helper->createHtmlElement(
                'select',
                [
                    'start'    => true,
                    'class'    => $class.'formsgenerator-select',
                    'id'       => $id,
                    'name'     => $name
                ]).PHP_EOL;

            if(in_array(strtolower($selectedValue), $optionsArr)){
                $optionsArr = array_combine($optionsArr, $optionsArr);
                $fieldCode .= $helper::TAB3.$helper->createHtmlElement(
                        'option',
                        [
                            'start'   => true,
                            'default' => ucwords($selectedValue),
                            'text'    => ucwords($selectedValue),
                            'end'     => true
                        ]
                        ).PHP_EOL;
                unset($optionsArr[strtolower($selectedValue)]);
            }else{
                $fieldCode .= $helper::TAB3.$helper->createHtmlElement(
                        'option',
                        [
                            'start'   => true,
                            'default' => 0,
                            'text'    => 'Select an Option',
                            'end'     => true
                        ]
                    ).PHP_EOL;
            }

            foreach($optionsArr as $option){
                $option = trim($option);
                $fieldCode .= $helper::TAB3.$helper->createHtmlElement(
                        'option',
                        [
                            'start'   => true,
                            'default' => ucwords($option),
                            'text'    => ucwords($option),
                            'end'     => true
                        ]
                    ).PHP_EOL;
            }
            $fieldCode .= $helper::TAB2.$helper->createHtmlElement(
                'select',
                [
                    'comment' => $comment,
                    'end'     => true
                ]).PHP_EOL;
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'div',
                [
                    'end' => true
                ]).PHP_EOL;
        }

        /**
         * Create input type: checkbox
         */
        if($type == 'checkbox'){
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'input',
                [
                    'start'   => true,
                    'type'    => $type,
                    'id'      => $id,
                    'class'   => 'checkbox-class',
                    'name'    => $name,
                    'default' => 1,
                    'checked' => $checked
                ]).PHP_EOL;
            $fieldCode .= $helper::TAB1.$helper->createLabelElement($label, $id).PHP_EOL;
        }

        /**
         * Create input type: textarea
         */
        if($type == 'textarea'){
            $fieldCode .= $helper::TAB1.$helper->createLabelElement($label, $id).PHP_EOL;
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'div',
                [
                    'start' => true,
                    'class' => 'input-box'
                ]).PHP_EOL;
            $fieldCode .= $helper::TAB2.$helper->createHtmlElement(
                $type,
                [
                    'start'       => true,
                    'id'          => $id,
                    'class'       => $class.'input-text',
                    'name'        => $name,
                    'placeholder' => $placeholder,
                    'text'        => $defaultValue,
                    'comment'     => $comment,
                    'end'         => true
                ]).PHP_EOL;

            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'div',
                [
                    'end' => true
                ]).PHP_EOL;
        }

        /**
         * Create input type: radio
         */
        if($type == 'radio'){
            $value = 1;

            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                'p',
                [
                    'start' => true,
                    'text'  => $label,
                    'end'   => true
                ]
                ).PHP_EOL;

            foreach($optionsArr as $option){
                $idx = $id.'-'.$value;
                $option = strtolower(trim($option));

                if($option == strtolower(trim($selectedValue))){
                    $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                        'input',
                        [
                            'start'   => true,
                            'type'    => $type,
                            'id'      => $idx,
                            'class'   => 'formsgenerator-radio',
                            'name'    => $name,
                            'default' => ucwords($selectedValue),
                            'checked' => true
                        ]).PHP_EOL;
                }else{
                    $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                        'input',
                        [
                            'start'   => true,
                            'type'    => $type,
                            'id'      => $idx,
                            'class'   => 'formsgenerator-radio',
                            'name'    => $name,
                            'default' => ucwords($option)
                        ]).PHP_EOL;
                }
                $fieldCode .= $helper::TAB1.$helper->createLabelElement(ucwords($option), $idx).PHP_EOL;
                $value++;
            }
        }

        /**
         * Create input type: file
         */
        if($type == 'file'){
            $fieldCode .= $helper::TAB1.$helper->createLabelElement($label, $id).PHP_EOL;
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                    'div',
                    [
                        'start' => true,
                        'class' => 'input-box'
                    ]).PHP_EOL;
            $fieldCode .= $helper::TAB2.$helper->createHtmlElement(
                    'input',
                    [
                        'start'    => true,
                        'type'     => $type,
                        'id'       => $id,
                        'name'     => $name,
                        'class'    => $class.'formsgenerator-file',
                        'multiple' => $multiple
                    ]).PHP_EOL;
            $fieldCode .= $helper::TAB1.$helper->createHtmlElement(
                    'div',
                    [
                        'end' => true
                    ]).PHP_EOL;
        }

        $fieldCode .= $helper->createHtmlElement(
            'li',
            [
                'end' => true
            ]).PHP_EOL;

        return $fieldCode;
    }
}