<?php
//==============================================================================
// Pay Pal Recurring Payments
// 
// Author: Avvici, Involution Media
// E-mail: joe@opencartvqmods.com
// Website: http://www.opencartvqmods.com
//==============================================================================
//Edit code with care. Always comment your code to take notes on what was changed so you can go back to it later.
//==============================================================================
class ControllerPaymentRecurringcallback extends Controller {
	public function index() {
			$request = 'cmd=_notify-validate';
		
			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}
isset($this->request->post['txn_type']) ? $txn_type = $this->request->post['txn_type'] : $txn_type = '';
isset($this->request->post['period_type']) ? $periodtype = $this->request->post['period_type'] : $periodtype = '';
isset($this->request->post['recurring_payment_id']) ? $recurringpid = $this->request->post['recurring_payment_id'] : $recurringpid = '';
isset($this->request->post['profile_status']) ? $pstatus = $this->request->post['profile_status'] : $pstatus = '';
isset($this->request->post['next_payment_date']) ? $next_payment_date = $this->request->post['next_payment_date'] : $next_payment_date = '';
isset($this->request->post['amount_per_cycle']) ? $amount_per_cycle = $this->request->post['amount_per_cycle'] : $amount_per_cycle = '';
isset($this->request->post['rp_invoice_id']) ? $orderid = $this->request->post['rp_invoice_id'] :$orderid = '';
isset($this->request->post['txn_id']) ? $txn_id = $this->request->post['txn_id'] : $txn_id = '';
isset($this->request->post['mc_gross']) ? $gross = $this->request->post['mc_gross'] : $gross = '';
isset($this->request->post['payment_status']) ? $payment_status = $this->request->post['payment_status'] : $payment_status = '';
			
				
			$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			
				
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					
			$response = curl_exec($curl);
			
