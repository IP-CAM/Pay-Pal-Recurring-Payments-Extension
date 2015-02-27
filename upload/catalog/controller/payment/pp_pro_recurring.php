<?php
 //==============================================================================
// Pay Pal Recurring Payments
// 
// Author: Avvici, Opencart Vqmods
// E-mail: joe@opencartvqmods.com
// Website: http://www.opencartvqmods.com
//==============================================================================
//Edit code with care. Always comment your code to take notes on what was changed so you can go back to it later.
//==============================================================================
class ControllerPaymentPPProRecurring extends Controller {
	protected function index() {
    	$this->language->load('payment/pp_pro_recurring');
		
		$this->data['text_credit_card'] = $this->language->get('text_credit_card');
		$this->data['text_start_date'] = $this->language->get('text_start_date');
		$this->data['text_issue'] = $this->language->get('text_issue');
		$this->data['text_wait'] = $this->language->get('text_wait');
		
		$this->data['entry_cc_type'] = $this->language->get('entry_cc_type');
		$this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$this->data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');
		$this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$this->data['entry_cc_issue'] = $this->language->get('entry_cc_issue');
		
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		$this->data['cards'] = array();

		$this->data['cards'][] = array(
			'text'  => 'Visa', 
			'value' => 'VISA'
		);

		$this->data['cards'][] = array(
			'text'  => 'MasterCard', 
			'value' => 'MASTERCARD'
		);

		$this->data['cards'][] = array(
			'text'  => 'Discover Card', 
			'value' => 'DISCOVER'
		);
		
		$this->data['cards'][] = array(
			'text'  => 'American Express', 
			'value' => 'AMEX'
		);

		$this->data['cards'][] = array(
			'text'  => 'Maestro', 
			'value' => 'SWITCH'
		);
		
		$this->data['cards'][] = array(
			'text'  => 'Solo', 
			'value' => 'SOLO'
		);		
	
		$this->data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		 
	
		$today = getdate();
		
		$this->data['year_valid'] = array();
		
		for ($i = $today['year'] - 10; $i < $today['year'] + 1; $i++) {	
			$this->data['year_valid'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)), 
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pp_pro_recurring.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/pp_pro_recurring.tpl';
		} else {
			$this->template = 'default/template/payment/pp_pro_recurring.tpl';
		}	
		
