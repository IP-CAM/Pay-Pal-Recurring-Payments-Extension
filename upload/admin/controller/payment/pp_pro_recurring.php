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
	private $error = array(); 
	public function index() {	
   
		$this->data = array_merge($this->data, $this->load->language('payment/pp_pro_recurring'));
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		$this->load->model('localisation/language');
		$this->data['token'] = $this->session->data['token'];
		if (isset($this->session->data['success_recurring'])) {
			$this->data['success_recurring'] = $this->session->data['success_recurring'];
		
			unset($this->session->data['success_recurring']);
		} else {
			$this->data['success_recurring'] = '';
		}
		 $this->data['languages'] = $this->model_localisation_language->getLanguages();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->session->data['success_recurring'] = $this->language->get('text_success');
			$this->model_setting_setting->editSetting('pp_pro_recurring', $this->request->post);				
			//insert trials if any
		if (isset($this->request->post['iteminfo']))  {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "paypal_recurring_items` ");
			foreach($this->request->post['iteminfo'] as $items){
		 
		   
		   foreach($items as  $item_data){
			  $this->db->query("INSERT INTO `" . DB_PREFIX . "paypal_recurring_items` SET item_period = '" . (string)$item_data['period']  . "',item_status = '" . (int)$item_data['status']  . "',item_product = '" . (int)$item_data['pid']  . "',item_frequency = '" . (int)$item_data['frequency']  . "',item_cycles = '" . (int)$item_data['cycles']  . "'");  
		       }
			 
			}
		}else{
			
			$this->data['iteminfo'] = '';
		}
		
			//insert trials if any
		if (isset($this->request->post['trialinfo']))  {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "paypal_recurring_trials` WHERE trial_status = '1'");
			foreach($this->request->post['trialinfo'] as $trial){
		 
		   $this->db->query("INSERT INTO `" . DB_PREFIX . "paypal_recurring_trials` SET trial_status = '1'");
           $trial_id = $this->db->getLastId();
		   
		   foreach($trial as  $trial_data){
			  $this->db->query("UPDATE `" . DB_PREFIX . "paypal_recurring_trials` SET trial_period = '" . (string)$trial_data['period']  . "',trial_product = '" . (int)$trial_data['product']  . "',trial_frequency = '" . (int)$trial_data['frequency']  . "',trial_cycles = '" . (int)$trial_data['cycles']  . "',trial_amount = '" . (float)$trial_data['amount']  . "' WHERE trial_id =  '" . (int)$trial_id . "'");  
		       }
			 
			}
		}else{
			
			$this->data['trialinfo'] = '';
		}
		
		if (isset($this->request->post['trialinfonew'])) {
			foreach($this->request->post['trialinfonew'] as $trial){
		 
		   $this->db->query("INSERT INTO `" . DB_PREFIX . "paypal_recurring_trials` SET trial_status = '1'");
           $trial_id = $this->db->getLastId();
		   
		   foreach($trial as  $trial_data){
			  $this->db->query("UPDATE `" . DB_PREFIX . "paypal_recurring_trials` SET trial_period = '" . (string)$trial_data['period']  . "',trial_product = '" . (int)$trial_data['product']  . "',trial_frequency = '" . (int)$trial_data['frequency']  . "',trial_cycles = '" . (int)$trial_data['cycles']  . "',trial_amount = '" . (float)$trial_data['amount']  . "' WHERE trial_id =  '" . (int)$trial_id . "'");  
		       }
			 
			}
		}else{
			
			$this->data['trialinfonew'] = '';
		}
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('payment/pp_pro_recurring', 'token=' . $this->session->data['token'], 'SSL'));
		}
         
		//deal with recurring item settings if any 
  $trial_count = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "paypal_recurring_trials` WHERE trial_status = '1'");
	    $trial_product_count = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE is_trial = '1' AND status = '1'");
		$this->data['trial_count_products'] = $trial_product_count->row['total'];
		$this->data['trial_count'] = $trial_count->row['total'];
		$this->data['cycles'] = $this->language->get('entry_cycles');		
        //left in language strings. 
		$this->data['must'] = $this->language->get('entry_must');
		$this->data['tab_general'] = $this->language->get('text_general');
		$this->data['tab_configuration'] = $this->language->get('text_configuration');
		$this->data['tab_emailtemplates'] = $this->language->get('text_emailtemplates');
		$this->data['tab_documentation'] = $this->language->get('text_documentation');
		$this->data['tab_order'] = $this->language->get('text_order');
		$this->data['tab_catalog'] = $this->language->get('text_catalog');
		$this->data['tab_ipn'] = $this->language->get('text_ipn');
		$this->data['save'] = $this->language->get('save_text');
		$this->data['frequency'] = $this->language->get('entry_frequency');
		$this->data['periodtext'] = $this->language->get('entry_periodtext');
		
		$this->data['email_template_info'] = sprintf($this->language->get('email_template_info'), HTTP_SERVER . 'view/image/email_template.jpg');
		$this->data['entry_screen_confirm1_info'] = sprintf($this->language->get('entry_screen_confirm1_info'), HTTP_SERVER . 'view/image/success.jpg');
		$this->data['entry_screen_confirm2_info'] = sprintf($this->language->get('entry_screen_confirm2_info'), HTTP_SERVER . 'view/image/continue_continue.jpg');
		$this->data['entry_screen_confirm3_info'] = sprintf($this->language->get('entry_screen_confirm3_info'), HTTP_SERVER . 'view/image/continue_cancel.jpg');