			if (!$response) {
				$this->log->write('PP_RECURRING :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}
					
			if ($this->config->get('pp_recurring_debug')) {
				$this->log->write('PP_RECURRING :: IPN REQUEST: ' . $request);
				$this->log->write('PP_RECURRING :: IPN RESPONSE: ' . $response);
			}
				
		
				
			if($this->config->get('pp_pro_recurring_send_new')){
				if($txn_type =="recurring_payment_profile_created"){
					 $this->sendToAdmin($recurringpid,$orderid,$pstatus);
				}
			}
				
		//The majority of all IPN messages will be dealt with below in this switch statement.
				switch($txn_type) {	
			
			
			case 'recurring_payment':
			//Get existing order details to create a new order:
			$this->gatherOrderInfoAndAdd($txn_id,$pstatus,$recurringpid,$orderid,$periodtype,$payment_status);			
			
			break;
			
			case 'recurring_payment_skipped':	
				 $this->updateRecurringProfileStatus($pstatus,$recurringpid,$orderid,$periodtype);
				
			break;	
			
			case 'recurring_payment_suspended':
			 
			 $this->updateRecurringProfileStatus($pstatus,$recurringpid,$orderid,$periodtype);
			
			break;
			case 'recurring_payment_profile_cancel':
			 
			 $this->updateRecurringProfileStatus($pstatus,$recurringpid,$orderid,$periodtype);
			
			break;
		
			
		}
		     
			curl_close($curl);
			
	}

	private function SendToAdmin($recurringpid,$orderid,$pstatus){
		
		$this->load->model('checkout/order');		
		
		$order_info = $this->model_checkout_order->getOrder($orderid);
		
			
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_filename']);
			$language->load('mail/order');
			$subject = sprintf($language->get('text_new_subject'), $order_info['store_name'], $orderid);
			if(!defined('HTTP_IMAGE')){
				$message   = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo') . "\n\n";		
			}else{
				$message   = HTTP_IMAGE . $this->config->get('config_logo') . "\n\n";	
			}

			$message .= $language->get('text_new_order_id') . ' ' . $orderid . "\n";
			$message .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";
			
		
			$message .= 'Recurring Profile Created: ' . $recurringpid. "\n\n";
			$message .= 'Order ID: ' . $orderid. "\n\n";
			$message .= 'Profile Status: ' . $pstatus. "\n\n";
			$message .= $language->get('text_new_footer');
			
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
			
			$emails = explode(',', $this->config->get('config_alert_emails'));
				
				foreach ($emails as $email) {
					if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
						$mail->setTo($email);
						$mail->send();
					}
				}				
		
	}
	private function getCustomerId($orderid) {
		
				
		$query = $this->db->query("SELECT customer_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$orderid . "'");
		
		return $query->row;	
	}
	private function getRecurringCustomerGroupId() {
		
				
		$query = $this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` WHERE is_recurring = '1'");
		
		return $query->row;	
	}
	private function updateRecurringProfileStatus($pstatus,$recurringpid,$orderid,$periodtype){
		
		$this->load->model('checkout/order');
		
		if($pstatus == "Cancelled"){
			$pstatus = "Canceled";
		}
	        $order_status_id_query = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order_status` WHERE name = '" . (string)$pstatus . "'");
		if ($order_status_id_query->num_rows) {
				$order_status_id = $order_status_id_query->row['order_status_id'];	
		}else{
		$order_status_id = $this->config->get('config_order_status_id');	
		}
		$comment = 'Recurring Profile: ' . $pstatus;
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', paypal_recurring_status = '" . (string)$pstatus . "', date_modified = NOW() WHERE paypal_recurringprofile_id = '" . (string)$recurringpid . "'");
		//Update Customer Group Information
		$customer_id = $this->getCustomerId($orderid);	
		$recurring_group_id = $this->getRecurringCustomerGroupId();	
		if($pstatus == "Canceled"){
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' WHERE customer_id = '" . (int)$customer_id['customer_id'] . "'");
		}else{
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$recurring_group_id['customer_group_id'] . "' WHERE customer_id = '" . (int)$customer_id['customer_id'] . "'");
		}	
		
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$orderid . "', order_status_id = '" . (int)$order_status_id . "', notify = '1', comment = '" . (string)$comment . "', date_added = NOW()");
		$order_info = $this->model_checkout_order->getOrder($orderid);
		//check to see if emails are enabled
		if($this->config->get('pp_pro_recurring_showipn')){			
			
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_filename']);
			$language->load('mail/order');
			$subject = sprintf($language->get('text_new_subject'), $order_info['store_name'], $orderid);
			
		if(!defined('HTTP_IMAGE')){
				$message   = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo') . "\n\n";		
			}else{
				$message   = HTTP_IMAGE . $this->config->get('config_logo') . "\n\n";	
			}	
			$message .= $language->get('text_new_order_id') . ' ' . $orderid . "\n";
			$message .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";
			if ($order_status_id_query->num_rows) {
			$order_status_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
				
			if ($order_status_query->num_rows) {
				$message .= 'Order/Profile Status:' . "\n";
				$message .= $order_status_query->row['name'] . "\n\n";
			}else{
			$message .= 'Order/Profile Status:' . "\n";
			$message .= 'There is no status tied to this order. Please call in for customer support' . "\n\n";	
				
			}
			}else{
				$message .= 'Order/Profile Status:' . "\n";
			$message .= 'There is no status tied to this order. Please call in for customer support' . "\n\n";	
				
			}
		
			$message .= $language->get('text_new_comment') . "\n";
			$message .= strip_tags(html_entity_decode($comment, ENT_QUOTES, 'UTF-8')) . "\n\n";
			$message .= $language->get('text_new_footer');
			
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}
	
	private function gatherOrderInfoAndAdd($txn_id,$pstatus,$recurringpid,$order_id,$periodtype,$payment_status){
	$this->load->model('checkout/order');
			

		//Initial Querys
		$order_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
								
     
		$order_info = $this->getExisitingOrder($order_id);	
		// Order Totals			
			$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
			
			foreach ($order_total_query->rows as $order_total) {
				$this->load->model('total/' . $order_total['code']);
				
				if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
					$this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
				}
			}
		
		
		
