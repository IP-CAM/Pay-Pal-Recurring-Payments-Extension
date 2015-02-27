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
class ControllerSaleRecurringOrder extends Controller {
	private $error = array();
	//Set up Refunds
	public function refund(){
		$json = array();
		$this->load->model('sale/order');
	   $order_id = $this->request->get['order_id'];
	   //Get Currency Code
	   $currency_code = $this->db->query("SELECT currency_code FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
	  //Get Transaction ID (paypal)
	  $transaction_id = $this->db->query("SELECT paypal_transaction_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		 if($transaction_id->row['paypal_transaction_id'] != ""){
		
          	if($this->request->post['refundtotal'] != ""){
				 
		
		//Now connect with Pay Pal 
		$request  = 'METHOD=RefundTransaction';
	    $request .= '&VERSION=' .urlencode("53.0");
	    $request .= '&USER=' . urlencode($this->config->get('pp_pro_recurring_username'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_recurring_password'));
		$request .= '&SIGNATURE=' . urlencode($this->config->get('pp_pro_recurring_signature'));	
		
		//new profile fields
		$request .= '&TRANSACTIONID=' . urlencode($transaction_id->row['paypal_transaction_id']);
		$request .= '&REFUNDTYPE=Partial';
		$request .= '&REFUNDSOURCE=any';
		$request .= '&AMT=' . urlencode($this->request->post['refundtotal']);
		$request .= '&CURRENCYCODE='.urlencode($currency_code->row['currency_code']);
		$request .= '&NOTE=' . urlencode($this->request->post['refundnotes']);
		
		$curl = curl_init('https://api-3t.paypal.com/nvp');
		
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
			$this->log->write('Refund Manager Has Failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
 
 		$response_data = array();
 
		parse_str($response, $response_data);
		if (($response_data['ACK'] == 'Success') || ($response_data['ACK'] == 'SuccessWithWarning')) {
		
			$id = '';
			
            if (isset($response_data['PROFILEID'])) {
				$id .=  $response_data['PROFILEID'];
			}
			$data=array();
			$data['order_status_id'] = $this->config->get('pp_pro_recurring_refunded_order_status_id');
			$data['notify'] = $this->request->get['refundnotify'];
			$data['comment'] = $this->request->post['refundnotes'];
			
			$this->model_sale_order->addOrderHistory($order_id,$data);
			 
			 $json['success'] = "Successs: Refund Applied. This order cannot be refunded again.";
			 
		}else{
			    if(isset($response_data['L_ERRORCODE0'])){
                    switch($response_data['L_ERRORCODE0']){
                        case "10001":
                        $json['error'] = "Invalid Data: The transaction ID may not be correct.";
                        break;
                        case "10004":
                        $json['error'] = "Invalid Data: The refund amount must be a positive(+) amount, or a transaction ID is missing.";
                        break;
                        case "10007":
                        $json['error'] = "Error: You do not have permission to refund this transaction";
                        break;                    
                        case "10009":
                        $json['error'] = "Error: The refund amount must be less than or equal to the original transaction amount";
                        break;
                        case "10011":
                        $json['error'] = "Error: Transaction refused because of an invalid transaction id value";
                        break;                      
                        default: 
                        $json['error'] = $response_data['L_LONGMESSAGE0'];
                    }
                
            }else{
                $json['error'] = $response_data['L_LONGMESSAGE0'];
            } 
		}
		
		    }else{
			 
			$json['error'] = "Error: You must fill out the refund amount"; 
		 }
		 }else{
			 
			$json['error'] = "Error: There is no Paypal Transaction ID related to this order threfore it cannot be refunded"; 
		 }
		
		$this->response->setOutput(json_encode($json));
	}
 public function changeSingleStatus() {
	      $json = array(); 
		$this->data = array_merge($this->data, $this->load->language('sale/recurring_order'));
		$this->load->model('sale/recurring_order');
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/recurring_order')) { 
				
			/// CONNECT WITH PAY PAL TO CHANGE TRIAL PROFILE STATUS
		$request  = 'METHOD=ManageRecurringPaymentsProfileStatus';
	    $request .= '&VERSION=' .urlencode("53.0");
	    $request .= '&USER=' . urlencode($this->config->get('pp_pro_recurring_username'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_recurring_password'));
		$request .= '&SIGNATURE=' . urlencode($this->config->get('pp_pro_recurring_signature'));
		
		//new profile fields
		$request .= '&PROFILEID=' . urlencode($this->request->get['profile_id']);
		
		
		$sql = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND order_status_id = '" . (int)$this->request->post['status_type'] . "'");
		
		switch($sql->row['name']){
		case "Canceled":
		$status = "Cancel";
		break;
		case "Active":
		$status = "Reactivate";
		break;
		case "Suspended":
		$status = "Suspend";
		break;
		}
		
		$request .= '&ACTION=' . urlencode($status);
		$request .= '&NOTE=' . urlencode($this->request->post['reason']);
		
		$curl = curl_init('https://api-3t.paypal.com/nvp');
		
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
			$this->log->write('Cancel Profile Recurring details failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
 
 		$response_data = array();
 
		parse_str($response, $response_data);
		
		if (($response_data['ACK'] == 'Success') || ($response_data['ACK'] == 'SuccessWithWarning')) {
						
			//Now reconnect with Pay Pal to get the new profile status
			
	
			$pid = '';
			$status = '';
		
            if (isset($response_data['PROFILEID'])) {
				$pid .=  $response_data['PROFILEID'];
			
						
			//UPDATE TRIAL PROFILE WITH CURRENT INFORMATION AND RELOAD PAGE
			 $this->updateRecurringProfileShort($pid,$this->request->post['reason']);
			 //ADD TO ORDER HISTORY LOG
			 $data = array();
			 $data = array('comment' => addslashes($this->request->post['reason']) ,'order_status_id' => $this->request->post['status_type'] , 'notify' => $this->request->get['notify-status']);
			 
			  $this->model_sale_recurring_order->addOrderHistory($this->request->get['order_id'], $data);
			 		
			 
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('sale/recurring_order/info', 'order_id=' .$this->request->get['order_id'].'&token='.$this->session->data['token'].'&statuschange=true'));
			$this->session->data['success'] = "Sucess: You have edited/altered the recurring profile!";
			
			}else{
			
			 $json['error'] = "Error: Incompatible status change. Try again.";
			}
		}else{
			$json['error'] = $response_data['L_LONGMESSAGE0'];
		}
		
		
		} 
		
		$this->response->setOutput(json_encode($json));
  	}
	private function updateRecurringProfileShort($profileid,$reason) {
			
			//CONNECT WITH PAY PAL TO GET RECURRING PROFILE INFORMATION
	    $request  = 'METHOD=' .urlencode("GetRecurringPaymentsProfileDetails");
	    $request .= '&VERSION=' .urlencode("53.0");
	    $request .= '&USER=' . urlencode($this->config->get('pp_pro_recurring_username'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_recurring_password'));
		$request .= '&SIGNATURE=' . urlencode($this->config->get('pp_pro_recurring_signature'));
		
		//new profile fields
		$request .= '&PROFILEID=' . urlencode($profileid);
		
		
		$curl = curl_init('https://api-3t.paypal.com/nvp');
		
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
			$this->log->write('Get Profile Recurring details failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
 
 		$response_data = array();
 
		parse_str($response, $response_data);
		if (($response_data['ACK'] == 'Success') || ($response_data['ACK'] == 'SuccessWithWarning')) {
          
			
		   $data = array(
		'paypal_recurringprofile_id' => isset($response_data['PROFILEID']) ? $response_data['PROFILEID'] : "",
		'paypal_recurring_status' => isset($response_data['STATUS']) ? $response_data['STATUS'] : "");
		
			//UPDATE TRIAL PROFILE WITH CURRENT INFORMATION			
			$this->updateRecurringInfoShort($data,$reason);
		}
	}
	
			  private function updateRecurringProfile($profileid,$trialboolean) {
			
			//CONNECT WITH PAY PAL TO GET RECURRING PROFILE INFORMATION
	    $request  = 'METHOD=' .urlencode("GetRecurringPaymentsProfileDetails");
	    $request .= '&VERSION=' .urlencode("53.0");
	    $request .= '&USER=' . urlencode($this->config->get('pp_pro_recurring_username'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_recurring_password'));
		$request .= '&SIGNATURE=' . urlencode($this->config->get('pp_pro_recurring_signature'));
		
		//new profile fields
		$request .= '&PROFILEID=' . urlencode($profileid);
		
		
		$curl = curl_init('https://api-3t.paypal.com/nvp');
		
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
			$this->log->write('Get Profile Recurring details failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
 
 		$response_data = array();
       
		parse_str($response, $response_data);
		
		if (($response_data['ACK'] == 'Success') || ($response_data['ACK'] == 'SuccessWithWarning')) {
          
			
		   $data = array(
		'paypal_recurringprofile_id' => isset($response_data['PROFILEID']) ? $response_data['PROFILEID'] : "",
		'paypal_recurring_status' => isset($response_data['STATUS']) ? $response_data['STATUS'] : "",
		'paypal_recurring_desc' => isset($response_data['DESC']) ? $response_data['DESC'] : "",
		'paypal_recurring_autobillamount' => isset($response_data['AUTOBILLOUTAMT']) ? $response_data['AUTOBILLOUTAMT'] : "",
		'paypal_recurring_maxfailedpayments' => isset($response_data['MAXFAILEDPAYMENTS']) ? $response_data['MAXFAILEDPAYMENTS'] : "",
		'paypal_recurring_finalpaymentduedate' => isset($response_data['FINALPAYMENTDUEDATE']) ? $response_data['FINALPAYMENTDUEDATE'] : "",
		'paypal_recurring_subscribername' => isset($response_data['SUBSCRIBERNAME']) ? $response_data['SUBSCRIBERNAME'] : "",
		'paypal_recurring_startdate' => isset($response_data['PROFILESTARTDATE']) ? $response_data['PROFILESTARTDATE'] : "",
		'paypal_recurring_reference' => isset($response_data['PROFILEREFERENCE']) ? $response_data['PROFILEREFERENCE'] : "",
		'paypal_recurring_billingperiod' => isset($response_data['BILLINGPERIOD']) ? $response_data['BILLINGPERIOD'] : "",
		'paypal_recurring_regularbillingperiod' => isset($response_data['REGULARBILLINGPERIOD']) ? $response_data['REGULARBILLINGPERIOD'] : "",
		'paypal_recurring_billingfrequency' => isset($response_data['BILLINGFREQUENCY']) ? $response_data['BILLINGFREQUENCY'] : "",
		'paypal_recurring_regularbillingfrequency' => isset($response_data['REGULARBILLINGFREQUENCY']) ? $response_data['REGULARBILLINGFREQUENCY'] : "",
		'paypal_recurring_totalbillingcycles' => isset($response_data['TOTALBILLINGCYCLES']) ? $response_data['TOTALBILLINGCYCLES'] : "",
		'paypal_recurring_regulartotalbillingcycles' => isset($response_data['REGULARTOTALBILLINGCYCLES']) ? $response_data['REGULARTOTALBILLINGCYCLES'] : "",
		'paypal_recurring_amount' => isset($response_data['AMT']) ? $response_data['AMT'] : "",
		'paypal_recurring_regularamount' => isset($response_data['REGULARAMT']) ? $response_data['REGULARAMT'] : "",
		'paypal_recurring_shippingamount' => isset($response_data['SHIPPINGAMT']) ? $response_data['SHIPPINGAMT'] : "",
		'paypal_recurring_regularshippingamount' => isset($response_data['REGULARSHIPPINGAMT']) ? $response_data['REGULARSHIPPINGAMT'] : "",
		'paypal_recurring_taxamount' => isset($response_data['TAXAMT']) ? $response_data['TAXAMT'] : "",
		'paypal_recurring_regulartaxamount' => isset($response_data['REGULARTAXAMT']) ? $response_data['REGULARTAXAMT'] : "",
		'paypal_recurring_nextbilldate' => isset($response_data['NEXTBILLINGDATE']) ? $response_data['NEXTBILLINGDATE'] : "",
		'paypal_recurring_cyclescompleted' => isset($response_data['NUMCYCYLESCOMPLETED']) ? $response_data['NUMCYCYLESCOMPLETED'] : "",
		'paypal_recurring_cyclesremaining' => isset($response_data['NUMCYCLESREMAINING']) ? $response_data['NUMCYCLESREMAINING'] : "",
		'paypal_recurring_outstandingbalance' => isset($response_data['OUTSTANDINGBALANCE']) ? $response_data['OUTSTANDINGBALANCE'] : "",
		'paypal_recurring_lastpaymentdate' => isset($response_data['LASTPAYMENTDATE']) ? $response_data['LASTPAYMENTDATE'] : "",
		'paypal_recurring_lastpaymentamount' => isset($response_data['LASTPAYMENTAMT']) ? $response_data['LASTPAYMENTAMT'] : "",
		'paypal_recurring_cctype' => isset($response_data['CREDITCARDTYPE']) ? $response_data['CREDITCARDTYPE'] : "",
		'paypal_recurring_ccnumber' => isset($response_data['ACCT']) ? $response_data['ACCT'] : "",
		'paypal_recurring_ccexpire' => isset($response_data['EXPDATE']) ? $response_data['EXPDATE'] : "",
		'paypal_recurring_maestrostartdate' => isset($response_data['STARTDATE']) ? $response_data['STARTDATE'] : "",
		'paypal_recurring_maestronumber' => isset($response_data['ISSUENUMBER']) ? $response_data['ISSUENUMBER'] : "");
		
			//UPDATE TRIAL PROFILE WITH CURRENT INFORMATION			
			$this->updateRecurringInfo($data,$trialboolean);
		}
	}
	private function getCustomerId($pid) {
		
				
		$query = $this->db->query("SELECT customer_id FROM `" . DB_PREFIX . "order` WHERE paypal_recurringprofile_id = '" . (string)$pid . "'");
		
		return $query->row;	
	}
	private function getRecurringCustomerGroupId() {
		
				
		$query = $this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` WHERE is_recurring = '1'");
		
		return $query->row;	
	}
	private function updateRecurringInfoShort($data = array(),$reason) {			
		
		
$this->db->query("UPDATE `" . DB_PREFIX . "order` SET paypal_recurringprofile_id = '" . (string)$data['paypal_recurringprofile_id'] . "',paypal_recurring_status = '" . (string)$data['paypal_recurring_status'] . "', paypal_recurring_cancelreason = '" . $this->db->escape($reason) . "', date_modified = NOW() WHERE paypal_recurringprofile_id = '" . (string)$data['paypal_recurringprofile_id'] . "' AND paypal_is_original_order = '1'");
	$customer_id = $this->getCustomerId($data['paypal_recurringprofile_id']);	
		$recurring_group_id = $this->getRecurringCustomerGroupId();	
		if($recurring_group_id){
			$recurring_group_id = $recurring_group_id['customer_group_id'];
		}else{
			$recurring_group_id = $this->config->get('config_customer_group_id');
			
		}
		if($data['paypal_recurring_status'] == "Cancelled"){
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' WHERE customer_id = '" . (string)$customer_id['customer_id'] . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' WHERE customer_id = '" . (string)$customer_id['customer_id'] . "'");
		}else{
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$recurring_group_id . "' WHERE customer_id = '" . (string)$customer_id['customer_id'] . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET customer_group_id = '" . (int)$recurring_group_id . "' WHERE customer_id = '" . (string)$customer_id['customer_id'] . "'");
		}	
}
	private function updateRecurringInfo($data = array(),$trialboolean) {		
$this->db->query("UPDATE `" . DB_PREFIX . "order` SET paypal_recurringprofile_id = '" . (string)$data['paypal_recurringprofile_id'] . "',paypal_recurring_status = '" . (string)$data['paypal_recurring_status'] . "',paypal_recurring_desc = '" . (string)addslashes($data['paypal_recurring_desc']) . "',paypal_recurring_autobillamount = '" . (string)$data['paypal_recurring_autobillamount'] . "',paypal_recurring_maxfailedpayments = '" . (int)$data['paypal_recurring_maxfailedpayments'] . "',paypal_recurring_finalpaymentduedate = '" . (string)$data['paypal_recurring_finalpaymentduedate'] . "',paypal_recurring_subscribername = '" . (string)$data['paypal_recurring_subscribername'] . "',paypal_recurring_startdate = '" . (string)$data['paypal_recurring_startdate'] . "',paypal_recurring_reference = '" . (string)$data['paypal_recurring_reference'] . "',paypal_recurring_billingperiod = '" . (string)$data['paypal_recurring_billingperiod'] . "',paypal_recurring_regularbillingperiod = '" . (string)$data['paypal_recurring_regularbillingperiod'] . "',paypal_recurring_billingfrequency = '" . (int)$data['paypal_recurring_billingfrequency'] . "',paypal_recurring_regularbillingfrequency = '" . (int)$data['paypal_recurring_regularbillingfrequency'] . "',paypal_recurring_totalbillingcycles = '" . (int)$data['paypal_recurring_totalbillingcycles'] . "',paypal_recurring_regulartotalbillingcycles = '" . (int)$data['paypal_recurring_regulartotalbillingcycles'] . "',paypal_recurring_amount = '" . (float)$data['paypal_recurring_amount'] . "',paypal_recurring_regularamount = '" . (float)$data['paypal_recurring_regularamount'] . "',paypal_recurring_shippingamount = '" . (float)$data['paypal_recurring_shippingamount'] . "',paypal_recurring_regularshippingamount = '" . (float)$data['paypal_recurring_regularshippingamount'] . "',paypal_recurring_taxamount = '" . (float)$data['paypal_recurring_taxamount'] . "',paypal_recurring_regulartaxamount = '" . (float)$data['paypal_recurring_regulartaxamount'] . "',paypal_recurring_nextbilldate = '" . (string)$data['paypal_recurring_nextbilldate'] . "',paypal_recurring_cyclescompleted = '" . (int)$data['paypal_recurring_cyclescompleted']. "',paypal_recurring_cyclesremaining = '" . (int)$data['paypal_recurring_cyclesremaining'] . "',paypal_recurring_outstandingbalance = '" . (float)$data['paypal_recurring_outstandingbalance'] . "',paypal_recurring_lastpaymentdate = '" . (string)$data['paypal_recurring_lastpaymentdate'] . "',paypal_recurring_lastpaymentamount = '" . (float)$data['paypal_recurring_lastpaymentamount'] . "',paypal_recurring_cctype = '" . (string)$data['paypal_recurring_cctype'] . "',paypal_recurring_ccnumber = '" . (int)$data['paypal_recurring_ccnumber'] . "',paypal_recurring_ccexpire = '" . (string)$data['paypal_recurring_ccexpire'] . "',paypal_recurring_maestrostartdate = '" . (string)$data['paypal_recurring_maestrostartdate'] . "',paypal_recurring_maestronumber = '" . (int)$data['paypal_recurring_maestronumber'] . "',paypal_recurring_trial_status	 = '" . (int)$trialboolean . "',date_modified = NOW() WHERE paypal_recurringprofile_id = '" . (string)$data['paypal_recurringprofile_id'] . "' AND paypal_is_original_order = '1'");
$this->db->query("UPDATE `" . DB_PREFIX . "order` SET paypal_recurring_trial_status = '" . (int)$trialboolean . "' , date_modified = NOW() WHERE paypal_recurringprofile_id = '" . (string)$data['paypal_recurringprofile_id'] . "'");
 
  $customer_id = $this->getCustomerId($data['paypal_recurringprofile_id']);	
		$recurring_group_id = $this->getRecurringCustomerGroupId();	
		if($data['paypal_recurring_status'] == "Canceled"){
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' WHERE customer_id = '" . (string)$customer_id['customer_id'] . "'");
		}else{
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_group_id = '" . (int)$recurring_group_id['customer_group_id'] . "' WHERE customer_id = '" . (string)$customer_id['customer_id'] . "'");
		}	
	}
			public function updateRecurringOrder(){
			$json = array();
			$this->load->model('sale/recurring_order');
			
		$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);			
		$request  = 'METHOD=UpdateRecurringPaymentsProfile';
	    $request .= '&VERSION=' .urlencode("53.0");
	    $request .= '&USER=' . urlencode($this->config->get('pp_pro_recurring_username'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_recurring_password'));
		$request .= '&SIGNATURE=' . urlencode($this->config->get('pp_pro_recurring_signature'));
		
		//new profile fields
		$request .= '&PROFILEID=' . urlencode($this->request->get['profile_id']);			
		
		if($this->request->post['update_recurring_note'] != ""){
		$request .= '&NOTE=' . urlencode($this->request->post['update_recurring_note']);
		}
		if($this->request->post['update_recurring_desc'] != ""){
		$request .= '&DESC=' . urlencode($this->request->post['update_recurring_desc']);
		}
		if($this->request->post['update_recurring_subscribename'] != ""){
		$request .= '&SUBSCRIBERNAME=' . urlencode($this->request->post['update_recurring_subscribename']);
		}
		if($this->request->post['update_recurring_reference'] != ""){
		$request .= '&PROFILEREFERENCE=' . urlencode($this->request->post['update_recurring_reference']);
		}
		if($this->request->post['update_recurring_email'] != ""){
		$request .= '&EMAIL=' . urlencode($this->request->post['update_recurring_email']);
		}
		if($this->request->post['update_recurring_additionalbillingcycles'] != ""){
		$request .= '&ADDITIONALBILLINGCYCLES=' . urlencode($this->request->post['update_recurring_additionalbillingcycles']);
		}
		if($this->request->post['update_recurring_shippingamt'] != ""){
		$request .= '&SHIPPINGAMT=' . urlencode($this->request->post['update_recurring_shippingamt']);
		}
		if($this->request->post['update_recurring_taxamt'] != ""){
		$request .= '&TAXAMT=' . urlencode($this->request->post['update_recurring_taxamt']);
		}		
		if($this->request->post['update_recurring_outstanding'] != ""){
		$request .= '&OUTSTANDINGAMT=' . urlencode($this->request->post['update_recurring_outstanding']);
		}
		if($this->request->post['update_recurring_amt'] != "" && $this->request->post['update_recurring_amt'] != "N/A"){
		$request .= '&TRIALAMT=' . urlencode($this->request->post['update_recurring_amt']);
		}
		if($this->request->post['update_recurring_regularamt'] != ""){
		$request .= '&AMT=' . urlencode($this->request->post['update_recurring_regularamt']);
		}		
		if($this->request->post['update_recurring_maxfailed'] != ""){
		$request .= '&MAXFAILEDPAYMENTS=' . urlencode($this->request->post['update_recurring_maxfailed']);
		}
		if($this->request->post['update_recurring_startdate'] != ""){
		
		$request .= '&PROFILESTARTDATE=' . urlencode(date($this->request->post['update_recurring_startdate']. ' H:i:s'));
		}
		if($this->request->post['update_recurring_regulartotalbillingcycles'] != "" && $this->request->post['update_recurring_regulartotalbillingcycles'] != "Infinite"){
		$request .= '&TOTALBILLINGCYCLES=' . urlencode($this->request->post['update_recurring_regulartotalbillingcycles']);
		}
		if($this->request->post['update_recurring_totalbillingcycles'] != "" && $this->request->post['update_recurring_totalbillingcycles'] != "N/A" && $this->request->post['update_recurring_totalbillingcycles'] != "Infinite"){
		$request .= '&TRIALTOTALBILLINGCYCLES=' . urlencode($this->request->post['update_recurring_totalbillingcycles']);
		}
		if($this->request->post['update_recurring_cc_type'] != ""){
		$request .= '&CREDITCARDTYPE=' . urlencode(str_replace(' ', '', $this->request->post['update_recurring_cc_type']));
		}
		if($this->request->post['update_recurring_cc_number'] != ""){
		$request .= '&ACCT=' . urlencode(str_replace(' ', '', $this->request->post['update_recurring_cc_number']));
		}
		if($this->request->post['update_recurring_cc_expire_date_month'] && $this->request->post['update_recurring_cc_expire_date_year'] != ""){
	    $request .= '&EXPDATE=' . urlencode($this->request->post['update_recurring_cc_expire_date_month'] . $this->request->post['update_recurring_cc_expire_date_year']);
		}
		if($this->request->post['update_recurring_cc_cvv2'] != ""){
		$request .= '&CVV2=' . urlencode($this->request->post['update_recurring_cc_cvv2']);
		}
		if($this->request->post['update_recurring_cc_start_date_month'] && $this->request->post['update_recurring_cc_start_date_year'] != ""){
	    $request .= '&STARTDATE=' . urlencode($this->request->post['update_recurring_cc_start_date_month'] . $this->request->post['update_recurring_cc_start_date_month']);
		}
		if ($this->request->post['update_recurring_cc_type'] == 'SWITCH' || $this->request->post['update_recurring_cc_type'] == 'SOLO') { 
			$request .= '&ISSUENUMBER=' . urlencode($this->request->post['update_recurring_cc_issue']);
		}
		$request .= '&CURRENCYCODE=' . urlencode($order_info['currency_code']);
		
		$curl = curl_init('https://api-3t.paypal.com/nvp');
		
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
			$this->log->write('Get Profile Recurring details failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
 
 		$response_data = array();
 
		parse_str($response, $response_data);
		if (($response_data['ACK'] == 'Success') || ($response_data['ACK'] == 'SuccessWithWarning')) {
			
			if (isset($response_data['PROFILEID'])) {			
			//GET NEW UPDATED PROFILE DETAILS AND UPDATE DATABSE
			
			$this->updateRecurringProfile($this->request->get['profile_id'],$order_info['paypal_recurring_trial_status']);
			//
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('sale/recurring_order/info', 'order_id=' .$order_info['order_id'].'&token='.$this->session->data['token'].'&rec=true'));
			$this->session->data['success'] = "Sucess: You have edited/altered the recurring profile!";
			}else{
			
			$json['error'] = "There was an error processing your request. Please check documentation to make sure you have all fields filled correctly.";
			}
		} else {
			
        	$json['error'] = $response_data['L_LONGMESSAGE0'];
        }
			
		
			
			
			$this->response->setOutput(json_encode($json));
			
			}
  	public function index() {
		
		$this->load->language('sale/recurring_order');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('sale/recurring_order');
    	$this->getList();
  	}
	
  	public function insert() {
		$this->load->language('sale/recurring_order');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('sale/recurring_order');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      	  	$this->model_sale_recurring_order->addOrder($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
		  
			$url = '';
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}
						
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}
													
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		
    	$this->getForm();
  	}
	
  	public function update() {
		$this->load->language('sale/recurring_order');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('sale/recurring_order');
    	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_recurring_order->editOrder($this->request->get['order_id'], $this->request->post);
	  		
			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = '';
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}
						
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}
													
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		
    	$this->getForm();
  	}
	
  	public function delete() {
		$this->load->language('sale/recurring_order');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('sale/recurring_order');
    	if (isset($this->request->post['selected']) && ($this->validateDelete())) {
			foreach ($this->request->post['selected'] as $order_id) {
				$this->model_sale_recurring_order->deleteOrder($order_id);
			}
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}
						
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}
													
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->redirect($this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
    	$this->getList();
  	}
  	private function getList() {
		$this->data['hastrial'] = $this->language->get('hastrial');
		$this->data['isrecurring'] = $this->language->get('isrecurring');
		
		if (isset($this->request->get['filter_profile_id'])) {
			$filter_profile_id = $this->request->get['filter_profile_id'];
		} else {
			$filter_profile_id = null;
		}
		
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = null;
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}
		
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$url = '';
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_profile_id'])) {
			$url .= '&filter_profile_id=' . $this->request->get['filter_profile_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
											
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
  		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		$this->data['invoice'] = $this->url->link('sale/recurring_order/invoice', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['insert'] = $this->url->link('sale/recurring_order/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('sale/recurring_order/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['orders'] = array();
		$data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_profile_id'        => $filter_profile_id,
			'filter_customer'	     => $filter_customer,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		$order_total = $this->model_sale_recurring_order->getTotalOrders($data);
		$results = $this->model_sale_recurring_order->getOrders($data);
		$counter = 0;
		foreach ($results as $result) {
			  
			  $parentresults = $this->model_sale_recurring_order->getParentOrders($result['paypal_recurringprofile_id']);
			$action = array();
		     $parent_orders = array();		
			$action[] = array(
				'text' => 'Manage Profile',
				'href' => $this->url->link('sale/recurring_order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
			);
			
		//gather aggregate total for this profile_ ID
		    $total_gross = $this->model_sale_recurring_order->getTotalGross($result['paypal_recurringprofile_id']);
			
				foreach ($parentresults as $presult) {
			$product_total = $this->model_sale_recurring_order->getTotalOrderProductsByOrderId($presult['order_id']);
			$voucher_total = $this->model_sale_recurring_order->getTotalOrderVouchersByOrderId($presult['order_id']);
			$parent_orders[] = array(
				'profile_id'      => $presult['paypal_recurringprofile_id'],
				'order_id'      => $presult['order_id'],
				'customer'      => $presult['customer'],
				'paypal_transaction_id'        => $presult['paypal_transaction_id'],
				'email'        => $presult['email'],
				'total_gross'         => $this->currency->format($presult['paypal_recurring_lastpaymentamount'], $presult['currency_code'], $presult['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($presult['date_added'])),
				'manage_child' =>  $this->url->link('sale/recurring_order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $presult['order_id'] . $url, 'SSL'),
				'products'   => ($product_total + $voucher_total)
				
			);
			
	}
			$this->data['orders'][] = array(
			    'parent_orders'      => $result['paypal_recurring_count'],
				'profile_id'      => $result['paypal_recurringprofile_id'],
			    'is_trial'      => $result['paypal_recurring_trial_status'] == 1  ? 'Yes' : 'No',
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'status'        => $result['status'],
				'total_gross'         => $this->currency->format($total_gross, $result['currency_code'], $result['currency_value']),
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'selected'      => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
				'action'        => $action,
				'manage' =>  $this->url->link('sale/recurring_order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
				'parent_orders_list'        => $parent_orders
			);
			$counter ++;
		}
        
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['column_parent_orders'] = $this->language->get('column_parent_orders');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_missing'] = $this->language->get('text_missing');
        $this->data['column_profile_id'] = $this->language->get('column_profile_id');
		$this->data['column_order_id'] = $this->language->get('column_order_id');
    	$this->data['column_customer'] = $this->language->get('column_customer');
		$this->data['column_status'] = $this->language->get('column_status');
		
		$this->data['column_recurring_profile_aggtotal'] = "Aggregate Total";
		$this->data['column_total'] = $this->language->get('column_total');
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_date_modified'] = $this->language->get('column_date_modified');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['button_invoice'] = $this->language->get('button_invoice');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');
		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		$url = '';
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		if (isset($this->request->get['filter_profile_id'])) {
			$url .= '&filter_profile_id=' . $this->request->get['filter_profile_id'];
		}
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
											
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$this->data['sort_order'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$this->data['sort_customer'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$this->data['sort_total'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$this->data['sort_date_modified'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');
		$url = '';
		if (isset($this->request->get['filter_profile_id'])) {
			$url .= '&filter_profile_id=' . $this->request->get['filter_profile_id'];
		}
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
											
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();
		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_profile_id'] = $filter_profile_id;
		$this->data['filter_customer'] = $filter_customer;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		$this->data['filter_total'] = $filter_total;
		$this->data['filter_date_added'] = $filter_date_added;
		$this->data['filter_date_modified'] = $filter_date_modified;
		$this->load->model('localisation/order_status');
    	$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->template = 'sale/recurring_order_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}
  	public function getForm() {
		$this->load->model('sale/customer');
				
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}
 		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}
		
 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
 		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}
						
 		if (isset($this->error['payment_firstname'])) {
			$this->data['error_payment_firstname'] = $this->error['payment_firstname'];
		} else {
			$this->data['error_payment_firstname'] = '';
		}
 		if (isset($this->error['payment_lastname'])) {
			$this->data['error_payment_lastname'] = $this->error['payment_lastname'];
		} else {
			$this->data['error_payment_lastname'] = '';
		}
				
		if (isset($this->error['payment_address_1'])) {
			$this->data['error_payment_address_1'] = $this->error['payment_address_1'];
		} else {
			$this->data['error_payment_address_1'] = '';
		}
		
		if (isset($this->error['payment_city'])) {
			$this->data['error_payment_city'] = $this->error['payment_city'];
		} else {
			$this->data['error_payment_city'] = '';
		}
		
		if (isset($this->error['payment_postcode'])) {
			$this->data['error_payment_postcode'] = $this->error['payment_postcode'];
		} else {
			$this->data['error_payment_postcode'] = '';
		}
		
		if (isset($this->error['payment_tax_id'])) {
			$this->data['error_payment_tax_id'] = $this->error['payment_tax_id'];
		} else {
			$this->data['error_payment_tax_id'] = '';
		}
				
		if (isset($this->error['payment_country'])) {
			$this->data['error_payment_country'] = $this->error['payment_country'];
		} else {
			$this->data['error_payment_country'] = '';
		}
		
		if (isset($this->error['payment_zone'])) {
			$this->data['error_payment_zone'] = $this->error['payment_zone'];
		} else {
			$this->data['error_payment_zone'] = '';
		}
		
		if (isset($this->error['payment_method'])) {
			$this->data['error_payment_method'] = $this->error['payment_method'];
		} else {
			$this->data['error_payment_method'] = '';
		}
 		if (isset($this->error['shipping_firstname'])) {
			$this->data['error_shipping_firstname'] = $this->error['shipping_firstname'];
		} else {
			$this->data['error_shipping_firstname'] = '';
		}
 		if (isset($this->error['shipping_lastname'])) {
			$this->data['error_shipping_lastname'] = $this->error['shipping_lastname'];
		} else {
			$this->data['error_shipping_lastname'] = '';
		}
				
		if (isset($this->error['shipping_address_1'])) {
			$this->data['error_shipping_address_1'] = $this->error['shipping_address_1'];
		} else {
			$this->data['error_shipping_address_1'] = '';
		}
		
		if (isset($this->error['shipping_city'])) {
			$this->data['error_shipping_city'] = $this->error['shipping_city'];
		} else {
			$this->data['error_shipping_city'] = '';
		}
		
		if (isset($this->error['shipping_postcode'])) {
			$this->data['error_shipping_postcode'] = $this->error['shipping_postcode'];
		} else {
			$this->data['error_shipping_postcode'] = '';
		}
		
		if (isset($this->error['shipping_country'])) {
			$this->data['error_shipping_country'] = $this->error['shipping_country'];
		} else {
			$this->data['error_shipping_country'] = '';
		}
		
		if (isset($this->error['shipping_zone'])) {
			$this->data['error_shipping_zone'] = $this->error['shipping_zone'];
		} else {
			$this->data['error_shipping_zone'] = '';
		}
		
		if (isset($this->error['shipping_method'])) {
			$this->data['error_shipping_method'] = $this->error['shipping_method'];
		} else {
			$this->data['error_shipping_method'] = '';
		}
								
		$url = '';
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
											
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL'),				
			'separator' => ' :: '
		);
		if (!isset($this->request->get['order_id'])) {
			$this->data['action'] = $this->url->link('sale/recurring_order/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('sale/recurring_order/update', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL');
    	if (isset($this->request->get['order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);
    	}
		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->request->get['order_id'])) {
			$this->data['order_id'] = $this->request->get['order_id'];
		} else {
			$this->data['order_id'] = 0;
		}
		 
			
						
    	if (isset($this->request->post['store_id'])) {
      		$this->data['store_id'] = $this->request->post['store_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['store_id'] = $order_info['store_id'];
		} else {
      		$this->data['store_id'] = '';
    	}
		
		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['store_url'] = HTTPS_CATALOG;
		} else {
			$this->data['store_url'] = HTTP_CATALOG;
		}
		
		if (isset($this->request->post['customer'])) {
			$this->data['customer'] = $this->request->post['customer'];
		} elseif (!empty($order_info)) {
			$this->data['customer'] = $order_info['customer'];
		} else {
			$this->data['customer'] = '';
		}
						
		if (isset($this->request->post['customer_id'])) {
			$this->data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($order_info)) {
			$this->data['customer_id'] = $order_info['customer_id'];
		} else {
			$this->data['customer_id'] = '';
		}
		
		if (isset($this->request->post['customer_group_id'])) {
			$this->data['customer_group_id'] = $this->request->post['customer_group_id'];
		} elseif (!empty($order_info)) {
			$this->data['customer_group_id'] = $order_info['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = '';
		}
		
		$this->load->model('sale/customer_group');
		
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
								
    	if (isset($this->request->post['firstname'])) {
      		$this->data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($order_info)) { 
			$this->data['firstname'] = $order_info['firstname'];
		} else {
      		$this->data['firstname'] = '';
    	}
    	if (isset($this->request->post['lastname'])) {
      		$this->data['lastname'] = $this->request->post['lastname'];
    	} elseif (!empty($order_info)) { 
			$this->data['lastname'] = $order_info['lastname'];
		} else {
      		$this->data['lastname'] = '';
    	}
    	if (isset($this->request->post['email'])) {
      		$this->data['email'] = $this->request->post['email'];
    	} elseif (!empty($order_info)) { 
			$this->data['email'] = $order_info['email'];
		} else {
      		$this->data['email'] = '';
    	}
				
    	if (isset($this->request->post['telephone'])) {
      		$this->data['telephone'] = $this->request->post['telephone'];
    	} elseif (!empty($order_info)) { 
			$this->data['telephone'] = $order_info['telephone'];
		} else {
      		$this->data['telephone'] = '';
    	}
		
    	if (isset($this->request->post['fax'])) {
      		$this->data['fax'] = $this->request->post['fax'];
    	} elseif (!empty($order_info)) { 
			$this->data['fax'] = $order_info['fax'];
		} else {
      		$this->data['fax'] = '';
    	}	
		
		if (isset($this->request->post['affiliate_id'])) {
      		$this->data['affiliate_id'] = $this->request->post['affiliate_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['affiliate_id'] = $order_info['affiliate_id'];
		} else {
      		$this->data['affiliate_id'] = '';
    	}
		
		if (isset($this->request->post['affiliate'])) {
      		$this->data['affiliate'] = $this->request->post['affiliate'];
    	} elseif (!empty($order_info)) { 
			$this->data['affiliate'] = ($order_info['affiliate_id'] ? $order_info['affiliate_firstname'] . ' ' . $order_info['affiliate_lastname'] : '');
		} else {
      		$this->data['affiliate'] = '';
    	}
				
		if (isset($this->request->post['order_status_id'])) {
      		$this->data['order_status_id'] = $this->request->post['order_status_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['order_status_id'] = $order_info['order_status_id'];
		} else {
      		$this->data['order_status_id'] = '';
    	}
			
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();	
			
    	if (isset($this->request->post['comment'])) {
      		$this->data['comment'] = $this->request->post['comment'];
    	} elseif (!empty($order_info)) { 
			$this->data['comment'] = $order_info['comment'];
		} else {
      		$this->data['comment'] = '';
    	}	
		
		$this->load->model('sale/customer');
		if (isset($this->request->post['customer_id'])) {
			$this->data['addresses'] = $this->model_sale_customer->getAddresses($this->request->post['customer_id']);
		} elseif (!empty($order_info)) {
			$this->data['addresses'] = $this->model_sale_customer->getAddresses($order_info['customer_id']);
		} else {
			$this->data['addresses'] = array();
		}
			
    	if (isset($this->request->post['payment_firstname'])) {
      		$this->data['payment_firstname'] = $this->request->post['payment_firstname'];
		} elseif (!empty($order_info)) { 
			$this->data['payment_firstname'] = $order_info['payment_firstname'];
		} else {
      		$this->data['payment_firstname'] = '';
    	}
    	if (isset($this->request->post['payment_lastname'])) {
      		$this->data['payment_lastname'] = $this->request->post['payment_lastname'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_lastname'] = $order_info['payment_lastname'];
		} else {
      		$this->data['payment_lastname'] = '';
    	}
    	if (isset($this->request->post['payment_company'])) {
      		$this->data['payment_company'] = $this->request->post['payment_company'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_company'] = $order_info['payment_company'];
		} else {
      		$this->data['payment_company'] = '';
    	}
		
    	if (isset($this->request->post['payment_company_id'])) {
      		$this->data['payment_company_id'] = $this->request->post['payment_company_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_company_id'] = $order_info['payment_company_id'];
		} else {
      		$this->data['payment_company_id'] = '';
    	}
		
    	if (isset($this->request->post['payment_tax_id'])) {
      		$this->data['payment_tax_id'] = $this->request->post['payment_tax_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_tax_id'] = $order_info['payment_tax_id'];
		} else {
      		$this->data['payment_tax_id'] = '';
    	}
				
    	if (isset($this->request->post['payment_address_1'])) {
      		$this->data['payment_address_1'] = $this->request->post['payment_address_1'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_address_1'] = $order_info['payment_address_1'];
		} else {
      		$this->data['payment_address_1'] = '';
    	}
    	if (isset($this->request->post['payment_address_2'])) {
      		$this->data['payment_address_2'] = $this->request->post['payment_address_2'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_address_2'] = $order_info['payment_address_2'];
		} else {
      		$this->data['payment_address_2'] = '';
    	}
		
    	if (isset($this->request->post['payment_city'])) {
      		$this->data['payment_city'] = $this->request->post['payment_city'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_city'] = $order_info['payment_city'];
		} else {
      		$this->data['payment_city'] = '';
    	}
    	if (isset($this->request->post['payment_postcode'])) {
      		$this->data['payment_postcode'] = $this->request->post['payment_postcode'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_postcode'] = $order_info['payment_postcode'];
		} else {
      		$this->data['payment_postcode'] = '';
    	}
				
    	if (isset($this->request->post['payment_country_id'])) {
      		$this->data['payment_country_id'] = $this->request->post['payment_country_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_country_id'] = $order_info['payment_country_id'];
		} else {
      		$this->data['payment_country_id'] = '';
    	}		
	    
		if (isset($this->request->post['payment_zone_id'])) {
      		$this->data['payment_zone_id'] = $this->request->post['payment_zone_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_zone_id'] = $order_info['payment_zone_id'];
		} else {
      		$this->data['payment_zone_id'] = '';
    	}
						
    	if (isset($this->request->post['payment_method'])) {
      		$this->data['payment_method'] = $this->request->post['payment_method'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_method'] = $order_info['payment_method'];
		} else {
      		$this->data['payment_method'] = '';
    	}
		
    	if (isset($this->request->post['payment_code'])) {
      		$this->data['payment_code'] = $this->request->post['payment_code'];
    	} elseif (!empty($order_info)) { 
			$this->data['payment_code'] = $order_info['payment_code'];
		} else {
      		$this->data['payment_code'] = '';
    	}			
			
    	if (isset($this->request->post['shipping_firstname'])) {
      		$this->data['shipping_firstname'] = $this->request->post['shipping_firstname'];
		} elseif (!empty($order_info)) { 
			$this->data['shipping_firstname'] = $order_info['shipping_firstname'];
		} else {
      		$this->data['shipping_firstname'] = '';
    	}
    	if (isset($this->request->post['shipping_lastname'])) {
      		$this->data['shipping_lastname'] = $this->request->post['shipping_lastname'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_lastname'] = $order_info['shipping_lastname'];
		} else {
      		$this->data['shipping_lastname'] = '';
    	}
    	if (isset($this->request->post['shipping_company'])) {
      		$this->data['shipping_company'] = $this->request->post['shipping_company'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_company'] = $order_info['shipping_company'];
		} else {
      		$this->data['shipping_company'] = '';
    	}
    	if (isset($this->request->post['shipping_address_1'])) {
      		$this->data['shipping_address_1'] = $this->request->post['shipping_address_1'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_address_1'] = $order_info['shipping_address_1'];
		} else {
      		$this->data['shipping_address_1'] = '';
    	}
    	if (isset($this->request->post['shipping_address_2'])) {
      		$this->data['shipping_address_2'] = $this->request->post['shipping_address_2'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_address_2'] = $order_info['shipping_address_2'];
		} else {
      		$this->data['shipping_address_2'] = '';
    	}
		
    	if (isset($this->request->post['shipping_city'])) {
      		$this->data['shipping_city'] = $this->request->post['shipping_city'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_city'] = $order_info['shipping_city'];
		} else {
      		$this->data['shipping_city'] = '';
    	}
		
    	if (isset($this->request->post['shipping_postcode'])) {
      		$this->data['shipping_postcode'] = $this->request->post['shipping_postcode'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_postcode'] = $order_info['shipping_postcode'];
		} else {
      		$this->data['shipping_postcode'] = '';
    	}
				
    	if (isset($this->request->post['shipping_country_id'])) {
      		$this->data['shipping_country_id'] = $this->request->post['shipping_country_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_country_id'] = $order_info['shipping_country_id'];
		} else {
      		$this->data['shipping_country_id'] = '';
    	}		
	    
		if (isset($this->request->post['shipping_zone_id'])) {
      		$this->data['shipping_zone_id'] = $this->request->post['shipping_zone_id'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_zone_id'] = $order_info['shipping_zone_id'];
		} else {
      		$this->data['shipping_zone_id'] = '';
    	}	
						
		$this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();															
		
    	if (isset($this->request->post['shipping_method'])) {
      		$this->data['shipping_method'] = $this->request->post['shipping_method'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_method'] = $order_info['shipping_method'];
		} else {
      		$this->data['shipping_method'] = '';
    	}	
		
    	if (isset($this->request->post['shipping_code'])) {
      		$this->data['shipping_code'] = $this->request->post['shipping_code'];
    	} elseif (!empty($order_info)) { 
			$this->data['shipping_code'] = $order_info['shipping_code'];
		} else {
      		$this->data['shipping_code'] = '';
    	}
		if (isset($this->request->post['order_product'])) {
			$order_products = $this->request->post['order_product'];
		} elseif (isset($this->request->get['order_id'])) {
			$order_products = $this->model_sale_recurring_order->getOrderProducts($this->request->get['order_id']);			
		} else {
			$order_products = array();
		}
		
		$this->load->model('catalog/product');
		
		$this->document->addScript('view/javascript/jquery/ajaxupload.js');
		
		$this->data['order_products'] = array();		
		
		foreach ($order_products as $order_product) {
			if (isset($order_product['order_option'])) {
				$order_option = $order_product['order_option'];
			} elseif (isset($this->request->get['order_id'])) {
				$order_option = $this->model_sale_recurring_order->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);
			} else {
				$order_option = array();
			}
			if (isset($order_product['order_download'])) {
				$order_download = $order_product['order_download'];
			} elseif (isset($this->request->get['order_id'])) {
				$order_download = $this->model_sale_recurring_order->getOrderDownloads($this->request->get['order_id'], $order_product['order_product_id']);
			} else {
				$order_download = array();
			}
											
			$this->data['order_products'][] = array(
				'order_product_id' => $order_product['order_product_id'],
				'product_id'       => $order_product['product_id'],
				'name'             => $order_product['name'],
				'model'            => $order_product['model'],
				'option'           => $order_option,
				'download'         => $order_download,
				'quantity'         => $order_product['quantity'],
				'price'            => $order_product['price'],
				'total'            => $order_product['total'],
				'tax'              => $order_product['tax'],
				'reward'           => $order_product['reward']
			);
		}
		
		if (isset($this->request->post['order_voucher'])) {
			$this->data['order_vouchers'] = $this->request->post['order_voucher'];
		} elseif (isset($this->request->get['order_id'])) {
			$this->data['order_vouchers'] = $this->model_sale_recurring_order->getOrderVouchers($this->request->get['order_id']);			
		} else {
			$this->data['order_vouchers'] = array();
		}
       
		$this->load->model('sale/voucher_theme');
					
		$this->data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();
						
		if (isset($this->request->post['order_total'])) {
      		$this->data['order_totals'] = $this->request->post['order_total'];
    	} elseif (isset($this->request->get['order_id'])) { 
			$this->data['order_totals'] = $this->model_sale_recurring_order->getOrderTotals($this->request->get['order_id']);
		} else {
      		$this->data['order_totals'] = array();
    	}	
		
		$this->template = 'sale/recurring_order_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}
	
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
    	if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
    	}
    	if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
      		$this->error['lastname'] = $this->language->get('error_lastname');
    	}
    	if ((utf8_strlen($this->request->post['email']) > 96) || (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email']))) {
      		$this->error['email'] = $this->language->get('error_email');
    	}
		
    	if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
    	}
		
    	if ((utf8_strlen($this->request->post['payment_firstname']) < 1) || (utf8_strlen($this->request->post['payment_firstname']) > 32)) {
      		$this->error['payment_firstname'] = $this->language->get('error_firstname');
    	}
    	if ((utf8_strlen($this->request->post['payment_lastname']) < 1) || (utf8_strlen($this->request->post['payment_lastname']) > 32)) {
      		$this->error['payment_lastname'] = $this->language->get('error_lastname');
    	}
    	if ((utf8_strlen($this->request->post['payment_address_1']) < 3) || (utf8_strlen($this->request->post['payment_address_1']) > 128)) {
      		$this->error['payment_address_1'] = $this->language->get('error_address_1');
    	}
    	if ((utf8_strlen($this->request->post['payment_city']) < 3) || (utf8_strlen($this->request->post['payment_city']) > 128)) {
      		$this->error['payment_city'] = $this->language->get('error_city');
    	}
		
		$this->load->model('localisation/country');
		
		$country_info = $this->model_localisation_country->getCountry($this->request->post['payment_country_id']);
		
		if ($country_info) {
			if ($country_info['postcode_required'] && (utf8_strlen($this->request->post['payment_postcode']) < 2) || (utf8_strlen($this->request->post['payment_postcode']) > 10)) {
				$this->error['payment_postcode'] = $this->language->get('error_postcode');
			}
			
			// VAT Validation
			$this->load->helper('vat');
			
			if ($this->config->get('config_vat') && $this->request->post['payment_tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['payment_tax_id']) != 'invalid')) {
				$this->error['payment_tax_id'] = $this->language->get('error_vat');
			}				
		}
    	if ($this->request->post['payment_country_id'] == '') {
      		$this->error['payment_country'] = $this->language->get('error_country');
    	}
		
    	if ($this->request->post['payment_zone_id'] == '') {
      		$this->error['payment_zone'] = $this->language->get('error_zone');
    	}	
		
    	if ($this->request->post['payment_method'] == '') {
      		$this->error['payment_zone'] = $this->language->get('error_zone');
    	}			
		
		if (!$this->request->post['payment_method']) {
			$this->error['payment_method'] = $this->language->get('error_payment');
		}	
					
		// Check if any products require shipping
		$shipping = false;
		
		if (isset($this->request->post['order_product'])) {
			$this->load->model('catalog/product');
			
			foreach ($this->request->post['order_product'] as $order_product) {
				$product_info = $this->model_catalog_product->getProduct($order_product['product_id']);
			
				if ($product_info && $product_info['shipping']) {
					$shipping = true;
				}
			}
		}
		
		if ($shipping) {
			if ((utf8_strlen($this->request->post['shipping_firstname']) < 1) || (utf8_strlen($this->request->post['shipping_firstname']) > 32)) {
				$this->error['shipping_firstname'] = $this->language->get('error_firstname');
			}
	
			if ((utf8_strlen($this->request->post['shipping_lastname']) < 1) || (utf8_strlen($this->request->post['shipping_lastname']) > 32)) {
				$this->error['shipping_lastname'] = $this->language->get('error_lastname');
			}
			
			if ((utf8_strlen($this->request->post['shipping_address_1']) < 3) || (utf8_strlen($this->request->post['shipping_address_1']) > 128)) {
				$this->error['shipping_address_1'] = $this->language->get('error_address_1');
			}
	
			if ((utf8_strlen($this->request->post['shipping_city']) < 3) || (utf8_strlen($this->request->post['shipping_city']) > 128)) {
				$this->error['shipping_city'] = $this->language->get('error_city');
			}
	
			$this->load->model('localisation/country');
			
			$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);
			
			if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['shipping_postcode']) < 2) || (utf8_strlen($this->request->post['shipping_postcode']) > 10)) {
				$this->error['shipping_postcode'] = $this->language->get('error_postcode');
			}
	
			if ($this->request->post['shipping_country_id'] == '') {
				$this->error['shipping_country'] = $this->language->get('error_country');
			}
			
			if ($this->request->post['shipping_zone_id'] == '') {
				$this->error['shipping_zone'] = $this->language->get('error_zone');
			}
			
			if (!$this->request->post['shipping_method']) {
				$this->error['shipping_method'] = $this->language->get('error_shipping');
			}			
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}    
	
   	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
    	}
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
	
	public function country() {
		$json = array();
		
		$this->load->model('localisation/country');
    	$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
		
		if ($country_info) {
			$this->load->model('localisation/zone');
			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']		
			);
		}
		
		$this->response->setOutput(json_encode($json));
	}
		
	public function info() {
		 if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		$this->load->model('sale/recurring_order');
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}
		///Get Saved Refund Reasons
		$query = $this->db->query("SELECT reason FROM `" . DB_PREFIX . "paypal_recurring_refund_reasons` ORDER BY id ASC");
		
		$this->data['therefundreasons'] = array();	
		foreach ($query->rows as $reason) {
		$this->data['therefundreasons'][] = array('reason' => stripslashes($reason['reason']));
		}
		$order_info = $this->model_sale_recurring_order->getOrder($order_id);
		if ($order_info) {
			 $this->data['tid'] = $order_info['paypal_transaction_id'];
		$this->data = array_merge($this->data, $this->load->language('sale/recurring_order'));
			$this->document->setTitle($this->language->get('heading_title'));
			
		
			$this->data['token'] = $this->session->data['token'];
			$url = '';
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}
						
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->data['breadcrumbs'] = array();
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
			);
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL'),				
				'separator' => ' :: '
			);
			$this->data['invoice'] = $this->url->link('sale/recurring_order/invoice', 'token=' . $this->session->data['token'] . '&order_id=' . (int)$this->request->get['order_id'], 'SSL');
			$this->data['cancel'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$this->data['order_id'] = $this->request->get['order_id'];
			
			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}
			
			$this->data['store_name'] = $order_info['store_name'];
			$this->data['store_url'] = $order_info['store_url'];
			$this->data['firstname'] = $order_info['firstname'];
			$this->data['lastname'] = $order_info['lastname'];
						
			if ($order_info['customer_id']) {
				$this->data['customer'] = $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $order_info['customer_id'], 'SSL');
			} else {
				$this->data['customer'] = '';
			}
			$this->load->model('sale/customer_group');
			$customer_group_info = $this->model_sale_customer_group->getCustomerGroup($order_info['customer_group_id']);
			if ($customer_group_info) {
				$this->data['customer_group'] = $customer_group_info['name'];
			} else {
				$this->data['customer_group'] = '';
			}
			$this->data['email'] = $order_info['email'];
			$this->data['telephone'] = $order_info['telephone'];
			$this->data['fax'] = $order_info['fax'];
			$this->data['comment'] = nl2br($order_info['comment']);
			$this->data['shipping_method'] = $order_info['shipping_method'];
			$this->data['payment_method'] = $order_info['payment_method'];
			$this->data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);
			
			if ($order_info['total'] < 0) {
				$this->data['credit'] = $order_info['total'];
			} else {
				$this->data['credit'] = 0;
			}
			
			$this->load->model('sale/customer');
						
			$this->data['credit_total'] = $this->model_sale_customer->getTotalTransactionsByOrderId($this->request->get['order_id']); 
			
			$this->data['reward'] = $order_info['reward'];
						
			$this->data['reward_total'] = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($this->request->get['order_id']);
			$this->data['affiliate_firstname'] = $order_info['affiliate_firstname'];
			$this->data['affiliate_lastname'] = $order_info['affiliate_lastname'];
			
			if ($order_info['affiliate_id']) {
				$this->data['affiliate'] = $this->url->link('sale/affiliate/update', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $order_info['affiliate_id'], 'SSL');
			} else {
				$this->data['affiliate'] = '';
			}
			
			$this->data['commission'] = $this->currency->format($order_info['commission'], $order_info['currency_code'], $order_info['currency_value']);
						
			$this->load->model('sale/affiliate');
			
			$this->data['commission_total'] = $this->model_sale_affiliate->getTotalTransactionsByOrderId($this->request->get['order_id']); 
			$this->load->model('localisation/order_status');
			$order_status_info = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
			if ($order_status_info) {
				$this->data['order_status'] = $order_status_info['name'];
			} else {
				$this->data['order_status'] = '';
			}
			
			$this->data['ip'] = $order_info['ip'];
			$this->load->model('sale/recurring_order');
			  $this->data['reasons'] = $this->model_sale_recurring_order->getReasons();
			  $getParentstep1 = $this->db->query("SELECT paypal_recurringprofile_id FROM `" . DB_PREFIX . "order` WHERE order_id = '". (int)$this->request->get['order_id'] ."'");
			  $getParentstep2 = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE paypal_recurringprofile_id = '". (string)$getParentstep1->row['paypal_recurringprofile_id'] ."' AND paypal_is_original_order = '1'");
			  $this->data['parentorder'] = $this->url->link('sale/recurring_order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $getParentstep2->row['order_id'] . $url, 'SSL');
			  $this->load->model('localisation/order_status');
			   $this->data['statuses'] = $this->model_localisation_order_status->getOrderStatuses();
			  
			  $this->data['jump_button'] = $this->language->get('jump');
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
			$this->data['recurring'] = $order_info['recurring_check'];
			$this->data['paypal_is_original_order'] = $order_info['paypal_is_original_order'];
			$this->data['paypal_recurring_desc'] = stripslashes($order_info['paypal_recurring_desc']);
			$this->data['paypal_transaction_id'] =  $order_info['paypal_transaction_id'] != "" ? $order_info['paypal_transaction_id'] : "None (No Transaction)";
			$this->data['paypal_recurring_autobillamount'] = $order_info['paypal_recurring_autobillamount'];
			$this->data['paypal_recurring_status'] = $order_info['paypal_recurring_status'];
			$this->data['paypal_recurring_maxfailedpayments'] = $order_info['paypal_recurring_maxfailedpayments'];
			$total_gross = $this->model_sale_recurring_order->getTotalGross($order_info['recurring_check']);
			$this->data['paypal_recurring_aggregatedtotal'] = $this->currency->format($total_gross, $order_info['currency_code'],$order_info['currency_value']);
			$this->data['paypal_recurring_subscribername'] = $order_info['paypal_recurring_subscribername'];
			$this->data['paypal_recurring_reference'] = $order_info['paypal_recurring_reference'];
			$this->data['paypal_recurring_email'] = $order_info['email'];
			
			$this->data['paypal_recurring_startdate'] = date($this->language->get('date_format_short'), strtotime($order_info['paypal_recurring_startdate']));
			
			
			 if($order_info['paypal_recurring_trial_status'] == 1){
			$this->data['paypal_recurring_profile_billingperiod'] = $order_info['paypal_recurring_billingperiod'];
			}else{
			$this->data['paypal_recurring_profile_billingperiod'] = 'N/A';
			}
			$this->data['paypal_recurring_regularbillingperiod'] = $order_info['paypal_recurring_regularbillingperiod'];
			 
			 if($order_info['paypal_recurring_trial_status'] == 1){
			$this->data['paypal_recurring_billingfrequency'] = $order_info['paypal_recurring_billingfrequency'];
			}else{
			$this->data['paypal_recurring_billingfrequency'] = 'N/A';
			}
			$this->data['paypal_recurring_regularbillingfrequency'] = $order_info['paypal_recurring_regularbillingfrequency'];
			
			 if($order_info['paypal_recurring_trial_status'] == 1){
			$this->data['paypal_recurring_shippingamount'] = $this->currency->format($order_info['paypal_recurring_shippingamount'], $order_info['currency_code'],$order_info['currency_value']);
			}else{
			$this->data['paypal_recurring_shippingamount'] = 'N/A';
			}
			$this->data['paypal_recurring_regularshippingamount'] = $this->currency->format($order_info['paypal_recurring_regularshippingamount'], $order_info['currency_code'],$order_info['currency_value']);
			
			 if($order_info['paypal_recurring_trial_status'] == 1){
			$this->data['paypal_recurring_taxamount'] = $this->currency->format($order_info['paypal_recurring_taxamount'], $order_info['currency_code'],$order_info['currency_value']);
			}else{
			$this->data['paypal_recurring_taxamount'] = 'N/A';
			}
			$this->data['paypal_recurring_regulartaxamount'] =  $this->currency->format($order_info['paypal_recurring_regulartaxamount'], $order_info['currency_code'],$order_info['currency_value']);
			
			 if($order_info['paypal_recurring_trial_status'] == 1){
			$this->data['paypal_recurring_totalbillingcycles'] = $order_info['paypal_recurring_totalbillingcycles'] == '0' ? "Infinite" : $order_info['paypal_recurring_totalbillingcycles'];
			}else{
			$this->data['paypal_recurring_totalbillingcycles'] = 'N/A';
			}
			$this->data['paypal_recurring_regulartotalbillingcycles'] = $order_info['paypal_recurring_regulartotalbillingcycles'] == '0' ? "Infinite" : $order_info['paypal_recurring_regulartotalbillingcycles'];
			
			 if($order_info['paypal_recurring_trial_status'] == 1){ 
			 $this->data['paypal_recurring_amount'] = $this->currency->format($order_info['paypal_recurring_amount'], $order_info['currency_code'], $order_info['currency_value']);
			}else{
			$this->data['paypal_recurring_amount'] = 'N/A';
			}
		
		    $this->data['paypal_recurring_regularamount'] = $this->currency->format($order_info['paypal_recurring_regularamount'], $order_info['currency_code'], $order_info['currency_value']);
			$this->data['paypal_recurring_trial_status'] = $order_info['paypal_recurring_trial_status'];
			$this->data['paypal_recurring_nextbilldate'] = date($this->language->get('date_format_short'), strtotime($order_info['paypal_recurring_nextbilldate']));
			$this->data['paypal_recurring_cyclescompleted'] = $order_info['paypal_recurring_cyclescompleted'];
			$this->data['paypal_recurring_cyclesremaining'] = $order_info['paypal_recurring_cyclesremaining'];
			$this->data['paypal_recurring_trial_status'] = $order_info['paypal_recurring_trial_status'];
			$this->data['paypal_recurring_nextbilldate'] = date($this->language->get('date_format_short'), strtotime($order_info['paypal_recurring_nextbilldate']));
			$this->data['paypal_recurring_cyclescompleted'] = $order_info['paypal_recurring_cyclescompleted'];
			$this->data['paypal_recurring_cyclesremaining'] = $order_info['paypal_recurring_cyclesremaining'];
			$this->data['paypal_recurring_outstandingbalance'] = $this->currency->format($order_info['paypal_recurring_outstandingbalance'], $order_info['currency_code'], $order_info['currency_value']);
			$this->data['paypal_recurring_failedpaymentcount'] = $order_info['paypal_recurring_failedpaymentcount'];
			if($order_info['paypal_recurring_lastpaymentdate'] != '0000-00-00 00:00:00'){
			$this->data['paypal_recurring_lastpaymentdate'] = date($this->language->get('date_format_short'), strtotime($order_info['paypal_recurring_lastpaymentdate']));
			}else{
			$this->data['paypal_recurring_lastpaymentdate'] = "Never Ending";
			}
			$this->data['paypal_recurring_lastpaymentamount'] = $this->currency->format($order_info['paypal_recurring_lastpaymentamount'], $order_info['currency_code'], $order_info['currency_value']);
			$this->data['paypal_recurring_cctype'] = $order_info['paypal_recurring_cctype'];
			$this->data['paypal_recurring_ccnumber'] = 'XXXX-XXXX-XXXX-'.$order_info['paypal_recurring_ccnumber'];
			$this->data['paypal_recurring_ccexpire'] = $order_info['paypal_recurring_ccexpire'];
			if($order_info['paypal_recurring_maestrostartdate'] != ""){
			$this->data['paypal_recurring_maestrostartdate'] = $order_info['paypal_recurring_maestrostartdate'];
			}else{
			$this->data['paypal_recurring_maestrostartdate'] = 'N/A';
			}
			if($order_info['paypal_recurring_maestronumber']){
			$this->data['paypal_recurring_maestronumber'] = $order_info['paypal_recurring_maestronumber'];
			}else{
			$this->data['paypal_recurring_maestronumber'] = 'N/A';
			
			}
			$url = '';
			$this->data['jump'] = $this->url->link('payment/pp_pro_recurring', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$this->data['forwarded_ip'] = $order_info['forwarded_ip'];
			$this->data['user_agent'] = $order_info['user_agent'];
			$this->data['accept_language'] = $order_info['accept_language'];
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			$this->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));		
			$this->data['payment_firstname'] = $order_info['payment_firstname'];
			$this->data['payment_lastname'] = $order_info['payment_lastname'];
			$this->data['payment_company'] = $order_info['payment_company'];
			$this->data['payment_company_id'] = $order_info['payment_company_id'];
			$this->data['payment_tax_id'] = $order_info['payment_tax_id'];
			$this->data['payment_address_1'] = $order_info['payment_address_1'];
			$this->data['payment_address_2'] = $order_info['payment_address_2'];
			$this->data['payment_city'] = $order_info['payment_city'];
			$this->data['payment_postcode'] = $order_info['payment_postcode'];
			$this->data['payment_zone'] = $order_info['payment_zone'];
			$this->data['payment_zone_code'] = $order_info['payment_zone_code'];
			$this->data['payment_country'] = $order_info['payment_country'];			
			$this->data['shipping_firstname'] = $order_info['shipping_firstname'];
			$this->data['shipping_lastname'] = $order_info['shipping_lastname'];
			$this->data['shipping_company'] = $order_info['shipping_company'];
			$this->data['shipping_address_1'] = $order_info['shipping_address_1'];
			$this->data['shipping_address_2'] = $order_info['shipping_address_2'];
			$this->data['shipping_city'] = $order_info['shipping_city'];
			$this->data['shipping_postcode'] = $order_info['shipping_postcode'];
			$this->data['shipping_zone'] = $order_info['shipping_zone'];
			$this->data['shipping_zone_code'] = $order_info['shipping_zone_code'];
			$this->data['shipping_country'] = $order_info['shipping_country'];
			$this->data['products'] = array();
			$products = $this->model_sale_recurring_order->getOrderProducts($this->request->get['order_id']);
			foreach ($products as $product) {
				$option_data = array();
				$options = $this->model_sale_recurring_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);
				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $option['value'],
							'type'  => $option['type']
						);
					} else {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.')),
							'type'  => $option['type'],
							'href'  => $this->url->link('sale/recurring_order/download', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&order_option_id=' . $option['order_option_id'], 'SSL')
						);						
					}
				}
                $parentresults = $this->model_sale_recurring_order->getParentOrders($product['paypal_recurringprofile_id']);
			$action = array();
		     $parent_orders = array();		
			$action[] = array(
				'text' => 'Manage Profile',
				'href' => $this->url->link('sale/recurring_order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $product['order_id'] . $url, 'SSL')
			);
			
		//gather aggregate total for this profile_ ID
		    $total_gross = $this->model_sale_recurring_order->getTotalGross($product['paypal_recurringprofile_id']);
			
				foreach ($parentresults as $presult) {
			$product_total = $this->model_sale_recurring_order->getTotalOrderProductsByOrderId($presult['order_id']);
			$voucher_total = $this->model_sale_recurring_order->getTotalOrderVouchersByOrderId($presult['order_id']);
			$parent_orders[] = array(
				'profile_id'      => $presult['paypal_recurringprofile_id'],
				'order_id'      => $presult['order_id'],
				'customer'      => $presult['customer'],
				'paypal_transaction_id'        => $presult['paypal_transaction_id'],
				'email'        => $presult['email'],
				'total_gross'         => $this->currency->format($presult['paypal_recurring_lastpaymentamount'], $presult['currency_code'], $presult['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($presult['date_added'])),
				'manage_child' =>  $this->url->link('sale/recurring_order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $presult['order_id'] . $url, 'SSL'),
				'products'   => ($product_total + $voucher_total)
				
			);
			
	}
				$this->data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product['name'],
					'model'    		   => $product['model'],
					'option'   		   => $option_data,
					'quantity'		   => $product['quantity'],
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'href'     		   => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL'),
					'parent_orders'      => $product['paypal_recurring_count'],
				    'parent_orders_list'        => $parent_orders,
				    'total_gross'         => $this->currency->format($total_gross, $product['currency_code'], $product['currency_value']),
					'profile_id'      => $product['paypal_recurringprofile_id'],
					'action'        => $action
				);
			}
		
			$this->data['vouchers'] = array();	
			
			$vouchers = $this->model_sale_recurring_order->getOrderVouchers($this->request->get['order_id']);
			 
			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
					'href'        => $this->url->link('sale/voucher/update', 'token=' . $this->session->data['token'] . '&voucher_id=' . $voucher['voucher_id'], 'SSL')
				);
			}
		
			$this->data['totals'] = $this->model_sale_recurring_order->getOrderTotals($this->request->get['order_id']);
			$this->data['downloads'] = array();
			foreach ($products as $product) {
				$results = $this->model_sale_recurring_order->getOrderDownloads($this->request->get['order_id'], $product['order_product_id']);
	
				foreach ($results as $result) {
					$this->data['downloads'][] = array(
						'name'      => $result['name'],
						'filename'  => $result['mask'],
						'remaining' => $result['remaining']
					);
				}
			}
			
			$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
			$this->data['order_status_id'] = $order_info['order_status_id'];
			// Fraud
			$this->load->model('sale/fraud');
			
			$fraud_info = $this->model_sale_fraud->getFraud($order_info['order_id']);
			
			if ($fraud_info) {
				$this->data['country_match'] = $fraud_info['country_match'];
				
				if ($fraud_info['country_code']) {
					$this->data['country_code'] = $fraud_info['country_code'];
				} else {
					$this->data['country_code'] = '';
				}
				
				$this->data['high_risk_country'] = $fraud_info['high_risk_country'];
				$this->data['distance'] = $fraud_info['distance'];
				
				if ($fraud_info['ip_region']) {
					$this->data['ip_region'] = $fraud_info['ip_region'];
				} else {
					$this->data['ip_region'] = '';
				}
								
				if ($fraud_info['ip_city']) {
					$this->data['ip_city'] = $fraud_info['ip_city'];
				} else {
					$this->data['ip_city'] = '';
				}
				
				$this->data['ip_latitude'] = $fraud_info['ip_latitude'];
				$this->data['ip_longitude'] = $fraud_info['ip_longitude'];
				if ($fraud_info['ip_isp']) {
					$this->data['ip_isp'] = $fraud_info['ip_isp'];
				} else {
					$this->data['ip_isp'] = '';
				}
				
				if ($fraud_info['ip_org']) {
					$this->data['ip_org'] = $fraud_info['ip_org'];
				} else {
					$this->data['ip_org'] = '';
				}
								
				$this->data['ip_asnum'] = $fraud_info['ip_asnum'];
				
				if ($fraud_info['ip_user_type']) {
					$this->data['ip_user_type'] = $fraud_info['ip_user_type'];
				} else {
					$this->data['ip_user_type'] = '';
				}
				
				if ($fraud_info['ip_country_confidence']) {
					$this->data['ip_country_confidence'] = $fraud_info['ip_country_confidence'];
				} else {
					$this->data['ip_country_confidence'] = '';
				}
												
				if ($fraud_info['ip_region_confidence']) {
					$this->data['ip_region_confidence'] = $fraud_info['ip_region_confidence'];
				} else {
					$this->data['ip_region_confidence'] = '';
				}
				
				if ($fraud_info['ip_city_confidence']) {
					$this->data['ip_city_confidence'] = $fraud_info['ip_city_confidence'];
				} else {
					$this->data['ip_city_confidence'] = '';
				}
				
				if ($fraud_info['ip_postal_confidence']) {
					$this->data['ip_postal_confidence'] = $fraud_info['ip_postal_confidence'];
				} else {
					$this->data['ip_postal_confidence'] = '';
				}
				
				if ($fraud_info['ip_postal_code']) {
					$this->data['ip_postal_code'] = $fraud_info['ip_postal_code'];
				} else {
					$this->data['ip_postal_code'] = '';
				}
								
				$this->data['ip_accuracy_radius'] = $fraud_info['ip_accuracy_radius'];
				
				if ($fraud_info['ip_net_speed_cell']) {
					$this->data['ip_net_speed_cell'] = $fraud_info['ip_net_speed_cell'];
				} else {
					$this->data['ip_net_speed_cell'] = '';
				}
								
				$this->data['ip_metro_code'] = $fraud_info['ip_metro_code'];
				$this->data['ip_area_code'] = $fraud_info['ip_area_code'];
				
				if ($fraud_info['ip_time_zone']) {
					$this->data['ip_time_zone'] = $fraud_info['ip_time_zone'];
				} else {
					$this->data['ip_time_zone'] = '';
				}
				if ($fraud_info['ip_region_name']) {
					$this->data['ip_region_name'] = $fraud_info['ip_region_name'];
				} else {
					$this->data['ip_region_name'] = '';
				}				
				
				if ($fraud_info['ip_domain']) {
					$this->data['ip_domain'] = $fraud_info['ip_domain'];
				} else {
					$this->data['ip_domain'] = '';
				}
				
				if ($fraud_info['ip_country_name']) {
					$this->data['ip_country_name'] = $fraud_info['ip_country_name'];
				} else {
					$this->data['ip_country_name'] = '';
				}	
								
				if ($fraud_info['ip_continent_code']) {
					$this->data['ip_continent_code'] = $fraud_info['ip_continent_code'];
				} else {
					$this->data['ip_continent_code'] = '';
				}
				
				if ($fraud_info['ip_corporate_proxy']) {
					$this->data['ip_corporate_proxy'] = $fraud_info['ip_corporate_proxy'];
				} else {
					$this->data['ip_corporate_proxy'] = '';
				}
								
				$this->data['anonymous_proxy'] = $fraud_info['anonymous_proxy'];
				$this->data['proxy_score'] = $fraud_info['proxy_score'];
				
				if ($fraud_info['is_trans_proxy']) {
					$this->data['is_trans_proxy'] = $fraud_info['is_trans_proxy'];
				} else {
					$this->data['is_trans_proxy'] = '';
				}	
							
				$this->data['free_mail'] = $fraud_info['free_mail'];
				$this->data['carder_email'] = $fraud_info['carder_email'];
				
				if ($fraud_info['high_risk_username']) {
					$this->data['high_risk_username'] = $fraud_info['high_risk_username'];
				} else {
					$this->data['high_risk_username'] = '';
				}
							
				if ($fraud_info['high_risk_password']) {
					$this->data['high_risk_password'] = $fraud_info['high_risk_password'];
				} else {
					$this->data['high_risk_password'] = '';
				}		
				
				$this->data['bin_match'] = $fraud_info['bin_match'];
				if ($fraud_info['bin_country']) {
					$this->data['bin_country'] = $fraud_info['bin_country'];
				} else {
					$this->data['bin_country'] = '';
				}	
								
				$this->data['bin_name_match'] = $fraud_info['bin_name_match'];
				
				if ($fraud_info['bin_name']) {
					$this->data['bin_name'] = $fraud_info['bin_name'];
				} else {
					$this->data['bin_name'] = '';
				}	
								
				$this->data['bin_phone_match'] = $fraud_info['bin_phone_match'];
				if ($fraud_info['bin_phone']) {
					$this->data['bin_phone'] = $fraud_info['bin_phone'];
				} else {
					$this->data['bin_phone'] = '';
				}	
				
				if ($fraud_info['customer_phone_in_billing_location']) {
					$this->data['customer_phone_in_billing_location'] = $fraud_info['customer_phone_in_billing_location'];
				} else {
					$this->data['customer_phone_in_billing_location'] = '';
				}	
												
				$this->data['ship_forward'] = $fraud_info['ship_forward'];
				if ($fraud_info['city_postal_match']) {
					$this->data['city_postal_match'] = $fraud_info['city_postal_match'];
				} else {
					$this->data['city_postal_match'] = '';
				}	
				
				if ($fraud_info['ship_city_postal_match']) {
					$this->data['ship_city_postal_match'] = $fraud_info['ship_city_postal_match'];
				} else {
					$this->data['ship_city_postal_match'] = '';
				}	
								
				$this->data['score'] = $fraud_info['score'];
				$this->data['explanation'] = $fraud_info['explanation'];
				$this->data['risk_score'] = $fraud_info['risk_score'];
				$this->data['queries_remaining'] = $fraud_info['queries_remaining'];
				$this->data['maxmind_id'] = $fraud_info['maxmind_id'];
				$this->data['error'] = $fraud_info['error'];
			} else {
				$this->data['maxmind_id'] = '';
			}
			
			$this->template = 'sale/recurring_order_info.tpl';
			$this->children = array(
				'common/header',
				'common/footer'
			);
			
			$this->response->setOutput($this->render());
		} else {
			$this->load->language('error/not_found');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->data['heading_title'] = $this->language->get('heading_title');
			$this->data['text_not_found'] = $this->language->get('text_not_found');
			$this->data['breadcrumbs'] = array();
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
			);
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
			);
		
			$this->template = 'error/not_found.tpl';
			$this->children = array(
				'common/header',
				'common/footer'
			);
		
			$this->response->setOutput($this->render());
		}	
	}
	public function createInvoiceNo() {
		$this->language->load('sale/recurring_order');
		$json = array();
		
     	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$json['error'] = $this->language->get('error_permission'); 
		} elseif (isset($this->request->get['order_id'])) {
			$this->load->model('sale/recurring_order');
			
			$invoice_no = $this->model_sale_recurring_order->createInvoiceNo($this->request->get['order_id']);
			
			if ($invoice_no) {
				$json['invoice_no'] = $invoice_no;
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}
		$this->response->setOutput(json_encode($json));
  	}
	public function addCredit() {
		$this->language->load('sale/recurring_order');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['order_id'])) {
			$this->load->model('sale/recurring_order');
			
			$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$this->load->model('sale/customer');
				
				$credit_total = $this->model_sale_customer->getTotalTransactionsByOrderId($this->request->get['order_id']);
				
				if (!$credit_total) {
					$this->model_sale_customer->addTransaction($order_info['customer_id'], $this->language->get('text_order_id') . ' #' . $this->request->get['order_id'], $order_info['total'], $this->request->get['order_id']);
					
					$json['success'] = $this->language->get('text_credit_added');
				} else {
					$json['error'] = $this->language->get('error_action');
				}
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	public function removeCredit() {
		$this->language->load('sale/recurring_order');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['order_id'])) {
			$this->load->model('sale/recurring_order');
			
			$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$this->load->model('sale/customer');
				
				$this->model_sale_customer->deleteTransaction($this->request->get['order_id']);
					
				$json['success'] = $this->language->get('text_credit_removed');
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
				
	public function addReward() {
		$this->language->load('sale/recurring_order');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['order_id'])) {
			$this->load->model('sale/recurring_order');
						
			$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$this->load->model('sale/customer');
				$reward_total = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($this->request->get['order_id']);
				
				if (!$reward_total) {
					$this->model_sale_customer->addReward($order_info['customer_id'], $this->language->get('text_order_id') . ' #' . $this->request->get['order_id'], $order_info['reward'], $this->request->get['order_id']);
					
					$json['success'] = $this->language->get('text_reward_added');
				} else {
					$json['error'] = $this->language->get('error_action'); 
				}
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	public function removeReward() {
		$this->language->load('sale/recurring_order');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['order_id'])) {
			$this->load->model('sale/recurring_order');
			
			$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);
			
			if ($order_info && $order_info['customer_id']) {
				$this->load->model('sale/customer');
				$this->model_sale_customer->deleteReward($this->request->get['order_id']);
				
				$json['success'] = $this->language->get('text_reward_removed');
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
		
	public function addCommission() {
		$this->language->load('sale/recurring_order');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['order_id'])) {
			$this->load->model('sale/recurring_order');
			
			$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);
			
			if ($order_info && $order_info['affiliate_id']) {
				$this->load->model('sale/affiliate');
				
				$affiliate_total = $this->model_sale_affiliate->getTotalTransactionsByOrderId($this->request->get['order_id']);
				
				if (!$affiliate_total) {
					$this->model_sale_affiliate->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $this->request->get['order_id'], $order_info['commission'], $this->request->get['order_id']);
					
					$json['success'] = $this->language->get('text_commission_added');
				} else {
					$json['error'] = $this->language->get('error_action'); 
				}
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	public function removeCommission() {
		$this->language->load('sale/recurring_order');
		
		$json = array(); 
    	
     	if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['order_id'])) {
			$this->load->model('sale/recurring_order');
			
			$order_info = $this->model_sale_recurring_order->getOrder($this->request->get['order_id']);
			
			if ($order_info && $order_info['affiliate_id']) {
				$this->load->model('sale/affiliate');
				$this->model_sale_affiliate->deleteTransaction($this->request->get['order_id']);
				
				$json['success'] = $this->language->get('text_commission_removed');
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	public function history() {
    	$this->language->load('sale/recurring_order');
		
		$this->data['error'] = '';
		$this->data['success'] = '';
		
		$this->load->model('sale/recurring_order');
	
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'sale/recurring_order')) { 
				$this->data['error'] = $this->language->get('error_permission');
			}
			
			if (!$this->data['error']) { 
				$this->model_sale_recurring_order->addOrderHistory($this->request->get['order_id'], $this->request->post);
				
				$this->data['success'] = $this->language->get('text_success');
			}
		}
				
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_notify'] = $this->language->get('column_notify');
		$this->data['column_comment'] = $this->language->get('column_comment');
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  
		
		$this->data['histories'] = array();
			
		$results = $this->model_sale_recurring_order->getOrderHistories($this->request->get['order_id'], ($page - 1) * 10, 10);
      		
		foreach ($results as $result) {
        	$this->data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
        		'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
        	);
      	}			
		
		$history_total = $this->model_sale_recurring_order->getTotalOrderHistories($this->request->get['order_id']);
			
		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10; 
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/recurring_order/history', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		
		$this->template = 'sale/recurring_order_history.tpl';		
		
		$this->response->setOutput($this->render());
  	}
	
	public function download() {
		$this->load->model('sale/recurring_order');
		
		if (isset($this->request->get['order_option_id'])) {
			$order_option_id = $this->request->get['order_option_id'];
		} else {
			$order_option_id = 0;
		}
		
		$option_info = $this->model_sale_recurring_order->getOrderOption($this->request->get['order_id'], $order_option_id);
		
		if ($option_info && $option_info['type'] == 'file') {
			$file = DIR_DOWNLOAD . $option_info['value'];
			$mask = basename(utf8_substr($option_info['value'], 0, utf8_strrpos($option_info['value'], '.')));
			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					
					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->load->language('error/not_found');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->data['heading_title'] = $this->language->get('heading_title');
			$this->data['text_not_found'] = $this->language->get('text_not_found');
			$this->data['breadcrumbs'] = array();
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
			);
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
			);
		
			$this->template = 'error/not_found.tpl';
			$this->children = array(
				'common/header',
				'common/footer'
			);
		
			$this->response->setOutput($this->render());
		}	
	}
	public function upload() {
		$this->language->load('sale/recurring_order');
		
		$json = array();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!empty($this->request->files['file']['name'])) {
				$filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
				
				if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
					$json['error'] = $this->language->get('error_filename');
				}	  	
				
				$allowed = array();
				
				$filetypes = explode(',', $this->config->get('config_upload_allowed'));
				
				foreach ($filetypes as $filetype) {
					$allowed[] = trim($filetype);
				}
				
				if (!in_array(utf8_substr(strrchr($filename, '.'), 1), $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}
							
				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		
			if (!isset($json['error'])) {
				if (is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
					$file = basename($filename) . '.' . md5(mt_rand());
					
					$json['file'] = $file;
					
					move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);
				}
							
				$json['success'] = $this->language->get('text_upload');
			}	
		}
		
		$this->response->setOutput(json_encode($json));
	}
			
  	public function invoice() {
		$this->load->language('sale/recurring_order');
		$this->data['title'] = $this->language->get('heading_title');
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}
		$this->data['direction'] = $this->language->get('direction');
		$this->data['language'] = $this->language->get('code');
		$this->data['text_invoice'] = $this->language->get('text_invoice');
		$this->data['text_order_id'] = $this->language->get('text_order_id');
		$this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$this->data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_telephone'] = $this->language->get('text_telephone');
		$this->data['text_fax'] = $this->language->get('text_fax');
		$this->data['text_to'] = $this->language->get('text_to');
		$this->data['text_company_id'] = $this->language->get('text_company_id');
		$this->data['text_tax_id'] = $this->language->get('text_tax_id');		
		$this->data['text_ship_to'] = $this->language->get('text_ship_to');
		$this->data['text_payment_method'] = $this->language->get('text_payment_method');
		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$this->data['column_product'] = $this->language->get('column_product');
		$this->data['column_model'] = $this->language->get('column_model');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_total'] = $this->language->get('column_total');
		$this->data['column_comment'] = $this->language->get('column_comment');
		$this->load->model('sale/recurring_order');
		$this->load->model('setting/setting');
		$this->data['orders'] = array();
		$orders = array();
		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
		foreach ($orders as $order_id) {
			$order_info = $this->model_sale_recurring_order->getOrder($order_id);
			if ($order_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
				
				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}
				
				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}
				
				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);
				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);
				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
				$product_data = array();
				$products = $this->model_sale_recurring_order->getOrderProducts($order_id);
				foreach ($products as $product) {
					$option_data = array();
					$options = $this->model_sale_recurring_order->getOrderOptions($order_id, $product['order_product_id']);
					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
						}
						
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);								
					}
					$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}
				
				$voucher_data = array();
				
				$vouchers = $this->model_sale_recurring_order->getOrderVouchers($order_id);
				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])			
					);
				}
					
				$total_data = $this->model_sale_recurring_order->getOrderTotals($order_id);
				$this->data['orders'][] = array(
					'order_id'	         => $order_id,
					'invoice_no'         => $invoice_no,
					'date_added'         => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'         => $order_info['store_name'],
					'store_url'          => rtrim($order_info['store_url'], '/'),
					'store_address'      => nl2br($store_address),
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'email'              => $order_info['email'],
					'telephone'          => $order_info['telephone'],
					'shipping_address'   => $shipping_address,
					'shipping_method'    => $order_info['shipping_method'],
					'payment_address'    => $payment_address,
					'payment_company_id' => $order_info['payment_company_id'],
					'payment_tax_id'     => $order_info['payment_tax_id'],
					'payment_method'     => $order_info['payment_method'],
					'product'            => $product_data,
					'voucher'            => $voucher_data,
					'total'              => $total_data,
					'comment'            => nl2br($order_info['comment'])
				);
			}
		}
		$this->template = 'sale/recurring_order_invoice.tpl';
		$this->response->setOutput($this->render());
	}
}
?>