		$this->render();		
	}

	public function send() {
		if (!$this->config->get('pp_pro_recurring_transaction')) {
			$payment_type = 'Authorization';	
		} else {
			$payment_type = 'Sale';
		}
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);		
		$request  = 'METHOD=' .urlencode("CreateRecurringPaymentsProfile");
	    $request .= '&VERSION=' .urlencode("53.0");
	    $request .= '&USER=' . urlencode($this->config->get('pp_pro_recurring_username'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_recurring_password'));
		$request .= '&SIGNATURE=' . urlencode($this->config->get('pp_pro_recurring_signature'));
		if($this->config->get('pp_pro_recurring_useorderid')){
		$request .= '&PROFILEREFERENCE=' . (int)$order_info['order_id'];
		}else{
		$request .= '&PROFILEREFERENCE=' . (int)$this->config->get('pp_pro_recurring_reference');
		}
		$request .= '&PAYMENTACTION=' . urlencode($payment_type);
		
		
		if($this->config->get('pp_pro_recurring_usetotal')){
			//Get Dyanmic total
		$products = $this->cart->getProducts();
		
		foreach ($products as $product) {
        $trial_product_ids = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE is_trial = '1' AND is_combo = '1'");			
		$ids = array();
		
		if($trial_product_ids->num_rows){
	    
		foreach($trial_product_ids->rows as $product_id){
		$ids[] = $product_id['product_id'];
		}
		if(in_array($product['product_id'],$ids)){
		$combo_data = $this->db->query("SELECT recurring_combo, price FROM `" . DB_PREFIX . "product` WHERE  product_id = '".(int)$product['product_id']."'");
		 foreach($combo_data->rows as $value){
			$base_price = $value['price'];
				$newprice = $this->getProducts(unserialize($value['recurring_combo']));
			
			foreach($newprice as $price){
				$base_price += $price['price'];
			}
			//Combo Item Total. Trial + Regular
		$request .= '&AMT=' . $this->currency->format($base_price, $order_info['currency_code'], false, false);
			
		}
        }
        }else{
		   //if just taking the order total. No Trial. Regular Recurring Item Only.
        $request .= '&AMT=' . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);	
         
		  }
		 
       }
		
		}else{
			//Custom Amount
		$request .= '&AMT=' . $this->currency->format($this->config->get('pp_pro_recurring_amount'), $order_info['currency_code'], false, false);
		}
		$request .= '&CREDITCARDTYPE=' . urlencode($this->request->post['cc_type']);
		$request .= '&ACCT=' . urlencode(str_replace(' ', '', $this->request->post['cc_number']));
		$request .= '&EXPDATE=' . urlencode($this->request->post['cc_expire_date_month'] . $this->request->post['cc_expire_date_year']);
		$request .= '&CARDSTART=' . urlencode($this->request->post['cc_start_date_month'] . $this->request->post['cc_start_date_year']);
		if ($this->request->post['cc_type'] == 'SWITCH' || $this->request->post['cc_type'] == 'SOLO') { 
			$request .= '&CARDISSUE=' . urlencode($this->request->post['cc_issue']);
		}
		$request .= '&CVV2=' . urlencode($this->request->post['cc_cvv2']);
		if($this->config->get('pp_pro_recurring_subscribername')){
		$request .= '&SUBSCRIBERNAME=' . urlencode($this->config->get('pp_pro_recurring_subscribername'));
		}else{
		$request .= '&SUBSCRIBERNAME=' . urlencode($order_info['payment_firstname']. ' ' . $order_info['payment_lastname']);
		}
		if($this->config->get('pp_pro_recurring_start_date') && $this->config->get('pp_pro_recurring_start_date') != ""){
		$request .= '&PROFILESTARTDATE=' .  urlencode(date('Y-m-d H:i:s', strtotime("+".$this->config->get('pp_pro_recurring_start_date')." days"))); 
		
		}else{
		$request .= '&PROFILESTARTDATE=' .  urlencode(date('Y-m-d H:i:s')); 
		}
		$request .= '&DESC=' .urlencode($this->config->get('pp_pro_recurring_desc')); 
		//Configure custom recurring item cycle information if any
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
        $item_ids = $this->db->query("SELECT item_product FROM `" . DB_PREFIX . "paypal_recurring_items` WHERE item_status = '1'");			
		$ids = array();
		if($item_ids->num_rows){
	    
		foreach($item_ids->rows as $product_id){
		$ids[] = $product_id['item_product'];
		}
		if(in_array($product['product_id'],$ids)){
		 $recurringitems = $this->db->query("SELECT * FROM `" . DB_PREFIX . "paypal_recurring_items` WHERE item_product = '".(int)$product['product_id']."'"); 
		 foreach($recurringitems->rows as $value){
		
		$request .= '&BILLINGPERIOD=' .urlencode($value['item_period']); 
		$request .= '&BILLINGFREQUENCY=' .urlencode($value['item_frequency']);
		$request .= '&TOTALBILLINGCYCLES=' .urlencode($value['item_cycles']);
			
		}
        }else{
		   //if just taking the order total. No Trial. Regular Recurring Item Only.
       
		$request .= '&BILLINGPERIOD=' .urlencode($this->config->get('pp_pro_recurring_billingperiod')); 
		$request .= '&BILLINGFREQUENCY=' .urlencode($this->config->get('pp_pro_recurring_billingfrequency'));
		$request .= '&TOTALBILLINGCYCLES=' .urlencode($this->config->get('pp_pro_recurring_billingcycles'));
        }
          }else{
			  
		$request .= '&BILLINGPERIOD=' .urlencode($this->config->get('pp_pro_recurring_billingperiod')); 
		$request .= '&BILLINGFREQUENCY=' .urlencode($this->config->get('pp_pro_recurring_billingfrequency'));
		$request .= '&TOTALBILLINGCYCLES=' .urlencode($this->config->get('pp_pro_recurring_billingcycles'));
		  }
        }
		
		
		//////
		if($this->config->get('pp_pro_recurring_initialamount') != ""){			
		$request .= '&INITAMT=' . $this->currency->format($this->config->get('pp_pro_recurring_initialamount'), $order_info['currency_code'], false, false);
		}
		if($this->config->get('pp_pro_recurring_taxamount') != ""){			
		$request .= '&TAXAMT=' . $this->currency->format($this->config->get('pp_pro_recurring_taxamount'), $order_info['currency_code'], false, false);
		}
		if($this->config->get('pp_pro_recurring_shippingamount') != ""){			
		$request .= '&SHIPPINGAMT=' . $this->currency->format($this->config->get('pp_pro_recurring_shippingamount'), $order_info['currency_code'], false, false);
		}
		$request .= '&AUTOBILLOUTAMT=' . urlencode($this->config->get('pp_pro_recurring_autobill'));
		$request .= '&FAILEDINITAMTACTION=' .urlencode($this->config->get('pp_pro_recurring_initialamountfail'));
		$request .= '&MAXFAILEDPAYMENTS=' .urlencode($this->config->get('pp_pro_recurring_maxfailed'));
		$request .= '&FIRSTNAME=' . urlencode($order_info['payment_firstname']);
		$request .= '&LASTNAME=' . urlencode($order_info['payment_lastname']);
		$request .= '&EMAIL=' . urlencode($order_info['email']);
		$request .= '&PHONENUM=' . urlencode($order_info['telephone']);
		$request .= '&IPADDRESS=' . urlencode($this->request->server['REMOTE_ADDR']);
		$request .= '&STREET=' . urlencode($order_info['payment_address_1']);
		$request .= '&CITY=' . urlencode($order_info['payment_city']);
		$request .= '&STATE=' . urlencode(($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code']);
		$request .= '&ZIP=' . urlencode($order_info['payment_postcode']);
		$request .= '&COUNTRYCODE=' . urlencode($order_info['payment_iso_code_2']);
		$request .= '&CURRENCYCODE=' . urlencode($order_info['currency_code']);
		if($this->config->get('pp_pro_recurring_trial')){	
		//Set up the correct trial according to what is inside the cart
		$products = $this->cart->getProducts();
				
		foreach ($products as $product) {
        $trial_product_ids = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE is_trial = '1'");			
		$ids = array();
		if($trial_product_ids){
	    
		foreach($trial_product_ids->rows as $product_id){
		$ids[] = $product_id['product_id'];
		}
		if(in_array($product['product_id'],$ids)){
			$has_trial = true;
		$trial_data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "paypal_recurring_trials` WHERE  trial_product = '".(int)$product['product_id']."'");
		foreach($trial_data->rows as $value){
		$request .= '&TRIALBILLINGPERIOD=' .urlencode($value['trial_period']);	
		$request .= '&TRIALBILLINGFREQUENCY=' .urlencode($value['trial_frequency']);	
		$request .= '&TRIALTOTALBILLINGCYCLES=' .urlencode($value['trial_cycles']);
		$request .= '&TRIALAMT=' . $this->currency->format($value['trial_amount'], $order_info['currency_code'], false, false);
			
		}
}else{
	    $has_trial = false;
}
}
}
		
}else{
	 $has_trial = false;
}
		if ($this->request->post['cc_type'] == 'SWITCH' || $this->request->post['cc_type'] == 'SOLO') { 
			$request .= '&CARDISSUE=' . urlencode($this->request->post['cc_issue']);
		}
		$request .= '&FIRSTNAME=' . urlencode($order_info['payment_firstname']);
		$request .= '&LASTNAME=' . urlencode($order_info['payment_lastname']);
		$request .= '&EMAIL=' . urlencode($order_info['email']);
		$request .= '&PHONENUM=' . urlencode($order_info['telephone']);
		$request .= '&IPADDRESS=' . urlencode($this->request->server['REMOTE_ADDR']);
		$request .= '&STREET=' . urlencode($order_info['payment_address_1']);
		$request .= '&CITY=' . urlencode($order_info['payment_city']);
		$request .= '&STATE=' . urlencode(($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code']);
		$request .= '&ZIP=' . urlencode($order_info['payment_postcode']);
		$request .= '&COUNTRYCODE=' . urlencode($order_info['payment_iso_code_2']);
		$request .= '&CURRENCYCODE=' . urlencode($order_info['currency_code']);
		
        if ($this->cart->hasShipping()) {
			$request .= '&SHIPTONAME=' . urlencode($order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname']);
			$request .= '&SHIPTOSTREET=' . urlencode($order_info['shipping_address_1']);
			$request .= '&SHIPTOCITY=' . urlencode($order_info['shipping_city']);
			$request .= '&SHIPTOSTATE=' . urlencode(($order_info['shipping_iso_code_2'] != 'US') ? $order_info['shipping_zone'] : $order_info['shipping_zone_code']);
			$request .= '&SHIPTOCOUNTRYCODE=' . urlencode($order_info['shipping_iso_code_2']);
			$request .= '&SHIPTOZIP=' . urlencode($order_info['shipping_postcode']);
        } else {
			$request .= '&SHIPTONAME=' . urlencode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']);
			$request .= '&SHIPTOSTREET=' . urlencode($order_info['payment_address_1']);
			$request .= '&SHIPTOCITY=' . urlencode($order_info['payment_city']);
			$request .= '&SHIPTOSTATE=' . urlencode(($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code']);
			$request .= '&SHIPTOCOUNTRYCODE=' . urlencode($order_info['payment_iso_code_2']);
			$request .= '&SHIPTOZIP=' . urlencode($order_info['payment_postcode']);			
		}		
		
		if (!$this->config->get('pp_pro_test')) {
			$curl = curl_init('https://api-3t.paypal.com/nvp');
		} else {
			$curl = curl_init('https://api-3t.sandbox.paypal.com/nvp');
		}
		
		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);


		$response = curl_exec($curl);
 		
		curl_close($curl);
 
		if (!$response) {
			$this->log->write('DoDirectPayment failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
 
 		$response_data = array();
 
		parse_str($response, $response_data);

		$json = array();
		
		if (($response_data['ACK'] == 'Success') || ($response_data['ACK'] == 'SuccessWithWarning')) {
			$profilestatus = '';
			$pptransactionid = '';
			$profileid = '';
			$notify=true;
			$error='';
			//My own hack for paypal's inability to return a failed transaction response from here.
			if (isset($response_data['PROFILEID'])) {
				$profileid .=  $response_data['PROFILEID'];
			}
			if (isset($response_data['PROFILESTATUS'])) {
				$profilestatus .=  $response_data['PROFILESTATUS'];
				
			}
			
			if (isset($response_data['TRANSACTIONID']) && $profilestatus == "ActiveProfile" || $profilestatus == "PendingProfile") {
				if (isset($response_data['TRANSACTIONID'])){
				     $pptransactionid .=  $response_data['TRANSACTIONID'];
				}else{
					  $pptransactionid =  '';
					
				}
				     //Confirm order and store recurring profile ID + Transaction ID along with order details
		            $this->model_checkout_order->confirmRecurringNew($this->session->data['order_id'], $this->config->get('pp_pro_recurring_order_status_id'), $comment = '', $notify , $profileid, $has_trial ? $this->config->get('pp_pro_recurring_trial') : 0 , $pptransactionid, $recurringtype = "1");
				  if (defined('VERSION')){
					if(VERSION === '1.5.4'){
					$json['success'] = $this->url->link('checkout/recurring_success154_continue');
					}else{
					$json['success'] = $this->url->link('checkout/recurring_success_continue');
					}
					}
			}else if(!isset($response_data['TRANSACTIONID']) && $profilestatus == "ActiveProfile") {
				
				     $pptransactionid .=  '';
				     //Confirm order and store recurring profile ID + Transaction ID along with order details
		            $this->model_checkout_order->confirmRecurringNew($this->session->data['order_id'], $this->config->get('pp_pro_recurring_order_status_id'), $comment = '', $notify , $profileid, $has_trial ? $this->config->get('pp_pro_recurring_trial') : 0 , $pptransactionid, $recurringtype = "1");
				    if (defined('VERSION')){
					if(VERSION === '1.5.4'){
					$json['success'] = $this->url->link('checkout/recurring_success154_continue');
					}else{
					$json['success'] = $this->url->link('checkout/recurring_success_continue');
					}
					}
			}else{
				//if continue on fail transaction, send to success page with notice of failed transaction and that profile WAS created. Failed qpayment amount is due on next billing cycle 
				if($this->config->get('pp_pro_recurring_initialamountfail') == "ContinueOnFailure"){
					$this->model_checkout_order->confirmRecurringNew($this->session->data['order_id'], $this->config->get('pp_pro_recurring_declined_order_status_id'), $comment = '', $notify , $profileid, $has_trial ? $this->config->get('pp_pro_recurring_trial') : 0 , $pptransactionid, $recurringtype = "2");
					$json['transactionforward'] = $this->url->link('checkout/recurring_failure_continue');
			}else{
				//if cancel on fail transaction, send to success page with notice of failed transaction and that profile is suspended until payment clears.. 
				   $this->model_checkout_order->confirmRecurringNew($this->session->data['order_id'], $this->config->get('pp_pro_recurring_declined_order_status_id'), $comment = '', $notify , $profileid, $has_trial ? $this->config->get('pp_pro_recurring_trial') : 0 , $pptransactionid, $recurringtype = "3");
				 
					$json['transactionforward'] = $this->url->link('checkout/recurring_failure_cancel');
				
				}
			}
			
		} else {
			
			if($response_data['L_LONGMESSAGE0'] === "Missing Token or buyer credit card"){
        	$json['error'] = "You must enter a valid credit card number.";
        
		}else{
			$error = $response_data['L_LONGMESSAGE0'];
			if($error == "Internal Error"){
					$json['error'] = "This is not a valid card number. Please enter a valid number.";
			}else{
        	$json['error'] = $error;
			}
		}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	private function getProducts($products) {
		
		$product_data = array();
		foreach ($products as $product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			 
			if ($query) {
				$product_data[] = array(
					'price' => $query->row['price']
					
				);
			}
		}
		return $product_data;
	}
}
?>