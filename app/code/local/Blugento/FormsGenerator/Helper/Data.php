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

class Blugento_FormsGenerator_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'blugento_formsgenerator/general/enable';
    const TAB1 = "\t";
    const TAB2 = "\t\t";
    const TAB3 = "\t\t\t";

    const RECAPTCHA_ENABLE = 'formsgenerator/general/enabled_recaptcha';
	const SITEKEY = 'formsgenerator/general/recaptcha_validate';
	const SITESECRETKEY = 'formsgenerator/general/recaptcha_secret';
	const RECAPTCHA_THEME = 'formsgenerator/general/recaptcha_theme';
	
	/**
     * Config array
     *
     * array(
     *      'start'       => bool, beggining tag
     *      'end'         => bool, ending tag
     *      'for'         => string, label for attribute
     *      'type'        => string, input type: 'text', 'password'...
     *      'name'        => string, form field name
     *      'class'       => string, tag class
     *      'id'          => string, tag id
     *      'placeholder' => string, form field placeholder
     *      'default'     => string, from field default value
     *      'required'    => bool, field required
     *      'checked'     => bool, radio/checkbox default checked
     *      'text'        => string, text between tags e.g. textarea, p, label..
     *      'multiple'    => bool, allow multiple file uploads
     *      'comment'     => string, add a comment/note for the field
     * )
     * @var array
     */
    protected $_config = array();

    /**
     * Check if module is enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Check if recaptcha is enabled
     *
     * @return int
     */
    public function isRecaptchaEnabled()
    {
        return (int) Mage::getStoreConfig(self::RECAPTCHA_ENABLE);
    }

    /**
     * Get recaptcha key
     *
     * @param $storeId
     * @return mixed
     * @throws Mage_Core_Model_Store_Exception
     */
	protected function getKey($storeId)
	{
		return Mage::getStoreConfig('magecomp/recaptcha_config/magecomp_recaptcha_validatesite', $storeId);
	}

	/**
	 * Get recaptcha secret key
	 *
	 * @return mixed
	 */
	public function getSecretKey()
	{
		return Mage::getStoreConfig(self::SITESECRETKEY);
	}
	
	/**
	 * Get recaptcha theme
	 *
	 * @return string
	 */
	protected function getTheme()
	{
		if (Mage::getStoreConfig(self::RECAPTCHA_THEME) == 0) {
			return 'light';
		} else {
			return 'dark';
		}
	}

    /**
     * Get allowed input types.
     *
     * @return array
     */
    public function getAllowedInputTypes(){
        $options = Mage::getStoreConfig('formsgenerator/general/input_types');
        $options = explode(',', $options);

        $options = array_combine($options, $options);
        array_unshift($options, $this->__('Select an Option...'));

        return $options;
    }

    /**
     * Create HTML form element tag.
     *
     * @param bool $first Beginning tag.
     * @param string $method Form method.
     * @param string $action Form action.
     * @param string $id Form id.
     * @param $file $id File or not.
     *
     * @return string
     */
    public function createFormElement($first = false, $method = '', $action = '', $id = '', $file = false){
        $code = '';

        if($first){
            $code .= '<form ';
            if($method){
                $code .= 'method="'.$method.'" ';
            }
            if($action){
                $code .= 'action="'.$action.'" ';
            }
            if($id){
                $code .= 'id="'.$id.'" ';
            }
            if($file){
                $code .= 'enctype="multipart/form-data" ';
            }
            $code .= '>';
        }else{
            $code .= '</form>';
        }

        return $code;
    }

    /**
     * Create HTML element.
     *
     * @param string $tagName Tag name.
     * @param array $config Tag configuration.
     *
     * @return string
     */
    public function createHtmlElement($tagName, $config = array()){
        $element = '';
        $this->_config = $config;

        if(isset($config['start']) && $config['start']){
            $element .= '<'.$tagName.' ';

            if($tagName == 'label' && isset($config['for']) && $config['for']){
                $element .= 'for="'.$config['for'].'" ';
            }
            if(isset($config['type']) && $config['type']){
                $element .= 'type="'.$config['type'].'" ';
            }
            if(isset($config['multiple']) && $config['multiple']){
                if(isset($config['name']) && $config['name']){
                    $element .= 'name="'.$config['name'].'[]" ';
                }
            }else {
                if (isset($config['name']) && $config['name']) {
                    $element .= 'name="' . $config['name'] . '" ';
                }
            }
            if(isset($config['class']) && $config['class']){
                $element .= 'class="'.$config['class'].'" ';
            }
            if(isset($config['id']) && $config['id']){
                $element .= 'id="'.$config['id'].'" ';
            }
            if(isset($config['placeholder']) && $config['placeholder']){
                $element .= 'placeholder="'.$config['placeholder'].'" ';
            }
            if(isset($config['default']) && $config['default'] !== false){
                if($config['default'] === 0){
                    $element .= 'value="" ';
                }elseif($config['default']) {
                    $element .= 'value="' . $config['default'] . '" ';
                }
            }
            if(isset($config['required']) && $config['required']){
                $element .= 'required ';
            }
            if(isset($config['checked']) && $config['checked']){
                $element .= 'checked ';
            }
            if(isset($config['multiple']) && $config['multiple']){
                $element .= 'multiple ';
            }
            if($tagName == 'textarea'){
                $element .= 'cols="5" rows="10" ';
            }
            $element .= '>';
        }

        if(isset($config['text']) && $config['text']){
            $element .= $config['text'];
        }

        if(isset($config['end']) && $config['end']){
            $element .= '</'.$tagName.'>';
        }

        if(isset($config['comment']) && $config['comment']){
            $element .= $config['comment'];
        }

        return $element;
    }
	
	/**
	 * Create HTML label element.
	 *
	 * @param string $label Label text.
	 * @param string $for Label for.
	 * @param string $class Label class.
	 * @param string $id Label id.
	 * @param bool $end
	 * @return string
	 */
    public function createLabelElement($label, $for, $class = '', $id = '', $end = true){
        $fieldCode = $this->createHtmlElement(
            'label',
            [
                'start' => true,
                'for'   => $for,
                'text'  => $label,
                'end'   => $end,
                'class' => $class,
                'id'    => $id
            ]
        );
        return $fieldCode;
    }

    /**
     * Create HTML button element.
     *
     * @param string $type Button type.
     * @param string $text Button value.
     * @param string $class Button class.
     * @param string $id Button id.
     *
     * @return string
     */
    public function createButton($type, $text, $class = null, $id = null){

        $button = $this->createHtmlElement(
                'div',
                [
                    'start' => 'true',
                    'class' => 'button-set'
                ]).PHP_EOL;
        $button .= self::TAB2.$this->createHtmlElement(
                'button',
                [
                    'start' => true,
                    'type'  => $type,
                    'class' => $class,
                    'id'    => 'g-recaptcha-forms',
                    'text'  => $this->createHtmlElement(
                        'span',
                        [
                            'start' => true,
                            'text'  => $text,
                            'end'   => true
                        ]),
                    'end' => true
                ]).PHP_EOL;

        $button .= self::TAB1.$this->createHtmlElement(
                'div',
                [
                    'end' => true
                ]);

        return $button;
    }

	/**
	 * Add GDPR consent for current form
	 *
	 * @return string
	 */
	public function addGDPRConsent()
	{
		$gdprText = $this->createHtmlElement(
			'div',
			[
				'start'        => true,
				'class'        => 'gdpr-conditions',
				'end'          => false
			]
		);
		
		$gdprText .= $this->createHtmlElement(
			'div',
			[
				'start' => true,
				'class' => Mage::getStoreConfig('blugento_gdpruserdata/consent/read_more') ? 'content-popup-text show-less' : 'content-popup-text',
				'end'   => false
			]
		);
		
		return $gdprText;
	}

    /**
     * Get additional input to create the form.
     *
     * @param string $code Code that contains all the form fields.
     * @param int $formId Form ID.
     * @param int $storeId Store ID.
     *
     * @return string
     */
    public function getAdditionalInput($code, $formId, $storeId)
    {
        $formHTMLId = 'formsgenerator-form-' . $formId;
        $formAction = $this->isRecaptchaEnabled() ? '' : $this->getFormAction($storeId, 'formsgenerator/index/sendemail');
        $sitekey = Mage::getStoreConfig('magecomp/recaptcha_config/magecomp_recaptcha_validate', $storeId);
        $formActionRecaptcha = $this->getFormAction($storeId, 'formsgenerator/index/sendemail');
        $startCode = $this->createFormElement(true, 'post', $formAction, $formHTMLId, true) . PHP_EOL;
        $startCode .= $this->createHtmlElement(
            'ul',
            [
                'start' => true,
                'class' => 'form-list'
            ]).PHP_EOL;

        $startCode .= $this->createHtmlElement(
            'input',
                [
                    'start'       => true,
                    'type'        => 'hidden',
                    'name'        => 'form_id',
                    'default'     => $formId,
                    'end'         => true
                ]).PHP_EOL;

        if ($this->isRecaptchaEnabled()) {
            $startCode .= $this->createHtmlElement(
                'input',
                    [
                        'start'       => true,
                        'type'        => 'text',
                        'name'        => 'form_id_spam',
                        'id'          => "no-spam-$formId",
                        'class'       => 'required-entry no-spam no-display',
                        'default'     => '',
                        'end'         => true
                    ]).PHP_EOL;
        }

        $endCode = $this->createHtmlElement(
                'ul',
                [
                    'end' => true,
                ]).PHP_EOL;

        $endCode .= $this->createHtmlElement(
                'form',
                [
                    'end' => true,
                ]).PHP_EOL;

        $endCode .= $this->createHtmlElement(
            'script',
            [
                'start' => true,
                'text'  => '// <![CDATA[
                        var theForm = new VarienForm("' . $formHTMLId . '", false);
                    // ]]',
                'end'   => true
            ]
        );

	    $endCode .= $this->createHtmlElement(
		    'script',
		    [
			    'start' => true,
			    'text'  => '// <![CDATA[
				            (function($) {
							    $(".content-popup-text .show-more").on("click", function() {
								    $(this).parent().parent().removeClass("show-less");
								    $(this).remove();
							    });
						    })(jQuery);
	                     // ]]',
			    'end'   => true
		    ]
	    );

        if ($this->isRecaptchaEnabled()) {
        	$endCode .= $this->createHtmlElement(
		        'script',
		        [
			        'start' => true,
			        'text'  => '// <![CDATA[
                        jQuery("#g-recaptcha-forms' . $formId . '").attr("data-badge","bottomleft");
                        // Prevent form to submit if hidden input has value
                        jQuery("#' . $formHTMLId . '").submit(function(e) {
                            if (jQuery("#no-spam-' . $formId . '").val() !== "") {
                                e.preventDefault();

                                window.history.back();
                            }
                        });

                        function onSubmitForms' . $formId . '(token) {
                            setTimeout(function(){
                                jQuery(".g-recaptcha-forms' . $formId . '").attr("data-token", token);

                                if (jQuery("#no-spam-' . $formId . '").val() === "") {
                                    return new Promise(function (resolve, reject) {
                                        var dataForm = new VarienForm("' . $formHTMLId . '");
                                        var tokenHash = jQuery(".g-recaptcha-forms' . $formId . '").attr("data-token");

                                        if (tokenHash && tokenHash.length > 100) {
                                            jQuery("#no-spam-' . $formId . '").removeClass("required-entry");

                                            if (dataForm.validator && dataForm.validator.validate()){
                                                jQuery("#' . $formHTMLId . '").attr("action", "' . $formActionRecaptcha .'");
                                                document.getElementById("' . $formHTMLId . '").submit();
                                            }
                                        }
                                    });
                                }
                            }, 300);
                        }

                        // Check if captcha container have loaded class, if not than captcha will be loaded
                        function loadCaptchaForms' . $formId . '() {
                            // Fix for google recaptcha from 18 June
                            if ("NodeList" in window) {
                                if (!NodeList.prototype.each && NodeList.prototype.forEach) {
                                    NodeList.prototype.each = NodeList.prototype.forEach;
                                }
                            }

                            if (!jQuery(".captcha_container_forms' . $formId . '").length) {
                                jQuery("#g-recaptcha-forms' . $formId . '").addClass("captcha_container_forms' . $formId . '");
                                var captchaContainer = null;
                                var loadCaptchaFormsFunction' . $formId .' = function() {
                                    captchaContainer = grecaptcha.render("g-recaptcha-forms' . $formId . '", {
                                        "sitekey": "' . $this->getKey($storeId) .'",
                                        "callback": onSubmitForms' . $formId . '
                                    });
                                };

                                loadCaptchaFormsFunction' . $formId .'();
                            }
                        };

                        // '. $sitekey .'

                        // Once user insert a value, loadCaptchaContent will be called
                        jQuery("#' . $formHTMLId . ' input, #' . $formHTMLId . ' textarea, #' . $formHTMLId . ' select").each(function(e) {
                            jQuery(this).on("change keyup blur input", function(e) {
                                loadCaptchaForms' . $formId . '();
                            });
                        });

                        jQuery(".g-recaptcha-forms' . $formId . '").on("click", function(e) {
                            onSubmitForms' . $formId . '();
                        });
                     // ]]',
			        'end'   => true
		        ]
            );

            $endCode .= $this->createHtmlElement(
        	    '',
	            [
	            	'start' => false,
		            'text'  => '<script src="https://www.google.com/recaptcha/api.js?onload=loadCaptchaFormsFunction' . $formId . '&render=explicit" async defer></script>',
		            'end'   => false
	            ]
	        );
        }

        $formCode = $startCode . $code . $endCode;

        return $formCode;
    }

    /**
     * Create form shortcode that is using to display the form in a CMS Page.
     *
     * @param int $formId Form ID.
     *
     * @return string
     */
    public function createShortcode($formId)
    {
        $shortCode = '{{customForm id='.$formId.'}}';
        return $shortCode;
    }

    /**
     * Get form action.
     *
     * @param string $path
     * @param string $storeId
     *
     * @return string
     */
    private function getFormAction($storeId, $path = '')
    {
        $url = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $url = str_replace('index.php/', '', $url);
        return $url . $path;
    }
}