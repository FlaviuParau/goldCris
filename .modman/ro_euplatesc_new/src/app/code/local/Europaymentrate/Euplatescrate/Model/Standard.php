<?php


class europaymentrate_euplatescrate_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
 	const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
	const STANDARD_TYPE_IPN = 'IPN';
	const PAYMENT_TYPE_SALE = 'SALE';

	protected $_code = 'euplatescrate';
  protected $_isGateway               = true;
  protected $_canAuthorize            = true;
  protected $_canCapture              = true;
  protected $_canCapturePartial       = false;
  protected $_canRefund               = false;
  protected $_canVoid                 = true;
  protected $_canUseInternal          = false;
  protected $_canUseCheckout          = true;
  protected $_canUseForMultishipping  = true;
  protected $_canSaveCc = false;

	protected $_stObj;

	protected $_formBlockType = 'euplatescrate/form';
	protected $_allowCurrencyCode = array('RON', 'EUR', 'USD');
	protected $_allowLanguageCode = array('RO','EN', 'DE', 'FR', 'IT', 'ES');


    public function getSession()
    {
        return Mage::getSingleton('euplatescrate/session');
    }

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('euplatescrate/form', $name)
            ->setMethod('euplatescrate_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('euplatescrate/form.phtml');

        return $block;
    }


    public function geteuplatescrateUrl()
    {
        return Mage::getStoreConfig('payment/euplatescrate/cgi_url');
    }

    public function getOrderPlaceRedirectUrl()
    {
		return Mage::getUrl('euplatescrate/index/redirect', array('_secure' => true));
    }


    public function isInitializeNeeded()
    {
        return true;
    }

    public function initialize($paymentAction, $stateObject)
    {
      $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
      $stateObject->setState($state);	
	    $stateObject->setStatus('pending_euplatescrate');
      $stateObject->setIsNotified(false);
    }

	private function getRateTable(){
		$table["apb"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate1'));
		$table["bcr"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate2'));
		$table["btrl"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate3'));
		$table["brdf"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate4'));
		$table["rzb"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate5'));
		return $table;
	}
 
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('euplatescrate')->__('Selected currency code (%s) is not compatible with euplatescrate',$currency_code));
        }
		//Mage::throwException(Mage::helper('euplatescrate')->__('Selecteaza numarul de rate!'));
		
		$postData = Mage::app()->getRequest()->getPost();
        if (isset($postData['epr_ptype']) and $postData['epr_ptype']=="rate") {
			if (isset($postData['epr_bank'])) {
				$rate_config = Mage::getStoreConfig('payment/euplatescrate/rateactive');
				$rate = explode(",", $rate_config);
				if(array_search($postData['epr_bank'],$rate)!== false){
					$nr_rate=$this->getRateTable();
					if( isset($postData['epr_nrrate']) && array_search($postData['epr_nrrate'],$nr_rate[$postData['epr_bank']])!== false){
						 Mage::getSingleton('core/session')->setNumInstallments($postData['epr_bank']."-".$postData['epr_nrrate']);
					}else{
						Mage::throwException(Mage::helper('euplatescrate')->__('Selectie invalida numar rate!'));//numarul de rate nu este activ sau existent
					}
				}else{
					Mage::throwException(Mage::helper('euplatescrate')->__('Selectie invalida banca!'));//banca aleasa nu este activa sau existenta
				}
			}else if(isset($postData['epr_rate'])){
				$rate_sel = explode("-", $postData['epr_rate']);
				if(count($rate_sel)==2){
					$rate_config = Mage::getStoreConfig('payment/euplatescrate/rateactive');
					$rate = explode(",", $rate_config);
					if(array_search($rate_sel[0],$rate)!== false){
						$nr_rate=$this->getRateTable();
						if(array_search($rate_sel[1],$nr_rate[$rate_sel[0]])!== false){
							 Mage::getSingleton('core/session')->setNumInstallments($postData['epr_rate']);
						}else{
							Mage::throwException(Mage::helper('euplatescrate')->__('Selectie invalida numar rate!'));//numarul de rate nu este activ sau existent
						}
					}else{
						Mage::throwException(Mage::helper('euplatescrate')->__('Selectie invalida banca!'));//banca aleasa nu este activa sau existenta
					}
				}else{
					Mage::throwException(Mage::helper('euplatescrate')->__('Eroare valoare rata!'));// structura invalida tip "banca-rate"
				}
			}else{
				Mage::throwException(Mage::helper('euplatescrate')->__('Selecteaza numarul de rate!'));// a ales plata in rate dar nu a ales banca si numarul
			}
		
		}else{
			Mage::getSingleton('core/session')->setNumInstallments("1");
		}
				
        return $this;
    }

    public function getCheckoutFormFields()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $salesEntity = $order;
        
              
        if ($salesEntity->getIsVirtual()) {
			$a = $salesEntity->getBillingAddress();
			$b = $a;
		} else {
			$a = $salesEntity->getBillingAddress();
			$b = $salesEntity->getShippingAddress();
		}
		
        $order->setStatus(Mage::getStoreConfig('payment/euplatescrate/order_status')); 
		$order->save();
	    $order->sendNewOrderEmail();

        $currency_code = $salesEntity->getBaseCurrencyCode();
		$amount = number_format($salesEntity->getBaseGrandTotal(),2,'.','');
		$merch=Mage::getStoreConfig('payment/euplatescrate/partner');
		$skey=Mage::getStoreConfig('payment/euplatescrate/trans_key');
		
		$dataAll = array(
			'amount'      => $amount,
			'curr'        => $currency_code,
			'invoice_id'  => $orderIncrementId,
			'order_desc'  => "Comanda Online",
			'merch_id'    => $merch,
			'timestamp'   => gmdate("YmdHis"),
			'nonce'       => md5(microtime() . mt_rand()),
		); 
		$dataAll['fp_hash'] = strtoupper($this->euplatescrate_mac($dataAll,$skey));
		
		$dataAll['ExtraData[rate]'] =  Mage::getSingleton('core/session')->getNumInstallments();
		
		$dataBill = array(
					'fname'	   => htmlspecialchars($a->getFirstname()), 
					'lname'	   => htmlspecialchars($a->getLastname()),
					'state'	   =>  htmlspecialchars($a->getRegionCode()),
					'zip_code' => '',
					'country'  => htmlspecialchars($a->getCountry()),
					'company'  => '',
					'city'	   => htmlspecialchars($a->getCity()),
					'add'	   => htmlspecialchars($a->getStreet(1).$a->getStreet(2)),
					'email'	   => htmlspecialchars($salesEntity->getCustomerEmail()),
					'phone'	   => htmlspecialchars($a->getTelephone()),
					'fax'	   => '',
		);
		$dataShip = array(
					'sfname'	   => htmlspecialchars($b->getFirstname()), 
					'slname'	   => htmlspecialchars($b->getLastname()),
					'sstate'	   =>  htmlspecialchars($b->getRegionCode()),
					'szip_code'	   => '',
					'scountry'     => htmlspecialchars($b->getCountry()),
					'scompany'     => '',
					'scity'	       => htmlspecialchars($b->getCity()),
					'sadd'	       => htmlspecialchars($b->getStreet(1).$b->getStreet(2)),
					'semail'	   => htmlspecialchars($salesEntity->getCustomerEmail()),
					'sphone'	   => htmlspecialchars($b->getTelephone()),
					'sfax'	       => '',
		);
		

		$rArr = array_merge($dataAll,$dataBill,$dataShip);
		return $rArr;
    }


	private function hmacsha1($key,$data) {
		$blocksize = 64;
		$hashfunc  = 'md5';
		if(strlen($key) > $blocksize)
			$key = pack('H*', $hashfunc($key));
		$key  = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
		return bin2hex($hmac);
	}
	
	private function euplatescrate_mac($data, $key = NULL){
		$str = NULL;
		foreach($data as $d){
			if($d === NULL || strlen($d) == 0)
				$str .= '-';
			else
				$str .= strlen($d) . $d;
		}
		$key = pack('H*', $key);
		return $this->hmacsha1($key, $str);
	}


    public function ipnPostSubmit(){
		
		if(isset($_POST['message']) and strpos(strtolower($_POST['message']),"pending")!==false){ /*to filter sms pending message*/
			echo "no pending";
			die();
		}
		
        $key =  Mage::getStoreConfig('payment/euplatescrate/trans_key');
        $secstatus =  Mage::getStoreConfig('payment/euplatescrate/test');
		if($secstatus=="0"){
			if(isset($_POST['invoice_id'])) {
				$zcrsp =  array (
				'amount'     => addslashes(trim(@$_POST['amount'])),  //original amount
				'curr'       => addslashes(trim(@$_POST['curr'])),    //original currency
				'invoice_id' => addslashes(trim(@$_POST['invoice_id'])),//original invoice id
				'ep_id'      => addslashes(trim(@$_POST['ep_id'])), //euplatescrate.ro unique id
				'merch_id'   => addslashes(trim(@$_POST['merch_id'])), //your merchant id
				'action'     => addslashes(trim(@$_POST['action'])), // if action ==0 transaction ok
				'message'    => addslashes(trim(@$_POST['message'])),// transaction responce message
				'approval'   => addslashes(trim(@$_POST['approval'])),// if action!=0 empty
				'timestamp'  => addslashes(trim(@$_POST['timestamp'])),// meesage timestamp
				'nonce'      => addslashes(trim(@$_POST['nonce'])),
				);
				 
				$zcrsp['fp_hash'] = strtoupper($this->euplatescrate_mac($zcrsp, $key));

				$fp_hash=addslashes(trim(@$_POST['fp_hash']));
				if($zcrsp['fp_hash']===$fp_hash)  {
				// start facem update in baza de date
					$id = $_POST['invoice_id'];
					
					$order = Mage::getModel('sales/order')->loadByIncrementId($id);

					if (!$order->getId()) {
						echo "Eroare"; exit;
					}

					if($zcrsp['action']=="0") {
						echo "Successfully completed";
						$order->setData('state', Mage::getStoreConfig('payment/euplatescrate/order_p_status'));
						$order->setStatus(Mage::getStoreConfig('payment/euplatescrate/order_p_status')); 
						$history = $order->addStatusHistoryComment('Order completed by using euplatesc gateway', false);
						$history->setIsCustomerNotified(true);
						$order->save();
						$order->sendNewOrderEmail();
					} else {
						echo "Tranzaction failed" . $zcrsp['message'];
						$order->setData('state', "canceled");
						$order->setStatus("canceled"); 
						$order->save();
					}
				}else {
					echo "Invalid signature";
				}
			}  else {echo"e0";}  
		}else{
			if(isset($_POST['invoice_id'])) {
				$zcrsp =  array (
					'amount'     => addslashes(trim(@$_POST['amount'])),  //original amount
					'curr'       => addslashes(trim(@$_POST['curr'])),    //original currency
					'invoice_id' => addslashes(trim(@$_POST['invoice_id'])),//original invoice id
					'ep_id'      => addslashes(trim(@$_POST['ep_id'])), //euplatescrate.ro unique id
					'merch_id'   => addslashes(trim(@$_POST['merch_id'])), //your merchant id
					'action'     => addslashes(trim(@$_POST['action'])), // if action ==0 transaction ok
					'message'    => addslashes(trim(@$_POST['message'])),// transaction responce message
					'approval'   => addslashes(trim(@$_POST['approval'])),// if action!=0 empty
					'timestamp'  => addslashes(trim(@$_POST['timestamp'])),// meesage timestamp
					'nonce'      => addslashes(trim(@$_POST['nonce'])),
					'sec_status' => addslashes(trim(@$_POST['sec_status'])),
				);
						 
				$zcrsp['fp_hash'] = strtoupper($this->euplatescrate_mac($zcrsp, $key));

				$fp_hash=addslashes(trim(@$_POST['fp_hash']));
				if($zcrsp['fp_hash']===$fp_hash)  {
					// start facem update in baza de date
					$id = $_POST['invoice_id'];
					$order = Mage::getModel('sales/order')->loadByIncrementId($id);

					if (!$order->getId()) {
						echo "Eroare"; exit;
					}

					if($zcrsp['action']=="0") {

						if($_POST['sec_status']==8 or $_POST['sec_status']==9){
							echo "Successfully completed";
							$order->setData('state', Mage::getStoreConfig('payment/euplatescrate/order_p_status'));
							$order->setStatus(Mage::getStoreConfig('payment/euplatescrate/order_p_status')); 
							$history = $order->addStatusHistoryComment('Order completed by using euplatesc gateway', false);
							$history->setIsCustomerNotified(true);
							$order->save();
							$order->sendNewOrderEmail();
						}else{
							$resource = Mage::getSingleton('core/resource');
							$writeConnection = $resource->getConnection('core_write');
							$query = "Insert INTO euplatescrate VALUES (null,'".$zcrsp['ep_id']."','".$zcrsp['invoice_id']."')";
							$writeConnection->query($query);
						}
							
							
					} else {
						echo "Tranzaction failed" . $zcrsp['message'];
						$order->setData('state', "canceled");
						$order->setStatus("canceled"); 
						$order->save();
					}

				} else {
					echo "Invalid signature";
				}
			} else if(isset($_POST['cart_id'])){
							
				$resource = Mage::getSingleton('core/resource');
				$readConnection = $resource->getConnection('core_read');
				$query = 'SELECT invoice_id FROM euplatescrate WHERE ep_id = "'. $_POST['cart_id'] .'"';
				$id = $readConnection->fetchOne($query);
				$order = Mage::getModel('sales/order')->loadByIncrementId((int)$id);
				Mage::log($order, null, 'debug3.log');
				if(isset($_POST['sec_status'])){
					if($_POST['sec_status']==8 or $_POST['sec_status']==9){
						echo "Successfully completed";
						$order->setData('state', Mage::getStoreConfig('payment/euplatescrate/order_p_status'));
						$order->setStatus(Mage::getStoreConfig('payment/euplatescrate/order_p_status'));
						$order->save();
						$order->sendNewOrderEmail();
					}if($_POST['sec_status']==5 or $_POST['sec_status']==6){
						echo "Tranzaction failed";
						$order->setData('state', "canceled");
						$order->setStatus("canceled"); 
						$order->save();
					}
				}
							
			}else {
				 echo"e1";
			}
		
		}
    }                 
}