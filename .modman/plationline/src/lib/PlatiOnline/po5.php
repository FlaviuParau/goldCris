<?php

class PO5 {
	// private
	private $f_request = NULL;
	private $f_secure = NULL;
	private $aes_key = NULL;
	private $iv = NULL;
	private $iv_itsn = NULL;
	private $rsa_key_enc = NULL;
	private $rsa_key_dec = NULL;
	private $url = 'https://secure.plationline.ro/';
	private $url_sv_request_xml = 'https://secure2.plationline.ro/xml_validation/po.request.v5.xsd'; // any call
	private $url_sv_auth_xml = 'https://secure2.plationline.ro/xml_validation/f_message.auth.v5.xsd'; // auth
	private $url_sv_auth_url_xml = 'https://secure2.plationline.ro/xml_validation/auth.url.response.v5.xsd'; // auth url
	private $url_sv_auth_response_xml = 'https://secure2.plationline.ro/xml_validation/auth.response.v5.xsd'; // auth response
	private $url_sv_auth_response_soap_xml = 'https://secure2.plationline.ro/xml_validation/auth.soap.response.v5.xsd'; // auth response soap
	private $url_sv_itsn_xml = 'https://secure2.plationline.ro/xml_validation/itsn.v5.xsd'; // itsn
	private $url_sv_query_xml = 'https://secure2.plationline.ro/xml_validation/f_message.query.v5.xsd'; // query
	private $url_sv_itsn_response_xml = 'https://secure2.plationline.ro/xml_validation/query.response.v5.xsd'; // query response
	private $url_sv_settle_xml = 'https://secure2.plationline.ro/xml_validation/f_message.settle.v5.xsd'; // settle
	private $url_sv_settle_response_xml = 'https://secure2.plationline.ro/xml_validation/settle.response.v5.xsd'; // settle response
	private $url_sv_void_xml = 'https://secure2.plationline.ro/xml_validation/f_message.void.v5.xsd'; // void
	private $url_sv_void_response_xml = 'https://secure2.plationline.ro/xml_validation/void.response.v5.xsd'; // void response
	private $url_sv_refund_xml = 'https://secure2.plationline.ro/xml_validation/f_message.refund.v5.xsd'; // refund
	private $url_sv_refund_response_xml = 'https://secure2.plationline.ro/xml_validation/refund.response.v5.xsd'; // refund response

	// public
	public $f_login = NULL;
	public $version = NULL;
	public $test_mode = 0;

	function __construct() {
		$errors = array();
		$php_min_version = '5.1.3';
		if (version_compare(phpversion(), $php_min_version, '<')) {
			$errors[] = '<b>PHP version ' . $php_min_version . '</b> is needed to use this <b>PlatiOnline</b> kit.<br />Current PHP version: <b>' . phpversion() . '</b>';
		}

		if (!extension_loaded('soap')) {
			$errors[] = '<b>SOAP extension active</b> is needed to use this <b>PlatiOnline</b> kit. The SOAP extension is currently <b>DISABLED!</b>';
		}

		if (isset($errors) && !empty($errors)) {
			//daca avem erori
			foreach ($errors as $error) {
				echo $error . "<hr />";
			}

			die('Please fix the above mentioned errors to use this PlatiOnline kit');
		}
	}

	//////////////////////////////////////////////////////////////
	// 						PUBLIC METHODS						//
	//////////////////////////////////////////////////////////////

	// setez versiunea de kit
	public function PO5() {
		$this->version = "PO 5.0.0 XML";
	}

	// setez cheia RSA pentru criptare
	public function setRSAKeyEncrypt($rsa_key_enc) {
		$this->rsa_key_enc = $rsa_key_enc;
	}

	// setez cheia RSA pentru decriptare
	public function setRSAKeyDecrypt($rsa_key_dec) {
		$this->rsa_key_dec = $rsa_key_dec;
	}

	// setez initial vector
	public function setIV($iv) {
		$this->iv = $iv;
	}

	// setez initial vector ITSN
	public function setIVITSN($iv) {
		$this->iv_itsn = $iv;
	}