$url = '';
			$this->data['jump'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$this->data['productjump'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
 		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}
		
 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
 		if (isset($this->error['signature'])) {
			$this->data['error_signature'] = $this->error['signature'];
		} else {
			$this->data['error_signature'] = '';
		}
		if (isset($this->error['description'])) {
			$this->data['error_description'] = $this->error['description'];
		} else {
			$this->data['error_description'] = '';
		}
		
		if (isset($this->error['frequency'])) {
			$this->data['error_frequency'] = $this->error['frequency'];
		} else {
			$this->data['error_frequency'] = '';
		}
		
        if (isset($this->error['trialamount'])) {
			$this->data['error_trialamount'] = $this->error['trialamount'];
		} else {
			$this->data['error_trialamount'] = '';
		}
		if (isset($this->error['trialcycles'])) {
			$this->data['error_trialcycles'] = $this->error['trialcycles'];
		} else {
			$this->data['error_trialcycles'] = '';
		}
		if (isset($this->error['trialbillingfrequency'])) {
			$this->data['error_trialbillingfrequency'] = $this->error['trialbillingfrequency'];
		} else {
			$this->data['error_trialbillingfrequency'] = '';
		}
		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/pp_pro_recurring', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
				
		    $this->data['action'] = $this->url->link('payment/pp_pro_recurring', 'token=' . $this->session->data['token'], 'SSL');
		
		    $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
        	$this->load->model('tool/image');
			$this->data['no_image'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);
		
		if (isset($this->request->post['email_template_logo_1'])) {
			$this->data['email_template_logo_1'] = $this->request->post['email_template_logo_1'];
		} else {
			$this->data['email_template_logo_1'] = $this->config->get('email_template_logo_1');			
		}
if (isset($this->request->post['paypal_recurring_master_type'])) {
			$this->data['paypal_recurring_master_type'] = $this->request->post['paypal_recurring_master_type'];
		} else {
			$this->data['paypal_recurring_master_type'] = $this->config->get('paypal_recurring_master_type');
		}
		if ($this->config->get('email_template_logo_1') && file_exists(DIR_IMAGE . $this->config->get('email_template_logo_1')) && is_file(DIR_IMAGE . $this->config->get('email_template_logo_1'))) {
			$this->data['template_logo_1'] = $this->model_tool_image->resize($this->config->get('email_template_logo_1'), 100, 100);		
		} else {
			$this->data['template_logo_1'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);
		}
		
		if (isset($this->request->post['email_template_logo_2'])) {
			$this->data['email_template_logo_2'] = $this->request->post['email_template_logo_2'];
		} else {
			$this->data['email_template_logo_2'] = $this->config->get('email_template_logo_2');			
		}
		if ($this->config->get('email_template_logo_2') && file_exists(DIR_IMAGE . $this->config->get('email_template_logo_2')) && is_file(DIR_IMAGE . $this->config->get('email_template_logo_2'))) {
			$this->data['template_logo_2'] = $this->model_tool_image->resize($this->config->get('email_template_logo_2'), 100, 100);		
		} else {
			$this->data['template_logo_2'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);
		}
		if (isset($this->request->post['email_template_logo_3'])) {
			$this->data['email_template_logo_3'] = $this->request->post['email_template_logo_3'];
		} else {
			$this->data['email_template_logo_3'] = $this->config->get('email_template_logo_3');			
		}
		if ($this->config->get('email_template_logo_3') && file_exists(DIR_IMAGE . $this->config->get('email_template_logo_3')) && is_file(DIR_IMAGE . $this->config->get('email_template_logo_3'))) {
			$this->data['template_logo_3'] = $this->model_tool_image->resize($this->config->get('email_template_logo_3'), 100, 100);		
		} else {
			$this->data['template_logo_3'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);
		}
		
		if (isset($this->request->post['pp_pro_recurring_username'])) {
			$this->data['pp_pro_recurring_username'] = $this->request->post['pp_pro_recurring_username'];
		} else {
			$this->data['pp_pro_recurring_username'] = $this->config->get('pp_pro_recurring_username');
		}
		if (isset($this->request->post['pp_pro_recurring_start_date'])) {
			$this->data['pp_pro_recurring_start_date'] = $this->request->post['pp_pro_recurring_start_date'];
		} else {
			$this->data['pp_pro_recurring_start_date'] = $this->config->get('pp_pro_recurring_start_date');
		}
		if (isset($this->request->post['pp_pro_recurring_initialamount'])) {
			$this->data['pp_pro_recurring_initialamount'] = $this->request->post['pp_pro_recurring_initialamount'];
		} else {
			$this->data['pp_pro_recurring_initialamount'] = $this->config->get('pp_pro_recurring_initialamount');
		}		
		
		if (isset($this->request->post['pp_pro_recurring_trialamount'])) {
			$this->data['pp_pro_recurring_trialamount'] = $this->request->post['pp_pro_recurring_trialamount'];
		} else {
			$this->data['pp_pro_recurring_trialamount'] = $this->config->get('pp_pro_recurring_trialamount');
		}
		
		if (isset($this->request->post['pp_pro_recurring_shippingamount'])) {
			$this->data['pp_pro_recurring_shippingamount'] = $this->request->post['pp_pro_recurring_shippingamount'];
		} else {
			$this->data['pp_pro_recurring_shippingamount'] = $this->config->get('pp_pro_recurring_shippingamount');
		}
		
		if (isset($this->request->post['pp_pro_recurring_trialbillingperiod'])) {
			$this->data['pp_pro_recurring_trialbillingperiod'] = $this->request->post['pp_pro_recurring_trialbillingperiod'];
		} else {
			$this->data['pp_pro_recurring_trialbillingperiod'] = $this->config->get('pp_pro_recurring_trialbillingperiod');
		}
		if (isset($this->request->post['pp_pro_recurring_trial'])) {
			$this->data['pp_pro_recurring_trial'] = $this->request->post['pp_pro_recurring_trial'];
		} else {
			$this->data['pp_pro_recurring_trial'] = $this->config->get('pp_pro_recurring_trial');
		}
		
		
		if (isset($this->request->post['pp_pro_recurring_usetotal'])) {
			$this->data['pp_pro_recurring_usetotal'] = $this->request->post['pp_pro_recurring_usetotal'];
		} else {
			$this->data['pp_pro_recurring_usetotal'] = $this->config->get('pp_pro_recurring_usetotal');
		}
		if (isset($this->request->post['pp_pro_recurring_amount'])) {
			$this->data['pp_pro_recurring_amount'] = $this->request->post['pp_pro_recurring_amount'];
		} else {
			$this->data['pp_pro_recurring_amount'] = $this->config->get('pp_pro_recurring_amount');
		}
		if (isset($this->request->post['pp_pro_recurring_maxfailed'])) {
			$this->data['pp_pro_recurring_maxfailed'] = $this->request->post['pp_pro_recurring_maxfailed'];
		} else {
			$this->data['pp_pro_recurring_maxfailed'] = $this->config->get('pp_pro_recurring_maxfailed');
		}
		if (isset($this->request->post['pp_pro_recurring_taxamount'])) {
			$this->data['pp_pro_recurring_taxamount'] = $this->request->post['pp_pro_recurring_taxamount'];
		} else {
			$this->data['pp_pro_recurring_taxamount'] = $this->config->get('pp_pro_recurring_taxamount');
		}
		
		if (isset($this->request->post['pp_pro_recurring_useorderid'])) {
			$this->data['pp_pro_recurring_useorderid'] = $this->request->post['pp_pro_recurring_useorderid'];
		} else {
			$this->data['pp_pro_recurring_useorderid'] = $this->config->get('pp_pro_recurring_useorderid');
		}
		if (isset($this->request->post['pp_pro_recurring_reference'])) {
			$this->data['pp_pro_recurring_reference'] = $this->request->post['pp_pro_recurring_reference'];
		} else {
			$this->data['pp_pro_recurring_reference'] = $this->config->get('pp_pro_recurring_reference');
		}
		
		if (isset($this->request->post['pp_pro_recurring_desc'])) {
			$this->data['pp_pro_recurring_desc'] = $this->request->post['pp_pro_recurring_desc'];
		} else {
			$this->data['pp_pro_recurring_desc'] = $this->config->get('pp_pro_recurring_desc');
		}
		
		
		if (isset($this->request->post['pp_pro_recurring_billingperiod'])) {
			$this->data['pp_pro_recurring_billingperiod'] = $this->request->post['pp_pro_recurring_billingperiod'];
		} else {
			$this->data['pp_pro_recurring_billingperiod'] = $this->config->get('pp_pro_recurring_billingperiod');
		}
		if (isset($this->request->post['pp_pro_recurring_billingfrequency'])) {
			$this->data['pp_pro_recurring_billingcycles'] = $this->request->post['pp_pro_recurring_billingcycles'];
		} else {
			$this->data['pp_pro_recurring_billingcycles'] = $this->config->get('pp_pro_recurring_billingcycles');
		}
		if (isset($this->request->post['pp_pro_recurring_billingfrequency'])) {
			$this->data['pp_pro_recurring_billingfrequency'] = $this->request->post['pp_pro_recurring_billingfrequency'];
		} else {
			$this->data['pp_pro_recurring_billingfrequency'] = $this->config->get('pp_pro_recurring_billingfrequency');
		}
		
		
		if (isset($this->request->post['pp_pro_recurring_password'])) {
			$this->data['pp_pro_recurring_password'] = $this->request->post['pp_pro_recurring_password'];
		} else {
			$this->data['pp_pro_recurring_password'] = $this->config->get('pp_pro_recurring_password');
		}
		
		if (isset($this->request->post['pp_pro_recurring_subscribername'])) {
			$this->data['pp_pro_recurring_subscribername'] = $this->request->post['pp_pro_recurring_subscribername'];
		} else {
			$this->data['pp_pro_recurring_subscribername'] = $this->config->get('pp_pro_recurring_subscribername');
		}
		
				
		if (isset($this->request->post['pp_pro_recurring_signature'])) {
			$this->data['pp_pro_recurring_signature'] = $this->request->post['pp_pro_recurring_signature'];
		} else {
			$this->data['pp_pro_recurring_signature'] = $this->config->get('pp_pro_recurring_signature');
		}
		if (isset($this->request->post['pp_pro_recurring_trialcycles'])) {
			$this->data['pp_pro_recurring_trialcycles'] = $this->request->post['pp_pro_recurring_trialcycles'];
		} else {
			$this->data['pp_pro_recurring_trialcycles'] = $this->config->get('pp_pro_recurring_trialcycles');
		}
		
		if (isset($this->request->post['pp_pro_recurring_usermanage'])) {
			$this->data['pp_pro_recurring_usermanage'] = $this->request->post['pp_pro_recurring_usermanage'];
		} else {
			$this->data['pp_pro_recurring_usermanage'] = $this->config->get('pp_pro_recurring_usermanage');
		}
		
		if (isset($this->request->post['pp_pro_recurring_test'])) {
			$this->data['pp_pro_recurring_test'] = $this->request->post['pp_pro_recurring_test'];
		} else {
			$this->data['pp_pro_recurring_test'] = $this->config->get('pp_pro_recurring_test');
		}
		
		if (isset($this->request->post['display_trial_order_list'])) {
			$this->data['display_trial_order_list'] = $this->request->post['display_trial_order_list'];
		} else {
			$this->data['display_trial_order_list'] = $this->config->get('display_trial_order_list');
		}
		if (isset($this->request->post['display_order_list'])) {
			$this->data['display_order_list'] = $this->request->post['display_order_list'];
		} else {
			$this->data['display_order_list'] = $this->config->get('display_order_list');
		}
		
		if (isset($this->request->post['display_in_orderlist'])) {
			$this->data['display_in_orderlist'] = $this->request->post['display_in_orderlist'];
		} else {
			$this->data['display_in_orderlist'] = $this->config->get('display_in_orderlist');
		}
		
		
		
		if (isset($this->request->post['entry_email_confirm1_enable'])) {
			$this->data['entry_email_confirm1_enable'] = $this->request->post['entry_email_confirm1_enable'];
		} else {
			$this->data['entry_email_confirm1_enable'] = $this->config->get('entry_email_confirm1_enable');
		}
		if (isset($this->request->post['entry_email_confirm2_enable'])) {
			$this->data['entry_email_confirm2_enable'] = $this->request->post['entry_email_confirm2_enable'];
		} else {
			$this->data['entry_email_confirm2_enable'] = $this->config->get('entry_email_confirm2_enable');
		}
		if (isset($this->request->post['entry_email_confirm3_enable'])) {
			$this->data['entry_email_confirm3_enable'] = $this->request->post['entry_email_confirm3_enable'];
		} else {
			$this->data['entry_email_confirm3_enable'] = $this->config->get('entry_email_confirm3_enable');
		}
		
		if (isset($this->request->post['pp_pro_recurring_trialbillingfrequency'])) {
			$this->data['pp_pro_recurring_trialbillingfrequency'] = $this->request->post['pp_pro_recurring_trialbillingfrequency'];
		} else {
			$this->data['pp_pro_recurring_trialbillingfrequency'] = $this->config->get('pp_pro_recurring_trialbillingfrequency');
		}
		
		if (isset($this->request->post['pp_pro_recurring_method'])) {
			$this->data['pp_pro_recurring_transaction'] = $this->request->post['pp_pro_recurring_transaction'];
		} else {
			$this->data['pp_pro_recurring_transaction'] = $this->config->get('pp_pro_recurring_transaction');
		}
		
		
		if (isset($this->request->post['pp_pro_recurring_total'])) {
			$this->data['pp_pro_recurring_total'] = $this->request->post['pp_pro_recurring_total'];
		} else {
			$this->data['pp_pro_recurring_total'] = $this->config->get('pp_pro_recurring_total'); 
		} 
				
		if (isset($this->request->post['pp_pro_recurring_order_status_id'])) {
			$this->data['pp_pro_recurring_order_status_id'] = $this->request->post['pp_pro_recurring_order_status_id'];
		} else {
			$this->data['pp_pro_recurring_order_status_id'] = $this->config->get('pp_pro_recurring_order_status_id'); 
		} 
		if (isset($this->request->post['pp_pro_recurring_declined_order_status_id'])) {
			$this->data['pp_pro_recurring_declined_order_status_id'] = $this->request->post['pp_pro_recurring_declined_order_status_id'];
		} else {
			$this->data['pp_pro_recurring_declined_order_status_id'] = $this->config->get('pp_pro_recurring_declined_order_status_id'); 
		} 
if (isset($this->request->post['pp_pro_recurring_expired_order_status_id'])) {
			$this->data['pp_pro_recurring_expired_order_status_id'] = $this->request->post['pp_pro_recurring_expired_order_status_id'];
		} else {
			$this->data['pp_pro_recurring_expired_order_status_id'] = $this->config->get('pp_pro_recurring_expired_order_status_id'); 
		} 
		if (isset($this->request->post['pp_pro_recurring_refunded_order_status_id'])) {
			$this->data['pp_pro_recurring_refunded_order_status_id'] = $this->request->post['pp_pro_recurring_refunded_order_status_id'];
		} else {
			$this->data['pp_pro_recurring_refunded_order_status_id'] = $this->config->get('pp_pro_recurring_refunded_order_status_id'); 
		} 
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['pp_pro_recurring_geo_zone_id'])) {
			$this->data['pp_pro_recurring_geo_zone_id'] = $this->request->post['pp_pro_recurring_geo_zone_id'];
		} else {
			$this->data['pp_pro_recurring_geo_zone_id'] = $this->config->get('pp_pro_recurring_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['pp_pro_recurring_status'])) {
			$this->data['pp_pro_recurring_status'] = $this->request->post['pp_pro_recurring_status'];
		} else {
			$this->data['pp_pro_recurring_status'] = $this->config->get('pp_pro_recurring_status');
		}
		if (isset($this->request->post['pp_pro_recurring_autobill'])) {
			$this->data['pp_pro_recurring_autobill'] = $this->request->post['pp_pro_recurring_autobill'];
		} else {
			$this->data['pp_pro_recurring_autobill'] = $this->config->get('pp_pro_recurring_autobill');
		}
		
		if (isset($this->request->post['pp_pro_recurring_initialamountfail'])) {
			$this->data['pp_pro_recurring_initialamountfail'] = $this->request->post['pp_pro_recurring_initialamountfail'];
		} else {
			$this->data['pp_pro_recurring_initialamountfail'] = $this->config->get('pp_pro_recurring_initialamountfail');
		}
		
		
		if (isset($this->request->post['pp_pro_recurring_sort_order'])) {
			$this->data['pp_pro_recurring_sort_order'] = $this->request->post['pp_pro_recurring_sort_order'];
		} else {
			$this->data['pp_pro_recurring_sort_order'] = $this->config->get('pp_pro_recurring_sort_order');
		}
		if (isset($this->request->post['email_confirm1'])) {
			$this->data['email_confirm1'] = $this->request->post['email_confirm1'];
		} else {
			$this->data['email_confirm1'] = $this->config->get('email_confirm1');
		}
		if (isset($this->request->post['email_confirm2'])) {
			$this->data['email_confirm2'] = $this->request->post['email_confirm2'];
		} else {
			$this->data['email_confirm2'] = $this->config->get('email_confirm2');
		}
		if (isset($this->request->post['email_confirm3'])) {
			$this->data['email_confirm3'] = $this->request->post['email_confirm3'];
		} else {
			$this->data['email_confirm3'] = $this->config->get('email_confirm3');
		}
		
		if (isset($this->request->post['screen_confirm1'])) {
			$this->data['screen_confirm1'] = $this->request->post['screen_confirm1'];
		} else {
			$this->data['screen_confirm1'] = $this->config->get('screen_confirm1');
		}
		if (isset($this->request->post['screen_confirm2'])) {
			$this->data['screen_confirm2'] = $this->request->post['screen_confirm2'];
		} else {
			$this->data['screen_confirm2'] = $this->config->get('screen_confirm2');
		}
		if (isset($this->request->post['screen_confirm3'])) {
			$this->data['screen_confirm3'] = $this->request->post['screen_confirm3'];
		} else {
			$this->data['screen_confirm3'] = $this->config->get('screen_confirm3');
		}
		if (isset($this->request->post['screen_confirm3'])) {
			$this->data['screen_confirm3'] = $this->request->post['screen_confirm3'];
		} else {
			$this->data['screen_confirm3'] = $this->config->get('screen_confirm3');
		}
		///Get Saved Reasons
		$query = $this->db->query("SELECT reason FROM `" . DB_PREFIX . "paypal_recurring_reasons` ORDER BY id ASC");
		
		$this->data['thereasons'] = array();	
		foreach ($query->rows as $reason) {
		$this->data['thereasons'][] = array('reason' => stripslashes($reason['reason']));
		}
		///Get Saved Refund Reasons
		$query = $this->db->query("SELECT reason FROM `" . DB_PREFIX . "paypal_recurring_refund_reasons` ORDER BY id ASC");
		
		$this->data['therefundreasons'] = array();	
		foreach ($query->rows as $reason) {
		$this->data['therefundreasons'][] = array('reason' => stripslashes($reason['reason']));
		}
		$this->load->model('catalog/product');
		
		//deal with recurring items
		 $this->data['value_row_trial'] = 1;
	  $this->data['value_row'] = 0;
		$this->data['recurringproducts'] = array();
		
		$recurringproducts =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON(pd.product_id = p.product_id) WHERE is_trial <> 1 AND is_recurring = '1' ORDER BY pd.product_id ASC");  
		if($recurringproducts){
         foreach($recurringproducts->rows as $row){
			 $itemdata = array();
		     $iteminfo =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "paypal_recurring_items` WHERE item_product = '".(int)$row['product_id']."' ORDER BY item_id ASC");  
              if($iteminfo){
				   foreach($iteminfo->rows as $row2){
				  $itemdata[] = array(
			
			'item_id' =>  $row2['item_id'],
			'item_period' =>  $row2['item_period'],
			'item_product' =>  $row2['item_product'],
			'item_frequency' =>  $row2['item_frequency'],
			'item_cycles' =>  $row2['item_cycles'],
			'item_status' =>  $row2['item_status']
			
			); 
		 }
			  }else{
				 $itemdata = array();  
			  }
			  
			$this->data['recurringproducts'][] = array(
			
			'recurring_product_name' =>  $row['name'],
			'recurring_product_id' =>  $row['product_id'],
			'item_data' =>  $itemdata
			
			);
			
			
		}
	
		}else{
		$this->data['recurringproducts'] = array();	
		}

      //deal with trials
	  
	    $this->data['trialproducts'] = array();
	  
	 
	  //get the products
	   $trialproducts = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON(p.product_id = pd.product_id) WHERE is_trial = '1' ORDER BY p.product_id ASC");
