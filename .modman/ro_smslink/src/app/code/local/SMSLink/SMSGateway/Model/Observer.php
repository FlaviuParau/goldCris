<?php

class SMSLink_SMSGateway_Model_Observer
{
	public function sendSmsOnOrderCreated(Varien_Event_Observer $observer)
	{
	    if ($this->getHelper()->getLogStatus(2))
	        Mage::log("Order Created.", null, 'smslink.debug.log');
	    
		if ($this->getHelper()->isOrdersEnabled())
		{
			$orders = $observer->getEvent()->getOrderIds();

			if(!empty($orders))
			{
			    $order = Mage::getModel('sales/order')->load($orders['0']);
			    $order_inc_id = $order->getIncrementId();
			    
			    if ($order instanceof Mage_Sales_Model_Order)
			    {				    
			    	$connection_id = $this->getHelper()->getConnectionID();
				    $password = $this->getHelper()->getPassword();
				
				    $destination = array(
			                "administrator" => $this->getHelper()->correctPhone($this->getHelper()->getAdminMobile()),
				            "customer"      => $this->getHelper()->correctPhone($this->getHelper()->getTelephoneFromOrder($order))
		                );
				    
				    $message = array(

				            "administrator" => $this->getHelper()->correctMessageText($this->getHelper()->getAdminMessage($order)),

				            "customer"      => $this->getHelper()->correctMessageText($this->getHelper()->getMessage($order))

				        );
				
				    if ($this->getHelper()->getLogStatus(2))
				        Mage::log("Information: Order placed with ID: ".$order_inc_id, null, 'smslink.debug.log');
				
				    $flag = 1;

				    if ($connection_id == '' || $password == '')
					    $flag = 0;
					
    				if ($flag == 1)
				    {
				        $api_parameters = 'connection_id='.urlencode($connection_id).

                				          '&password='.urlencode($password).

                				          '&to='.urlencode($destination["customer"]).

                				          '&message='.urlencode($message["customer"]);
    					
    					$api_result = $this->getHelper()->SendSMS($api_parameters);
				        
    					Mage::log("Sending SMS to Customer: Session ID: ".$api_result[1].", Communication: ".$api_result[2], null, 'smslink.debug.log');
    					
    					if ($this->getHelper()->isAdminEnabled() && $adminNo != '')
    					{
    					    $api_parameters = 'connection_id='.urlencode($connection_id).

                    					      '&password='.urlencode($password).

                    					      '&to='.urlencode($destination["administrator"]).

                    					      '&message='.urlencode($message["administrator"]);

					        $api_result = $this->getHelper()->SendSMS($api_parameters);
    					    
					        if ($this->getHelper()->getLogStatus())
        					    Mage::log("Sending SMS to Administrator: Session ID: ".$api_result[1].", Communication: ".$api_result[2], null, 'smslink.log');
        					
    					}
    
    				}
    				else
    				{
    				    if ($this->getHelper()->getLogStatus(2))
    					    Mage::log("Error: SMS Gateway Connection ID and Password are not configured.", null, 'smslink.debug.log');
    					
    				}
    				
    			}
    			
			} 
			else
			{
			    if ($this->getHelper()->getLogStatus(2))
				    Mage::log("Error: Unable to call observer for Order Created for Order ID: ".$order_inc_id, null, 'smslink.debug.log');
			    				
			}
			
        }
        
	}