		$data = array();
		$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
		$data['store_id'] = $this->config->get('config_store_id');
		$data['store_name'] = $order_info['store_name'];
		$data['store_url'] = $order_info['store_url'];
		$data['store_url'] = $order_info['store_url'];
		$data['customer_id'] = $order_info['customer_id'];
		$data['customer_group_id'] = $order_info['customer_group_id'];
		$data['payment_method'] = $order_info['payment_method'];
		$data['shipping_method'] = $order_info['shipping_method'];
		$data['firstname'] = $order_info['firstname'];
		$data['lastname'] = $order_info['lastname'];
		$data['payment_code'] = $order_info['payment_code'];
		$data['shipping_code'] = $order_info['shipping_code'];
		$data['email'] = $order_info['email'];
		$data['telephone'] = $order_info['telephone'];
		$data['fax'] = $order_info['fax'];
		$data['ip'] = $order_info['ip'];
		$data['forwarded_ip'] = $order_info['forwarded_ip'];
		$data['user_agent'] = $order_info['user_agent'];
		$data['accept_language'] = $order_info['accept_language'];
		$data['total'] = $order_info['total'];
		$data['language_id'] = $this->config->get('config_language_id');
		$data['currency_id'] = $this->currency->getId();
		$data['currency_code'] = $this->currency->getCode();
		$data['currency_value'] = $this->currency->getValue($this->currency->getCode());
		//products
		
		
		$product_data = array();			
			
		
			//check to see if any extra products appended to regular billing cycle or trial cycle. If so, replace the existing order_product data with the new product_data in the original recurring order.			
		    $combo_items = $order_info['paypal_recurring_combo'];
			