	public function auth($f_request, $f_action = 2) {
		// ne asiguram ca stergem tot ce e in campul f_request
		$this->f_request = NULL;
		$f_request['f_action'] = $f_action;
		$request = $this->setFRequest($f_request, 'po_auth_request', $this->url_sv_auth_xml);

		$opts = array(
			'http' => array(
				'user_agent' => 'PlatiOnline-SOAP',
			),
		);
		$context = stream_context_create($opts);
		$client = new SoapClient(null, array(
			'location' => $this->url,
			'uri' => 'auth-only',
			'stream_context' => $context,
		));

		$response = $client->__doRequest($request, $this->url, 'auth-only', 1);

		if (!isset($response) || empty($response)) {
			die('<b>ERROR</b>: Nu am putut comunica cu serverul PO pentru operatiunea de autorizare!');
		}

		$this->validate_xml($response, $this->url_sv_auth_url_xml);

		require_once 'xml2Array.php';
		$objXML = new xml2Array();
		$arrOutput = $objXML->parse($response);

		$parsedResponse = $arrOutput;

		if ($parsedResponse['PO_AUTH_URL_RESPONSE']['PO_ERROR_CODE'] == 1) {
			die($parsedResponse['PO_AUTH_URL_RESPONSE']['PO_ERROR_REASON']);
		} else {
			$redirect_url = $parsedResponse['PO_AUTH_URL_RESPONSE']['PO_REDIRECT_URL'];
			if (isset($redirect_url) && !empty($redirect_url)) {
				header('Location: ' . $redirect_url);
				die();
			} else {
				die('<b>ERROR</b>: Serverul nu a intors URL-ul pentru a finaliza tranzactia!');
			}
		}
	}

	// obtin raspunsul pentru cererea de autorizare
	public function auth_response($f_relay_message, $f_crypt_message) {
		return $this->decrypt_response($f_relay_message, $f_crypt_message, $this->url_sv_auth_response_xml);
	}

	// obtin datele din notificarea ITSN
	public function itsn($f_relay_message, $f_crypt_message) {
		return $this->decrypt_response($f_relay_message, $f_crypt_message, $this->url_sv_itsn_xml);
	}

	// interogare
	public function query($f_request, $f_action = 0) {
		// ne asiguram ca stergem tot ce e in campul f_request
		$this->f_request = NULL;
		$f_request['f_action'] = $f_action;
		$request = $this->setFRequest($f_request, 'po_query', $this->url_sv_query_xml);

		$opts = array(
			'http' => array(
				'user_agent' => 'PlatiOnline-SOAP',
			),
		);
		$context = stream_context_create($opts);
		$client = new SoapClient(null, array(
			'location' => $this->url,
			'uri' => 'query',
			'stream_context' => $context,
		));
		$response = $client->__doRequest($request, $this->url, 'query', 1);

		if (!isset($response) || empty($response)) {
			die('<b>ERROR</b>: Nu am putut comunica cu serverul PO pentru operatiunea de interogare!');
		}

		// validez xml-ul primit ca raspuns de la PO
		$this->validate_xml($response, $this->url_sv_itsn_response_xml);

		require_once 'xml2Array.php';
		$objXML = new xml2Array();
		$arrOutput = $objXML->parse($response);

		return $arrOutput;
	}

	public function settle($f_request, $f_action = 3) {
		// ne asiguram ca stergem tot ce e in campul f_request
		$this->f_request = NULL;
		$f_request['f_action'] = $f_action;
		$request = $this->setFRequest($f_request, 'po_settle', $this->url_sv_settle_xml);

		$opts = array(
			'http' => array(
				'user_agent' => 'PlatiOnline-SOAP',
			),
		);
		$context = stream_context_create($opts);
		$client = new SoapClient(null, array(
			'location' => $this->url,
			'uri' => 'settle',
			'stream_context' => $context,
		));
		$response = $client->__doRequest($request, $this->url, 'settle', 1);

		if (!isset($response) || empty($response)) {
			die('<b>ERROR</b>: Nu am putut comunica cu serverul PO pentru operatiunea de incasare!');
		}

		// validez xml-ul primit ca raspuns de la PO
		$this->validate_xml($response, $this->url_sv_settle_response_xml);

		require_once 'xml2Array.php';
		$objXML = new xml2Array();
		$arrOutput = $objXML->parse($response);

		return $arrOutput;
	}