	public function sendSmsOnOrderStatusChange(Varien_Event_Observer $observer)
	{
	    if ($this->getHelper()->getLogStatus(2))
	        Mage::log("Order Status Changed.", null, 'smslink.debug.log');

	    
	    $order = $observer->getOrder();

        $message = "";
        
        switch (true)
        {
            case (($order->getState() !== $order->getOrigData('state')) and ($order->getState() === Mage_Sales_Model_Order::STATE_HOLDED) and ($this->getHelper()->isOrderHoldEnabled())):
                $message = $this->getHelper()->correctMessageText($this->getHelper()->getMessageForOrderHold($order));
                if ($this->getHelper()->getLogStatus(2))
                    Mage::log("Order Status is Hold.", null, 'smslink.debug.log');
                break;
            case (($order->getState() !== $order->getOrigData('state')) and ($order->getOrigData('state') === Mage_Sales_Model_Order::STATE_HOLDED) and ($this->getHelper()->isOrderUnholdEnabled())):
                $message = $this->getHelper()->correctMessageText($this->getHelper()->getMessageForOrderUnhold($order));
                if ($this->getHelper()->getLogStatus(2))
                    Mage::log("Order Status is Unhold.", null, 'smslink.debug.log');
                break;
            case (($order->getState() !== $order->getOrigData('state')) and ($order->getState() === Mage_Sales_Model_Order::STATE_CANCELED) and ($this->getHelper()->isOrderCanceledEnabled())):
                $message = $this->getHelper()->correctMessageText($this->getHelper()->getMessageForOrderCanceled($order));
                if ($this->getHelper()->getLogStatus(2))
                    Mage::log("Order Status is Canceled.", null, 'smslink.debug.log');
                break;
                
        }
        
        if (strlen(trim($message)) > 0)
        {

            if ($order instanceof Mage_Sales_Model_Order)

            {

                $order_inc_id = $order->getIncrementId();

                               

                $connection_id = $this->getHelper()->getConnectionID();

                $password = $this->getHelper()->getPassword();
                

                $destination = $this->getHelper()->correctPhone($this->getHelper()->getTelephoneFromOrder($order));
                                
                $flag = 1;
    		    if ($connection_id == '' || $password == '')
    			    $flag = 0;
    			
    			if ($flag == 1)
    		    {

                    $api_parameters = 'connection_id='.urlencode($connection_id).
            				          '&password='.urlencode($password).
            				          '&to='.urlencode($destination).
            				          '&message='.urlencode($message);
					
					$api_result = $this->getHelper()->SendSMS($api_parameters);
			        
					if ($this->getHelper()->getLogStatus())
					    Mage::log("Sending SMS to Customer: Session ID: ".$api_result[1].", Communication: ".$api_result[2], null, 'smslink.log');

        

                }

                else

                {
                    if ($this->getHelper()->getLogStatus(2))

                        Mage::log("Error: SMS Gateway Connection ID and Password are not configured.", null, 'smslink.debug.log');
                    

                }

            

            }

            else

            {
                if ($this->getHelper()->getLogStatus(2))

                    Mage::log("Error: Unable to call observer for Order Status Change for Order ID: ".$order_inc_id, null, 'smslink.debug.log');
                			

            }
            
	    }

	    

	}
	
	public function sendSmsOnShipmentCreated(Varien_Event_Observer $observer)
	{
		if($this->getHelper()->isShipmentsEnabled() && Mage::registry('sms_shipping_sent') != true)
		{
		    if ($this->getHelper()->getLogStatus(2))
			    Mage::log("Order is Shipped.", null, 'smslink.debug.log');
			
			$shipment = $observer->getEvent()->getShipment();
			$order = $shipment->getOrder();

			if ($order instanceof Mage_Sales_Model_Order)
			{
				$order_inc_id = $order->getIncrementId();
				
				$connection_id = $this->getHelper()->getConnectionID();

				$password = $this->getHelper()->getPassword();
				
				$destination = $this->getHelper()->correctPhone($this->getHelper()->getTelephoneFromOrder($order));
				$message = $this->getHelper()->correctMessageText($this->getHelper()->getMessageForShipment($order));
				
				$flag = 1;

				if ($connection_id == '' || $password == '')

				    $flag = 0;

				 

				if ($flag == 1)

				{

				    $api_parameters = 'connection_id='.urlencode($connection_id).

                    				  '&password='.urlencode($password).

                    				  '&to='.urlencode($destination).

                    				  '&message='.urlencode($message);

				    	

				    $api_result = $this->getHelper()->SendSMS($api_parameters);


				    if ($this->getHelper()->getLogStatus())

				        Mage::log("Sending SMS to Customer: Session ID: ".$api_result[1].", Communication: ".$api_result[2], null, 'smslink.log');


                    try {
                        Mage::register('sms_shipping_sent', true);
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
				}

				else

				{
				    if ($this->getHelper()->getLogStatus(2))

				        Mage::log("Error: SMS Gateway Connection ID and Password are not configured.", null, 'smslink.debug.log');

				

				}

			}
			else
			{
			    if ($this->getHelper()->getLogStatus(2))
				    Mage::log("Error: Unable to call observer for Order Shipment for Order ID: ".$order_inc_id, null, 'smslink.debug.log');
				
			}

		}
		
	}

	public function getHelper()
    {
        return Mage::helper('smsgateway/Data');
        
    }
    
}
