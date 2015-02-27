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
class ControllerCheckoutRecurringSuccessContinue extends Controller { 
	public function index() { 	
		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();
            unset($this->session->data['recurring']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		}	
									   
		$this->language->load('checkout/success');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['breadcrumbs'] = array(); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 
		
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
				
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
					
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/success'),
        	'text'      => $this->language->get('text_success'),
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		if ($this->customer->isLogged()) {
			 if($this->config->get('screen_confirm1')){
			$this->data['text_custom_message'] = html_entity_decode($this->config->get('screen_confirm1'), ENT_QUOTES, 'UTF-8');
			}else{
			$this->data['text_custom_message'] = "";	
				
			} 
    		$this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} else {
			 if($this->config->get('screen_confirm1')){
			$this->data['text_custom_message'] = html_entity_decode($this->config->get('screen_confirm1'), ENT_QUOTES, 'UTF-8');
			}else{
			$this->data['text_custom_message'] = "";	
				
		} 
    		$this->data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}
		
    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/recurring_success_continue.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/recurring_success_continue.tpl';
		} else {
			$this->template = 'default/template/common/recurring_success_continue.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'			
		);
				
		$this->response->setOutput($this->render());
  	}
}
?>