			if($combo_items != ""){
				 
				 if($periodtype !== " Trial"){  
				
	                $option = array();
				foreach(unserialize($combo_items) as $product_id){
					$this->cart->add($product_id, 1, $option);
					
				}	
				
				foreach ($this->cart->getProducts() as $product) {
					$outsidedetails = $this->getProductDetails($product['product_id']);
					$o = array();
					$d = array();
					$product_data[] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],					
					'quantity'   => $product['quantity'],
					'option'     => $o,
					'download'   => $d,
					'subtract'   => $outsidedetails['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['price'], $outsidedetails['tax_class_id']),
					'reward'     => $product['reward']
				
				);
					
				}
			
		     }
			 
			  
		
		 }else{
			 
			 //GATHER ORDER_PRODUCT DATA LIKE NORMAL
			foreach ($order_product_query->rows as $product) {
				
				
			
				$option_data = array();
				
				$order_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
				// Downloads
				$order_download_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_download` WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");	
				$download_data = array();
				
				if ($order_download_query->num_rows) {
						
						foreach ($order_download_query->rows as $download) {
					$download_data[] = array(
						'order_product_id'   => $download['order_product_id'],
						'order_id'           => $download['order_id'],
						'name'               => $download['name'],
						'filename'           => $download['filename'],								   
						'mask'               => $download['mask'],						
						'remaining'          => $download['remaining']
					);	
					
			}
				}else{
					
				$download_data	= array();
					
				}
				
				//
			
					foreach ($order_option_query->rows as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];	
					} else {
						$value = $this->encryption->decrypt($option['option_value']);
					}	
					
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],								   
						'name'                    => $option['name'],
						'value'                   => $value,
						'type'                    => $option['type']
					);					
				}
			    
				$outsidedetails = $this->getProductDetails($product['product_id']);
				
				$product_data[] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $download_data,
					'quantity'   => $product['quantity'],
					'subtract'   => $outsidedetails['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['price'], $outsidedetails['tax_class_id']),
					'reward'     => $product['reward']
				
				);
			}
		 
		 }
	
		
		
		
		
		//address stuff
		    $data['payment_firstname'] = $order_info['payment_firstname'];
			$data['payment_lastname'] = $order_info['payment_lastname'];	
			$data['payment_company'] = $order_info['payment_company'];	
			$data['payment_company_id'] = $order_info['payment_company_id'];	
			$data['payment_tax_id'] = $order_info['payment_tax_id'];	
			$data['payment_address_1'] = $order_info['payment_address_1'];
			$data['payment_address_2'] = $order_info['payment_city'];
			$data['payment_city'] = $order_info['payment_city'];
			$data['payment_postcode'] = $order_info['payment_postcode'];
			$data['payment_zone'] = $order_info['payment_zone'];
			$data['payment_zone_id'] = $order_info['payment_zone_id'];
			$data['payment_country'] = $order_info['payment_country'];
			$data['payment_country_id'] = $order_info['payment_country_id'];
			$data['payment_address_format'] = $order_info['payment_address_format'];
		      ////////////////
		        $data['shipping_firstname'] = $order_info['shipping_firstname'];
				$data['shipping_lastname'] = $order_info['shipping_lastname'];	
				$data['shipping_company'] = $order_info['shipping_company'];	
				$data['shipping_address_1'] = $order_info['shipping_address_1'];
				$data['shipping_address_2'] = $order_info['shipping_address_2'];
				$data['shipping_city'] = $order_info['shipping_city'];
				$data['shipping_postcode'] = $order_info['shipping_postcode'];
				$data['shipping_zone'] = $order_info['shipping_zone'];
				$data['shipping_zone_id'] = $order_info['shipping_zone_id'];
				$data['shipping_country'] = $order_info['shipping_country'];
				$data['shipping_country_id'] = $order_info['shipping_country_id'];
				$data['shipping_address_format'] = $order_info['shipping_address_format'];
			$data['products'] = $product_data;			
			$data['comment'] = '';
			
			$data['totals'] = $order_total_query->rows;
			if($periodtype === " Trial"){
				$tstatus = 1;
			}else{
				$tstatus = 0;
			}
			//ADD THE NEW ORDER (this generates a brand new order + ID
			 $new_order_id = $this->addRecurringOrder($data);
			 if($payment_status == "Completed"){
				 $recurringtype = "1";
			 }else{
				 
				$this->config->get('pp_pro_recurring_initialamountfail') == "ContinueOnFailure" ?  $recurringtype = "2" : $recurringtype = "3"; 
				 
			 }
				 
			 //CONFIRM
			 $this->model_checkout_order->confirmRecurring($new_order_id, $this->config->get('pp_pro_recurring_order_status_id'), $comment = '', $notify = true , $recurringpid, $tstatus , $txn_id, $recurringtype, $order_id);
	}
	

	private function getProductDetails($product_id) {
		
				
		$query = $this->db->query("SELECT subtract,tax_class_id FROM " . DB_PREFIX . "product  WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row;	
	}
	private function getExisitingOrder($order_id) {

		$order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}			
			
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			
			$this->load->model('localisation/language');
			
			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);
			
			if ($language_info) {
				$language_code = $language_info['code'];
				$language_filename = $language_info['filename'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_filename = '';
				$language_directory = '';
			}
		 			
			return array(
				'paypal_recurring_combo'  => $order_query->row['paypal_recurring_combo'],
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],				
				'customer_id'             => $order_query->row['customer_id'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],				
				'payment_company'         => $order_query->row['payment_company'],
				'payment_company_id'         => $order_query->row['payment_company_id'],
				'payment_tax_id'         => $order_query->row['payment_tax_id'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],	
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],				
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],	
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $order_query->row['order_status'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'language_filename'       => $language_filename,
				'language_directory'      => $language_directory,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'], 
				'user_agent'              => $order_query->row['user_agent'],	
				'accept_language'         => $order_query->row['accept_language'],				
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added']
			);
		} else {
			return false;	
		}
	}	
	private function addRecurringOrder($data = array()) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");

		$order_id = $this->db->getLastId();

		foreach ($data['products'] as $product) { 
			$this->db->query("INSERT INTO `" . DB_PREFIX . "order_product` SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");
 
			$order_product_id = $this->db->getLastId();

			foreach ($product['option'] as $option) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "order_option` SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
			}
			if(is_array($product['download'])){	
			foreach ($product['download'] as $download) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "order_download` SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int)($download['remaining'] * $product['quantity']) . "'");
			}
			}
		}
		
		foreach ($data['totals'] as $total) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
		}	

		return $order_id;
	}

}
?>
