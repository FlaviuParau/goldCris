<?php

class SMSLink_SMSGateway_Helper_Data extends Mage_Core_Helper_Data
{
	const CONFIG_PATH = 'smsgateway/';

	// ------------------------------------------------------------------------------

	//    Get SMS Gateway Configuration

	// ------------------------------------------------------------------------------

	public function getConnectionID()

	{

	    return trim(Mage::getStoreConfig(self::CONFIG_PATH.'general/api_key'));

	}

	

	public function getPassword()

	{

	    return trim(Mage::getStoreConfig(self::CONFIG_PATH.'general/api_secret'));

	}
	
	public function getHTTPSEnabled()

	{

	    return Mage::getStoreConfig(self::CONFIG_PATH.'general/httpsenabled');

	}
	
	public function getLogStatus($log_type = 1)

	{
	    if ($log_type == 1) return Mage::getStoreConfig(self::CONFIG_PATH.'general/logenabled');
	        else return Mage::getStoreConfig(self::CONFIG_PATH.'general/debugenabled');

	}
	
	// ------------------------------------------------------------------------------
	//    Get Notifications Status 
	// ------------------------------------------------------------------------------
	public function isAdminEnabled()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'admin/enabled');
	}

	public function isOrdersEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'orders/enabled');
    }

	public function isOrderHoldEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_hold/enabled');
    }

	public function isOrderUnholdEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_unhold/enabled');
    }

	public function isOrderCanceledEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_canceled/enabled');
    }

	public function isShipmentsEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'shipments/enabled');
    }

    // ------------------------------------------------------------------------------

    //    Get Administrator Notifications Details

    // ------------------------------------------------------------------------------    
	public function getAdminMobile()
	{
		return trim(Mage::getStoreConfig(self::CONFIG_PATH.'admin/admin_mobile'));
	}
	
	// ------------------------------------------------------------------------------

	//    SMS Notifications for Administrator about New Orders

	// ------------------------------------------------------------------------------
	public function getAdminMessage(Mage_Sales_Model_Order $order)
	{
		$store = Mage::app()->getStore();

		return str_replace(
    		            array(
		                    '{{sitename}}',
    		                '{{amount}}',
    		                '{{order_id}}'
                        ),
    		            array(
		                    $store->getFrontendName(),
		                    sprintf("%.2f", $order->getGrandTotal()),
		                    $order->getIncrementId()
                        ),
    		            Mage::getStoreConfig(self::CONFIG_PATH.'admin/message')
    		        );

	}

	// ------------------------------------------------------------------------------

	//    SMS Notifications for Customers about New Orders

	// ------------------------------------------------------------------------------
	public function getMessage(Mage_Sales_Model_Order $order)
	{
		$store = Mage::app()->getStore();
		$billingAddress = $order->getBillingAddress();

		return str_replace(

		        array(

		                '{{sitename}}',

		                '{{amount}}',

		                '{{order_id}}',
		                '{{firstname}}',
		                '{{lastname}}'

		        ),

		        array(

		                $store->getFrontendName(),

		                sprintf("%.2f", $order->getGrandTotal()),

		                $order->getIncrementId(),
		                $billingAddress->getFirstname(),
		                $billingAddress->getLastname()

		        ),

		        Mage::getStoreConfig(self::CONFIG_PATH.'orders/message')

		);

	}

	// ------------------------------------------------------------------------------

	//    SMS Notifications for Customers about Hold Orders

	// ------------------------------------------------------------------------------
	public function getMessageForOrderHold(Mage_Sales_Model_Order $order)
	{
	    $store = Mage::app()->getStore();

	    $billingAddress = $order->getBillingAddress();
		
	    return str_replace(

	            array(

	                    '{{sitename}}',

	                    '{{amount}}',

	                    '{{order_id}}',

	                    '{{firstname}}',

	                    '{{lastname}}'

	            ),

	            array(

	                    $store->getFrontendName(),

	                    sprintf("%.2f", $order->getGrandTotal()),

	                    $order->getIncrementId(),

	                    $billingAddress->getFirstname(),

	                    $billingAddress->getLastname()

	            ),

	            Mage::getStoreConfig(self::CONFIG_PATH.'order_hold/message')

	    );
	    
	}

	// ------------------------------------------------------------------------------

	//    SMS Notifications for Customers about Unhold Orders

	// ------------------------------------------------------------------------------
	public function getMessageForOrderUnhold(Mage_Sales_Model_Order $order)
	{
		$store = Mage::app()->getStore();
	    $billingAddress = $order->getBillingAddress();

	    return str_replace(

	            array(

	                    '{{sitename}}',

	                    '{{amount}}',

	                    '{{order_id}}',

	                    '{{firstname}}',

	                    '{{lastname}}'

	            ),

	            array(

	                    $store->getFrontendName(),

	                    sprintf("%.2f", $order->getGrandTotal()),

	                    $order->getIncrementId(),

	                    $billingAddress->getFirstname(),

	                    $billingAddress->getLastname()

	            ),

	            Mage::getStoreConfig(self::CONFIG_PATH.'order_unhold/message')

	    );

	}

	// ------------------------------------------------------------------------------

	//    SMS Notifications for Customers about Canceled Orders

	// ------------------------------------------------------------------------------
	public function getMessageForOrderCanceled(Mage_Sales_Model_Order $order)
	{
	    $store = Mage::app()->getStore();

	    $billingAddress = $order->getBillingAddress();


	    return str_replace(

	            array(

	                    '{{sitename}}',

	                    '{{amount}}',

	                    '{{order_id}}',

	                    '{{firstname}}',

	                    '{{lastname}}'

	            ),

	            array(

	                    $store->getFrontendName(),

	                    sprintf("%.2f", $order->getGrandTotal()),

	                    $order->getIncrementId(),

	                    $billingAddress->getFirstname(),

	                    $billingAddress->getLastname()

	            ),

	            Mage::getStoreConfig(self::CONFIG_PATH.'order_canceled/message')

	    );

	}

	public function getMessageForShipment(Mage_Sales_Model_Order $order)
	{
		$store = Mage::app()->getStore();
	    $billingAddress = $order->getBillingAddress(); 
		
        $track_numbers = array();
        foreach ($order->getTracksCollection() as $track)
        {
            $title = $track->getTitle();
            $track_numbers[] = ($title != '' ? '('.$title.': '.$track->getNumber().')' : $track->getNumber());
        }
        
        $track_numbers = implode(", ", $track_numbers);
        
        return str_replace(

                array(

                        '{{sitename}}',

                        '{{amount}}',

                        '{{order_id}}',

                        '{{firstname}}',

                        '{{lastname}}',

                        '{{tracking_info}}',

                        '{{awb_number}}'

                ),

                array(

                        $store->getFrontendName(),

                        sprintf("%.2f", $order->getGrandTotal()),

                        $order->getIncrementId(),

                        $billingAddress->getFirstname(),

                        $billingAddress->getLastname(),

                        $track_numbers,

                        $order->getAwbNumber(),

                ),

                Mage::getStoreConfig(self::CONFIG_PATH.'shipments/message')

        );
        
	}

	public function getTelephoneFromOrder(Mage_Sales_Model_Order $order)
    {
        $billingAddress = $order->getBillingAddress();
        $number = $billingAddress->getTelephone();
        
        return $number;

    }

    public function correctPhone($Phone)

    {

        $Phone = preg_replace('/\D/', '', $Phone);

    

        if (substr($Phone, 0, 4) == "0040")

            $Phone = substr($Phone, 3);

    

        if (substr($Phone, 0, 2) == "40")

            $Phone = substr($Phone, 1);

    

        if (strlen($Phone) < 10)

            if ($Phone[0] == "7")

            $Phone = "0".$Phone;

    

        return $Phone;

    

    }
    
    public function correctMessageText($Text)

    {

        $Find = array("\xC4\x82", "\xC4\x83", "\xC3\x82", "\xC3\xA2", "\xC3\x8E", "\xC3\xAE", "\xC8\x98", "\xC8\x99", "\xC8\x9A", "\xC8\x9B", "\xC5\x9E", "\xC5\x9F", "\xC5\xA2", "\xC5\xA3", "\xC3\xA3", "\xC2\xAD", "\xe2\x80\x93");

        $Replace = array("A", "a", "A", "a", "I", "i", "S", "s", "T", "t", "S", "s", "T", "t", "a", " ", "-");

    

        $Text = str_replace($Find, $Replace, $Text);

        $Text = trim($Text);

    

        return $Text;

    

    }
    
	public function SendSMS($api_parameters)
	{
	    $api_url = "http://www.smslink.ro/sms/gateway/communicate/index.php";
	    if (Mage::getStoreConfig(self::CONFIG_PATH.'admin/httpsenabled'))
	        $api_url = "https://secure.smslink.ro/sms/gateway/communicate/index.php";
	    
	    $session_id = uniqid();	    
	    
	    $ch = curl_init();

	    

	    curl_setopt($ch, CURLOPT_URL, $api_url."?".$api_parameters);

	    

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	    curl_setopt($ch, CURLOPT_HEADER, 0);

	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    

	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	    

	    if (strpos($api_url, "https://") !== false)

	    {

	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	    }

	    

	    $Result = @curl_exec($ch);

	    

	    $curl_errno = curl_errno($ch);

	    $curl_error = curl_error($ch);

	    

	    curl_close($ch);

	    if ($Result !== false)

	    {
	        return array(true, $session_id, "Request: ".$api_url."?".$api_parameters.", Response: ".$Result);
	    }
	    else
	    {
	        return array(false, $session_id, $curl_errno." - ".$curl_error);
	    }
	    
	}

}