	public function void($f_request, $f_action = 7) {
		// ne asiguram ca stergem tot ce e in campul f_request
		$this->f_request = NULL;
		$f_request['f_action'] = $f_action;
		$request = $this->setFRequest($f_request, 'po_void', $this->url_sv_void_xml);

		$opts = array(
			'http' => array(
				'user_agent' => 'PlatiOnline-SOAP',
			),
		);
		$context = stream_context_create($opts);
		$client = new SoapClient(null, array(
			'location' => $this->url,
			'uri' => 'void',
			'stream_context' => $context,
		));
		$response = $client->__doRequest($request, $this->url, 'void', 1);

		if (!isset($response) || empty($response)) {
			die('<b>ERROR</b>: Nu am putut comunica cu serverul PO pentru operatiunea de anulare!');
		}

		// validez xml-ul primit ca raspuns de la PO
		$this->validate_xml($response, $this->url_sv_void_response_xml);

		require_once 'xml2Array.php';
		$objXML = new xml2Array();
		$arrOutput = $objXML->parse($response);

		return $arrOutput;
	}

	public function refund($f_request, $f_action = 1) {
		// ne asiguram ca stergem tot ce e in campul f_request
		$this->f_request = NULL;
		$f_request['f_action'] = $f_action;
		$request = $this->setFRequest($f_request, 'po_refund', $this->url_sv_refund_xml);

		$opts = array(
			'http' => array(
				'user_agent' => 'PlatiOnline-SOAP',
			),
		);
		$context = stream_context_create($opts);
		$client = new SoapClient(null, array(
			'location' => $this->url,
			'uri' => 'refund',
			'stream_context' => $context,
		));
		$response = $client->__doRequest($request, $this->url, 'refund', 1);

		if (!isset($response) || empty($response)) {
			die('<b>ERROR</b>: Nu am putut comunica cu serverul PO pentru operatiunea de creditare!');
		}

		// validez xml-ul primit ca raspuns de la PO
		$this->validate_xml($response, $this->url_sv_refund_response_xml);

		require_once 'xml2Array.php';
		$objXML = new xml2Array();
		$arrOutput = $objXML->parse($response);

		return $arrOutput;
	}

	public function parse_soap_response($soap) {
		$this->validate_xml($soap, $this->url_sv_auth_response_soap_xml);

		require_once 'xml2Array.php';
		$objXML = new xml2Array();
		$arrOutput = $objXML->parse($soap);

		return $arrOutput;
	}

	//////////////////////////////////////////////////////////////
	// 					END PUBLIC METHODS						//
	//////////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////////
	// 					  PRIVATE METHODS						//
	//////////////////////////////////////////////////////////////

	// criptez f_request cu AES
	private function AESEnc() {
		require_once 'Crypt/AES_Encryption.php';
		require_once 'Crypt/padCrypt.php';

		$this->aes_key = md5(uniqid());
		$aes = new AES_Encryption($this->aes_key, $this->iv, "PKCS7", "cbc");
		$this->f_request = bin2hex(base64_encode($aes->encrypt($this->f_request)));
	}

	// criptez cheia AES cu RSA
	private function RSAEnc() {
		require_once 'Crypt/RSA.php';
		$rsa = new Crypt_RSA();
		$rsa->loadKey($this->rsa_key_enc);
		$rsa->setPublicKey();
		$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
		$this->f_secure = base64_encode($rsa->encrypt($this->aes_key));
	}

