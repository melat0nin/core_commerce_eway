<?php 
require_once dirname(__FILE__) . '/EWayConfig.php';

class EwayPaymentHosted {
	var $myGatewayURL;
    var $myCustomerID;
    var $myTransactionData = array();
    
	//Class Constructor
	function EwayPaymentHosted($customerID = EWAY_DEFAULT_CUSTOMER_ID, $method = EWAY_DEFAULT_PAYMENT_METHOD ,$liveGateway  = EWAY_DEFAULT_LIVE_GATEWAY) {
		
		$this->myCustomerID = $customerID;
		$this->setTransactionData('CustomerID', $customerID);

	    switch($method){
			case REAL_TIME:
		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_HOSTED_REAL_TIME;
		    		else
	    				$this->myGatewayURL = EWAY_PAYMENT_HOSTED_REAL_TIME_TESTING_MODE;
	    		break;
			case REAL_TIME_CVN:
			case GEO_IP_ANTI_FRAUD:
		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_HOSTED_REAL_TIME_CVN;
		    		else
	    				$this->myGatewayURL = EWAY_PAYMENT_HOSTED_REAL_TIME_CVN_TESTING_MODE;
	    		break;	    	
    	}
	}
	
	//Set Transaction Data
	//Possible fields: "TotalAmount", "CustomerFirstName", "CustomerLastName", "CustomerEmail", "CustomerAddress", "CustomerPostcode", 
	//"CustomerInvoiceDescription", "CustomerInvoiceRef", "URL", "SiteTitle", "TrxnNumber", "Option1", "Option2", "Option3", "CVN"
	function setTransactionData($field, $value) {
		if($field=="TotalAmount")
			$value = round($value*100);
		$this->myTransactionData["eway" . $field] = htmlentities(trim($value));
	}

	function getTransactionData() {
		return $this->myTransactionData;
	}

	function getTransactionURL() {
		return $this->myGatewayURL;
	}
}
?>