if ($trialproducts) {
	foreach($trialproducts->rows as $row){
		
		$this->data['trialproducts'][] = array('name' => $row['name'], 'product_id' => $row['product_id']);
	}
}else{
	$this->data['trialproducts'] = array();
	
}
		$this->data['trialinfo'] = array();
		
		$trialinfo =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "paypal_recurring_trials` WHERE trial_status = '1' ORDER BY trial_id ASC");  
		if($trialinfo){
        foreach($trialinfo->rows as $row){
			
			$this->data['trialinfo'][] = array(
			'trial_id' =>  $row['trial_id'],
			'trial_period' =>  $row['trial_period'],
			'trial_product' =>  $row['trial_product'],
			'trial_frequency' =>  $row['trial_frequency'],
			'trial_cycles' =>  $row['trial_cycles'],
			'trial_amount' =>  number_format($row['trial_amount'],2),
			'trial_status' =>  $row['trial_status']
			);
			
			
		}
		}else{
		$this->data['trialinfo'] = array();	
		}
		if (isset($this->request->post['trialinfonew'])) {
			foreach($this->request->post['trialinfonew'] as $info){
				foreach($info as $details){
					$this->data['trialinfonew'][] = array(
		    'trial_id' =>  'na',
			'trial_period' =>  $details['period'],
			'trial_product' =>  isset($details['product']) ? $details['product'] : '',
			'trial_frequency' =>  $details['frequency'],
			'trial_cycles' =>  $details['cycles'],
			'trial_amount' =>  number_format($details['amount'],2)
			
			);
				}
		}
		}else{
			$this->data['trialinfonew'] = array();	
		}
		
		$this->template = 'payment/pp_pro_recurring.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function related() {
		$this->load->model('catalog/product');
		
		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} else {
			$products = array();
		}
	
		$product_data = array();
		
		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info) {
				$product_data[] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name'],
					'model'      => $product_info['model']
				);
			}
		}
		
		
		
		$this->response->setOutput(json_encode($product_data));
	}
	 //get number of trial products
	  
		
		public function getTrialCount() {
			
	$json = array();
		$trial_count = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE is_trial = '1'");
	    $trial_product_count = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE is_trial = '1' AND status = '1'");
		
         $count_data = array();
		
			$count_data[] = array(
				'trial_count' => $trial_count->row['total'],
				'trial_product_count'       => $trial_product_count->row['total']
			
			);
	
	$this->response->setOutput(json_encode($count_data));
}
	public function deleteTrial() {
	$json = array();
	if(isset($this->request->get['trialid'])){
		$check_id = $this->db->query("SELECT trial_id FROM `" . DB_PREFIX . "paypal_recurring_trials` WHERE trial_id = '".(int)$this->request->get['trialid']."'");
		if (!$check_id->num_rows) {
	$json['error'] = "Error: There are no trials with this ID. You have somehow altered the database from another source. You will have to delete manually!";
		}else{
		//check for existing reason
$this->db->query("DELETE FROM `" . DB_PREFIX . "paypal_recurring_trials` WHERE trial_id = '".(int)$this->request->get['trialid']."'");
$json['success'] = "Success: Trial Deleted!";
		}
	}else{
	$json['error'] = "Error: There was an error with the request. Please try again later.";	
}
	$this->response->setOutput(json_encode($json));
}
public function addReason() {
	$json = array();
	if(isset($this->request->post['profilereasons'])){
		if($this->request->post['profilereasons'] == ""){
			$json['error'] = "You must type a reason!";
	}else{
		//check for existing reason
$query = $this->db->query("SELECT reason FROM `" . DB_PREFIX . "paypal_recurring_reasons` WHERE reason = '".$this->db->escape($this->request->post['profilereasons'])."'");
if ($query->num_rows) {
	$json['error'] = "Error: This reason already exists. Please type another!";
}else{
	$this->db->query("INSERT INTO `" . DB_PREFIX . "paypal_recurring_reasons` SET reason = '".$this->db->escape($this->request->post['profilereasons'])."'");
	
	$query = $this->db->query("SELECT reason FROM `" . DB_PREFIX . "paypal_recurring_reasons` ORDER BY id ASC");
		
		
		foreach ($query->rows as $reason) {
		$json[] = array('reason' => stripslashes($reason['reason']));
		}
		
	
}
	
		
	}
}
	$this->response->setOutput(json_encode($json));
}
public function addRefundReason() {
	$json = array();
	if(isset($this->request->post['profilereasons'])){
		if($this->request->post['profilereasons'] == ""){
			$json['error'] = "You must type a reason!";
	}else{
		//check for existing reason
$query = $this->db->query("SELECT reason FROM `" . DB_PREFIX . "paypal_recurring_refund_reasons` WHERE reason = '".$this->db->escape($this->request->post['profilereasons'])."'");
if ($query->num_rows) {
	$json['error'] = "Error: This reason already exists. Please type another!";
}else{
	$this->db->query("INSERT INTO `" . DB_PREFIX . "paypal_recurring_refund_reasons` SET reason = '".$this->db->escape($this->request->post['profilereasons'])."'");
	
	$query = $this->db->query("SELECT reason FROM `" . DB_PREFIX . "paypal_recurring_refund_reasons` ORDER BY id ASC");
		
		
		foreach ($query->rows as $reason) {
		$json[] = array('reason' => stripslashes($reason['reason']));
		}
		
	
}
	
		
	}
}
	$this->response->setOutput(json_encode($json));
}
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/pp_pro_recurring')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['pp_pro_recurring_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}
		if (!$this->request->post['pp_pro_recurring_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}
		if (!$this->request->post['pp_pro_recurring_signature']) {
			$this->error['signature'] = $this->language->get('error_signature');
		}
		if (!$this->request->post['pp_pro_recurring_desc']) {
			$this->error['description'] = $this->language->get('error_required');
		}
		
		if (!$this->request->post['pp_pro_recurring_billingfrequency']) {
			$this->error['frequency'] = $this->language->get('error_required');
		}
		
		//Recurring Items validation
		 if(isset($this->request->post['iteminfo'])){
		
		 
		foreach($this->request->post['iteminfo'] as $items){
		   
		   foreach($items as  $item_data){
			   if($item_data['status'] == '1'){
			   if($item_data['frequency'] == "" || $item_data['period'] == "" ){
				$this->error['warning'] = "There are required fields that you must fill out before proceeding.";   
			   }
			   }
			   
			   
		    }
		}
		 }
		//Trials Validation
		if($this->request->post['pp_pro_recurring_trial'] == "1"){
		   if(isset($this->request->post['trialinfo'])){
		
		 
		foreach($this->request->post['trialinfo'] as $trial){
		   
		   foreach($trial as  $trial_data){
			   if($trial_data['frequency'] == "" || $trial_data['amount'] == "" || $trial_data['period'] == "" || $trial_data['product'] == ""){
				$this->error['warning'] = "There are required fields in the TRIAL CONFIGURATION not yet filled out.";   
			   }
			   
			   
		    }
		}
			}
			  if(isset($this->request->post['trialinfonew'])){
		foreach($this->request->post['trialinfonew'] as $trial){
		   
		   foreach($trial as  $trial_data){
			   if($trial_data['frequency'] == "" || $trial_data['amount'] == "" || $trial_data['period'] == "" || $trial_data['product'] == ""){
				$this->error['warning'] = "There are required fields in the TRIAL CONFIGURATION not yet filled out.";   
			   }
		   }
		   }
		    }
		}
		
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>