	// setez f_request, criptez f_request cu AES si cheia AES cu RSA
	private function setFRequest($f_request, $type, $validation_url) {
		// aici construiesc XML din array
		$xml = new SimpleXMLElement('<' . $type . '/>');

		// test mode
		if ($type == 'po_auth_request') {
			if ($this->test_mode == 0) {
				$f_request['f_test_request'] = 0;
			} else {
				$f_request['f_test_request'] = 1;
			}

			$f_request['f_sequence'] = rand(1, 1000);
		}

		$f_request['f_timestamp'] = date('Y-m-d\TH:i:sP');
		// set f_login
		$f_request['f_login'] = $this->f_login;

		// sortez parametrii alfabetic
		ksort($f_request);

		$this->array2xml($f_request, $xml);
		$this->f_request = $xml->asXML();

		// validez XML conform schemei (parametrul 2)
		$this->validate_xml($this->f_request, $validation_url);

		$this->AESEnc();
		$this->RSAEnc();

		$request = array();

		$request['f_login'] = $this->f_login;
		$request['f_message'] = $this->f_request;
		$request['f_crypt_message '] = $this->f_secure;

		$xml_auth_soap = new SimpleXMLElement('<po_request/>');
		$this->array2xml($request, $xml_auth_soap);
		$xml_auth_soap = $xml_auth_soap->asXML();
		$this->validate_xml($xml_auth_soap, $this->url_sv_request_xml);
		return $xml_auth_soap;
	}

	// function definition to convert array to xml
	private function array2xml($arr, &$xml_arr) {
		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				if (!is_numeric($key)) {
					if (strpos($key, 'coupon') !== false) {
						$subnode = $xml_arr->addChild("coupon");
					} else {
						$subnode = $xml_arr->addChild("$key");
					}

					$this->array2xml($value, $subnode);
				} else {
					$subnode = $xml_arr->addChild("item");
					$this->array2xml($value, $subnode);
				}
			} else {
				$xml_arr->addChild("$key", htmlspecialchars("$value"));
			}
		}
	}

	private function validate_xml($poxml, $url) {
		libxml_use_internal_errors(true);
		$xml = new DOMDocument();
		$xml->loadXML($poxml);
		if (!$xml->schemaValidate($url)) {
			$errors = libxml_get_errors();
			foreach ($errors as $error) {
				echo $this->libxml_display_error($error);
			}
			libxml_clear_errors();
			die('<br />INVALID XML');
		}
	}

	private function decrypt_response($f_relay_message, $f_crypt_message, $validation_url) {
		if (!isset($f_relay_message) || empty($f_relay_message)) {
			die('Decriptare raspuns - nu se primeste [criptul AES]');
		}

		if (!isset($f_crypt_message) || empty($f_crypt_message)) {
			die('Decriptare raspuns - nu se primeste [criptul RSA]');
		}

		require_once 'Crypt/RSA.php';
		$rsa = new Crypt_RSA();
		$rsa->loadKey($this->rsa_key_dec);
		$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
		$aes_key = $rsa->decrypt(base64_decode($f_crypt_message));
		if (!isset($aes_key) || empty($aes_key)) {
			die('Nu am putut decripta cheia AES din RSA');
		}

		require_once 'Crypt/AES_Encryption.php';
		require_once 'Crypt/padCrypt.php';
		$aes = new AES_Encryption($aes_key, $this->iv_itsn, "PKCS7", "cbc");
		$response = $aes->decrypt(base64_decode($this->hex2str($f_relay_message)));
		if (!isset($response) || empty($response)) {
			die('Nu am putut decripta mesajul din criptul AES');
		}

		$this->validate_xml($response, $validation_url);
		require_once 'xml2Array.php';
		$objXML = new xml2Array();
		$arrOutput = $objXML->parse($response);
		return $arrOutput;
	}

	private function libxml_display_error($error) {
		$return = "<br />\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "<b>Warning $error->code</b>: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "<b>Error $error->code</b>: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "<b>Fatal Error $error->code</b>: ";
				break;
		}
		$return .= trim($error->message);
		$return .= "\n";
		return $return;
	}

	private function encrypt_ITSN($input) {
		$aes_key = md5(uniqid());
		require_once 'Crypt/AES_Encryption.php';
		require_once 'Crypt/padCrypt.php';
		$aes = new AES_Encryption($aes_key, $this->iv_itsn, "PKCS7", "cbc");
		$local_rez = ($aes->encrypt($input));
		return base64_encode($local_rez);
	}

	private function hex2str($hex) {
		$str = '';
		for ($i = 0; $i < strlen($hex); $i += 2) {
			$str .= chr(hexdec(substr($hex, $i, 2)));
		}

		return $str;
	}

	//////////////////////////////////////////////////////////////
	// 					  END PRIVATE METHODS					//
	//////////////////////////////////////////////////////////////
}
?>