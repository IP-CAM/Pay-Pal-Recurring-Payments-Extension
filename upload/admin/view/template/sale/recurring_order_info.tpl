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
?>
<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">

    <div class="heading">
      <h1><img src="view/image/recurring.jpg" alt="" /> <?php echo $heading_title; ?></h1>
			 <div class="buttons"><a href="<?php echo $jump;?>" class="button"><?php echo $jump_button; ?></a><a onclick="window.open('<?php echo $invoice; ?>');" class="button"><?php echo $button_invoice; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <?php if ($recurring && $paypal_is_original_order == '0') { ?>
    <div style="padding:15px;"> 
     This recurring order has a Parent Order. To view the parent order click <a href="<?php echo $parentorder;?>">HERE</a>
     </div><?php }?>
    <div class="content">
    
      <div class="vtabs"><a href="#tab-order"><?php echo $tab_order; ?></a><a href="#tab-payment"><?php echo $tab_payment; ?></a>
        <?php if ($shipping_method) { ?>
        <a href="#tab-shipping"><?php echo $tab_shipping; ?></a>
        <?php } ?>
        <a href="#tab-product"><?php echo $tab_product; ?></a><a href="#tab-history"><?php echo $tab_order_history; ?></a>
         <?php if ($recurring && $paypal_is_original_order == '1') { ?>
        <a href="#tab-recurring"><?php echo $recurring_tab; ?></a> 
        <a href="#tab-recurring-edit"><?php echo $edit_recurring_tab; ?></a>
           <?php } ?>
         
        <a href="#tab-recurring-refund"><?php echo $refund_recurring_tab; ?></a>
      
        <?php if ($maxmind_id) { ?>
        <a href="#tab-fraud"><?php echo $tab_fraud; ?></a>
        <?php } ?>
      </div>
      <div id="tab-order" class="vtabs-content">
        <table class="form">
          <tr>
            <td><?php echo $text_order_id; ?></td>
            <td>#<?php echo $order_id; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_invoice_no; ?></td>
            <td><?php if ($invoice_no) { ?>
              <?php echo $invoice_no; ?>
              <?php } else { ?>
              <span id="invoice"><b>[</b> <a id="invoice-generate"><?php echo $text_generate; ?></a> <b>]</b></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $text_store_name; ?></td>
            <td><?php echo $store_name; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_store_url; ?></td>
            <td><a onclick="window.open('<?php echo $store_url; ?>');"><u><?php echo $store_url; ?></u></a></td>
          </tr>
          <?php if ($customer) { ?>
          <tr>
            <td><?php echo $text_customer; ?></td>
            <td><a href="<?php echo $customer; ?>"><?php echo $firstname; ?> <?php echo $lastname; ?></a></td>
          </tr>
          <?php } else { ?>
          <tr>
            <td><?php echo $text_customer; ?></td>
            <td><?php echo $firstname; ?> <?php echo $lastname; ?></td>
          </tr>
          <?php } ?>
          <?php if ($customer_group) { ?>
          <tr>
            <td><?php echo $text_customer_group; ?></td>
            <td><?php echo $customer_group; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_email; ?></td>
            <td><?php echo $email; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_telephone; ?></td>
            <td><?php echo $telephone; ?></td>
          </tr>
          <?php if ($fax) { ?>
          <tr>
            <td><?php echo $text_fax; ?></td>
            <td><?php echo $fax; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_total; ?></td>
            <td><?php if ($recurring && $paypal_is_original_order == '1'){ if($paypal_recurring_amount != "N/A"){ echo $paypal_recurring_amount; } elseif($paypal_recurring_regularamount != "N/A"){ echo $paypal_recurring_regularamount;}}else{  echo $total; }?>
              <?php if ($credit && $customer) { ?>
              <?php if (!$credit_total) { ?>
              <span id="credit"><b>[</b> <a id="credit-add"><?php echo $text_credit_add; ?></a> <b>]</b></span>
              <?php } else { ?>
              <span id="credit"><b>[</b> <a id="credit-remove"><?php echo $text_credit_remove; ?></a> <b>]</b></span>
              <?php } ?>
              <?php } ?></td>
          </tr>
          <?php if ($reward && $customer) { ?>
          <tr>
            <td><?php echo $text_reward; ?></td>
            <td><?php echo $reward; ?>
              <?php if (!$reward_total) { ?>
              <span id="reward"><b>[</b> <a id="reward-add"><?php echo $text_reward_add; ?></a> <b>]</b></span>
              <?php } else { ?>
              <span id="reward"><b>[</b> <a id="reward-remove"><?php echo $text_reward_remove; ?></a> <b>]</b></span>
              <?php } ?></td>
          </tr>
          <?php } ?>
          <?php if ($order_status) { ?>
          <tr>
            <td><?php echo $text_order_status; ?></td>
            <td id="order-status"><?php echo $order_status; ?></td>
          </tr>
          <?php } ?>
          <?php if ($comment) { ?>
          <tr>
            <td><?php echo $text_comment; ?></td>
            <td><?php echo $comment; ?></td>
          </tr>
          <?php } ?>
          <?php if ($affiliate) { ?>
          <tr>
            <td><?php echo $text_affiliate; ?></td>
            <td><a href="<?php echo $affiliate; ?>"><?php echo $affiliate_firstname; ?> <?php echo $affiliate_lastname; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $text_commission; ?></td>
            <td><?php echo $commission; ?>
              <?php if (!$commission_total) { ?>
              <span id="commission"><b>[</b> <a id="commission-add"><?php echo $text_commission_add; ?></a> <b>]</b></span>
              <?php } else { ?>
              <span id="commission"><b>[</b> <a id="commission-remove"><?php echo $text_commission_remove; ?></a> <b>]</b></span>
              <?php } ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip) { ?>
          <tr>
            <td><?php echo $text_ip; ?></td>
            <td><?php echo $ip; ?></td>
          </tr>
          <?php } ?>
          <?php if ($forwarded_ip) { ?>
          <tr>
            <td><?php echo $text_forwarded_ip; ?></td>
            <td><?php echo $forwarded_ip; ?></td>
          </tr>
          <?php } ?>
          <?php if ($user_agent) { ?>
          <tr>
            <td><?php echo $text_user_agent; ?></td>
            <td><?php echo $user_agent; ?></td>
          </tr>
          <?php } ?>
          <?php if ($accept_language) { ?>
          <tr>
            <td><?php echo $text_accept_language; ?></td>
            <td><?php echo $accept_language; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_date_added; ?></td>
            <td><?php echo $date_added; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_date_modified; ?></td>
            <td><?php echo $date_modified; ?></td>
          </tr>
        </table>
      </div>
      <div id="tab-payment" class="vtabs-content">
        <table class="form">
          <tr>
            <td><?php echo $text_firstname; ?></td>
            <td><?php echo $payment_firstname; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_lastname; ?></td>
            <td><?php echo $payment_lastname; ?></td>
          </tr>
          <?php if ($payment_company) { ?>
          <tr>
            <td><?php echo $text_company; ?></td>
            <td><?php echo $payment_company; ?></td>
          </tr>
          <?php } ?>
          <?php if ($payment_company_id) { ?>
          <tr>
            <td><?php echo $text_company_id; ?></td>
            <td><?php echo $payment_company_id; ?></td>
          </tr>
          <?php } ?>          
          <?php if ($payment_tax_id) { ?>
          <tr>
            <td><?php echo $text_tax_id; ?></td>
            <td><?php echo $payment_tax_id; ?></td>
          </tr>
          <?php } ?>            
          <tr>
            <td><?php echo $text_address_1; ?></td>
            <td><?php echo $payment_address_1; ?></td>
          </tr>
          <?php if ($payment_address_2) { ?>
          <tr>
            <td><?php echo $text_address_2; ?></td>
            <td><?php echo $payment_address_2; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_city; ?></td>
            <td><?php echo $payment_city; ?></td>
          </tr>
          <?php if ($payment_postcode) { ?>
          <tr>
            <td><?php echo $text_postcode; ?></td>
            <td><?php echo $payment_postcode; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_zone; ?></td>
            <td><?php echo $payment_zone; ?></td>
          </tr>
          <?php if ($payment_zone_code) { ?>
          <tr>
            <td><?php echo $text_zone_code; ?></td>
            <td><?php echo $payment_zone_code; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_country; ?></td>
            <td><?php echo $payment_country; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_payment_method; ?></td>
            <td><?php echo $payment_method; ?></td>
          </tr>
        </table>
      </div>
      <?php if ($shipping_method) { ?>
      <div id="tab-shipping" class="vtabs-content">
        <table class="form">
          <tr>
            <td><?php echo $text_firstname; ?></td>
            <td><?php echo $shipping_firstname; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_lastname; ?></td>
            <td><?php echo $shipping_lastname; ?></td>
          </tr>
          <?php if ($shipping_company) { ?>
          <tr>
            <td><?php echo $text_company; ?></td>
            <td><?php echo $shipping_company; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_address_1; ?></td>
            <td><?php echo $shipping_address_1; ?></td>
          </tr>
          <?php if ($shipping_address_2) { ?>
          <tr>
            <td><?php echo $text_address_2; ?></td>
            <td><?php echo $shipping_address_2; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_city; ?></td>
            <td><?php echo $shipping_city; ?></td>
          </tr>
          <?php if ($shipping_postcode) { ?>
          <tr>
            <td><?php echo $text_postcode; ?></td>
            <td><?php echo $shipping_postcode; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_zone; ?></td>
            <td><?php echo $shipping_zone; ?></td>
          </tr>
          <?php if ($shipping_zone_code) { ?>
          <tr>
            <td><?php echo $text_zone_code; ?></td>
            <td><?php echo $shipping_zone_code; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_country; ?></td>
            <td><?php echo $shipping_country; ?></td>
          </tr>
          <?php if ($shipping_method) { ?>
          <tr>
            <td><?php echo $text_shipping_method; ?></td>
            <td><?php echo $shipping_method; ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      <?php } ?>
      
      <div id="tab-product" class="vtabs-content">
    
      
   <?php if ($recurring && $paypal_is_original_order == '1') { ?>
   Initial Recurring Orders do not carry product data.
   <?php }?>
        <table class="list" <?php if ($recurring && $paypal_is_original_order == '1') { ?>style="display:none;" <?php }?>>
          <thead>
            <tr>
              <td class="left"><?php echo $column_product; ?></td>
              <td class="left"><?php echo $column_model; ?></td>
              <td class="right"><?php echo $column_quantity; ?></td>
              <td class="right"><?php echo $column_price; ?></td>
              <td class="right"><?php echo $column_total; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) { ?>
             
            <tr>
              <td class="left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                <?php foreach ($product['option'] as $option) { ?>
                <br />
                <?php if ($option['type'] != 'file') { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                <?php } else { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
                <?php } ?>
                <?php } ?></td>
              <td class="left"><?php echo $product['model']; ?></td>
              <td class="right"><?php echo $product['quantity']; ?></td>
              <td class="right"><?php echo $product['price']; ?></td>
              <td class="right"><?php echo $product['total']; ?></td>
            </tr>
            <?php } ?>
            <?php foreach ($vouchers as $voucher) { ?>
            <tr>
              <td class="left"><a href="<?php echo $voucher['href']; ?>"><?php echo $voucher['description']; ?></a></td>
              <td class="left"></td>
              <td class="right">1</td>
              <td class="right"><?php echo $voucher['amount']; ?></td>
              <td class="right"><?php echo $voucher['amount']; ?></td>
            </tr>
            <?php } ?>
          </tbody>
          <?php foreach ($totals as $totals) { ?>
          <tbody id="totals">
            <tr>
              <td colspan="4" class="right"><?php echo $totals['title']; ?>:</td>
              <td class="right"><?php echo $totals['text']; ?></td>
            </tr>
          </tbody>
          <?php } ?>
        </table>
        <?php if ($downloads) { ?>
        <h3><?php echo $text_download; ?></h3>
        <table class="list">
          <thead>
            <tr>
              <td class="left"><b><?php echo $column_download; ?></b></td>
              <td class="left"><b><?php echo $column_filename; ?></b></td>
              <td class="right"><b><?php echo $column_remaining; ?></b></td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($downloads as $download) { ?>
            <tr>
              <td class="left"><?php echo $download['name']; ?></td>
              <td class="left"><?php echo $download['filename']; ?></td>
              <td class="right"><?php echo $download['remaining']; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
           <?php }?>
        
      </div>
      <div id="tab-history" class="vtabs-content">
        <div id="history"></div>
        <table class="form">
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="order_status_id">
                <?php foreach ($order_statuses as $order_statuses) { ?>
                <?php if ($order_statuses['order_status_id'] == $order_status_id) { ?>
                <option value="<?php echo $order_statuses['order_status_id']; ?>" selected="selected"><?php echo $order_statuses['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_statuses['order_status_id']; ?>"><?php echo $order_statuses['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_notify; ?></td>
            <td><input type="checkbox" name="notify" value="1" /></td>
          </tr>
          <tr>
            <td><?php echo $entry_comment; ?></td>
            <td><textarea name="comment" cols="40" rows="8" style="width: 99%"></textarea>
              <div style="margin-top: 10px; text-align: right;"><a id="button-history" class="button"><?php echo $button_add_history; ?></a></div></td>
          </tr>
        </table>
      </div>
         <?php if ($recurring && $paypal_is_original_order == '1') { ?>
			 
      <div id="tab-recurring" class="vtabs-content">
	
 <div style='display:none'>
			<div id='inline-content-list' style='padding:10px; background:#fff;height:400px;overflow:auto;'>
           <div style= "float:left; font-size:18px;">Recurring Orders <br /><small>Profile ID: (<?php echo $product['profile_id']; ?>)</small></div>
            <div style= "float:right; font-size:12px;">Profile Status<br /> <small>(<?php if($order_status == 'Active'){ echo '<span style="color:#090;">' . $order_status . '</span>'; }elseif($order_status == 'Suspended'){ echo '<span style="color:#F60;">' . $order_status . '</span>' ; }else{ echo $order_status; } ?>)</small></div>
            <div style="clear:both;margin-bottom:30px;"></div>
  <?php if ($product['parent_orders_list']) { ?>
  
  <?php foreach ($product['parent_orders_list'] as $parentorder) { ?>
  <div class="order-list">
   
    <div class="order-id"><b>Order ID:</b> <?php echo $parentorder['order_id']; ?> <br />
     <b>Profile ID:</b> <?php echo $parentorder['profile_id']; ?><br />
   <b>Paypal Transaction ID:</b> <?php echo $parentorder['paypal_transaction_id']; ?></div>
    <div class="order-status"><a href="<?php echo $parentorder['manage_child'];?>">[ View Order ]</a></div>
    <div class="order-content">
      <div><b>Date Added</b> <?php echo $parentorder['date_added']; ?><br />
      <?php if($parentorder['products']){?>
        <b>Products</b> <?php echo $parentorder['products']; ?><?php }?></div>
      <div><b>Name: </b> <?php echo $parentorder['customer']; ?><br />
      <b>Email: </b> <?php echo $parentorder['email']; ?><br />
        <b>Order Total</b> <?php echo $parentorder['total_gross']; ?></div>
        
    </div>
  </div>
  <?php } ?>
   <?php } ?>
            </div>
            </div>
<div style=" background-color:#F5F5F5; padding:15px;  color:#000; width:600px;border:#CCC 1px solid;"><table width="550"><tr><td align="left"><strong>Paypal Transaction ID:</strong> <i><?php echo $paypal_transaction_id;?></i> <br /><br /> This profile currently has <strong><?php echo $product['parent_orders']; ?></strong> recurring orders. <?php if($product['parent_orders'] > 0){?><a id="inline-list"  href="#inline-content-list"><img src="view/image/log_small.png" style="position:relative; top:2px;" alt="View Recent" /> <small>(view history)</small></a><?php }?></td><td align="right"> <span style=" font-weight:bold; font-size:24px;"><?php echo $product['total_gross'];?></span></td></tr></table></div>
         
        <table class="form2">
		 <tr>
            <td><strong><?php echo $recurring_profile_id;?></strong></td>
            <td align="left"><?php echo $recurring;?></td>
             <td><strong><?php echo $recurring_profile_desc;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_desc;?></td>
          </tr>
           <tr>
		   
            <td><strong><?php echo $recurring_profile_status;?></strong></td>
            <td align="left"><?php if($paypal_recurring_status == "Active"){ ?><span style="color:#090;"><?php echo $paypal_recurring_status;?></span><?php }elseif($paypal_recurring_status == "Cancelled"){ ?><span style="color:#F00;">Canceled</span><?php }elseif($paypal_recurring_status == "Suspended"){?><span style="color:#F60;">Suspended</span><?php }else{?><span style="color:#F90;"><?php echo $paypal_recurring_status;}?></span></td>
             <td><strong><?php echo $recurring_trial_status;?></strong></td>
            <td align="left"><?php if($paypal_recurring_trial_status == 1 && $paypal_recurring_status != "Suspended"){ ?><span style="color:#090;"><?php echo "Active";?></span><?php }elseif($paypal_recurring_status == "Cancelled"){ ?><span style="color:#F00;">Canceled</span><?php }elseif($paypal_recurring_status == "Suspended"){?><span style="color:#F60;">Suspended</span><?php }else{ echo "Innactive";}?></td>
          </tr>
		   <tr>
            <td><strong><?php echo $recurring_profile_aggtotal;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_aggregatedtotal;?></td>
             <td><strong><?php echo $recurring_profile_startdate;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_startdate;?></td>
          </tr>
           <tr>
            <td><strong><?php echo $recurring_profile_subscribename;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_subscribername;?></td>
             <td><strong><?php echo $recurring_profile_maxfailed;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_maxfailedpayments;?></td>
          </tr>
		   <tr>		
            <td><strong><?php echo $recurring_profile_billingperiod;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_profile_billingperiod;?></td>
             <td><strong><?php echo $recurring_profile_regularbillingperiod;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_regularbillingperiod;?></td>
          </tr>
           <tr>
            <td><strong><?php echo $recurring_profile_billingfrequency;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_billingfrequency;?></td>
             <td><strong><?php echo $recurring_profile_regularbillingfrequency;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_regularbillingfrequency;?></td>
          </tr>
		    <tr>	
			 <td><strong><?php echo $recurring_profile_shippingamt;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_shippingamount;?></td>
             <td><strong><?php echo $recurring_profile_regularshipingamt;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_regularshippingamount;?></td>
          </tr>
           <tr>
            <td><strong><?php echo $recurring_profile_taxamt;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_taxamount;?></td>
             <td><strong><?php echo $recurring_profile_regulartaxamt;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_regulartaxamount;?></td>
          </tr>
		   <tr>	
		
			 <td><strong><?php echo $recurring_profile_totalbillingcycles;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_totalbillingcycles;?></td>
             <td><strong><?php echo $recurring_profile_regulartotalbillingcycles;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_regulartotalbillingcycles;?></td>
          </tr>
           <tr>
            <td><strong><?php echo $recurring_profile_amt;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_amount;?></td>
             <td><strong><?php echo $recurring_profile_regularamt;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_regularamount;?></td>
          </tr>
		   <tr>	
		
		    <td><strong><?php echo $recurring_profile_autobill;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_autobillamount;?></td>
             <td><strong><?php echo $recurring_profile_nextbilldate;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_nextbilldate;?></td>
          </tr>
           <tr>
            <td><strong><?php echo $recurring_profile_cyclescompleted;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_cyclescompleted;?></td>
             <td><strong><?php echo $recurring_profile_cyclesremaining;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_cyclesremaining;?></td>
          </tr>
		   <tr>	
		  
		    <td><strong><?php echo $recurring_profile_failedcount;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_failedpaymentcount;?></td>
             <td><strong><?php echo $recurring_profile_lastpaymentdate;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_lastpaymentdate;?></td>
          </tr>
           <tr>
            <td><strong><?php echo $recurring_profile_lastpaymentamount;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_lastpaymentamount;?></td>
             <td><strong><?php echo $recurring_profile_outstanding;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_outstandingbalance;?></td>
          </tr>
		    <tr>	
	
		    <td><strong><?php echo $recurring_profile_cctype;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_cctype;?></td>
             <td><strong><?php echo $recurring_profile_ccnumber;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_ccnumber;?></td>
          </tr>
           <tr>
            <td><strong><?php echo $recurring_profile_ccexpire;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_ccexpire;?></td>
             <td><strong><?php echo $recurring_profile_maestrostartdate;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_maestrostartdate;?></td>
          </tr>
		    <tr>
            <td><strong><?php echo $recurring_profile_maestronumber;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_maestronumber;?></td>
			<td><strong><?php echo $recurring_profile_reference;?></strong></td>
            <td align="left"><?php echo $paypal_recurring_reference;?></td>
          </tr>
		
		</table>
		
      </div>
         <?php if ($recurring && $paypal_is_original_order == '1') { ?>
	   <div id="tab-recurring-edit" class="vtabs-content">
	   
			   <table class="form3">
		 <tr>
            <td><strong><?php echo $update_recurring_note;?></strong><br><small>(optional - reason for update)</small></td>
            <td align="left"><textarea name="update_recurring_note" cols="30" rows="3"></textarea></td> <td><strong><?php echo $recurring_profile_desc;?></strong></td>
            <td align="left"><textarea name="update_recurring_desc" cols="30" rows="3"><?php echo $paypal_recurring_desc;?></textarea></td>
			</tr>
		
		  <tr>
              <td><strong><?php echo $recurring_profile_subscribename;?></strong></td>
            <td align="left"><input type="text" value="<?php echo $paypal_recurring_subscribername;?>" name="update_recurring_subscribename"></td>  <td><strong><?php echo $recurring_profile_reference;?></strong></td>
            <td align="left"><input type="text" value="<?php echo $paypal_recurring_reference;?>" onkeypress="return isNumberKey(event);"  name="update_recurring_reference"></td>
          </tr>
		 
		   <tr>
              <td><strong><?php echo $update_recurring_additionalbillingcycles;?></strong></td>
            <td align="left"><input type="text" value="" onkeypress="return isNumberKey(event);" name="update_recurring_additionalbillingcycles"></td>
             <td><strong><?php echo $recurring_profile_email;?></strong></td>
            <td align="left"><input type="text" value="<?php echo $paypal_recurring_email;?>" name="update_recurring_email"></td>   
          </tr>
		 
		  <tr>
		  
		   <td><strong><?php echo $recurring_profile_regularshipingamt;?></strong></td>
            <td align="left"><input type="text" value="<?php echo str_replace("$" , "", $paypal_recurring_regularshippingamount);?>" onkeypress="return isNumberKeyWithDecimal(event);" name="update_recurring_shippingamt"></td> <td><strong><?php echo $recurring_profile_regulartaxamt;?></strong></td>
            <td align="left"><input type="text" value="<?php echo str_replace("$" , "", $paypal_recurring_regulartaxamount);?>" onkeypress="return isNumberKeyWithDecimal(event);" name="update_recurring_taxamt"></td>
		 </tr>
		
		  <tr>
		  
		   <td><strong><?php echo $recurring_profile_outstanding;?></strong></td>
            <td align="left"><input type="text" value="<?php echo str_replace("$" , "", $paypal_recurring_outstandingbalance);?>" onkeypress="return isNumberKeyWithDecimal(event);" name="update_recurring_outstanding"><br><small><?php echo $update_recurring_profile_outstanding_text;?></small></td>  <td><strong><?php echo $recurring_profile_autobill; ?></strong></td>
            <td align="left"><select name="update_recurring_autobill" id="pp_pro_recurring_autobill">
               <option value="" selected><?php echo "--Select One--"; ?></option>
                <option value="AddToNextBilling"><?php echo $text_enabled; ?></option>
                <option value="NoAutoBill"><?php echo $text_disabled; ?></option>
				
              
              </select></td>
		 </tr>
		
		  <tr>
		  
		   <td><strong><?php echo $recurring_profile_maxfailed;?></strong></td>
            <td align="left"><input type="text" value="<?php echo $paypal_recurring_maxfailedpayments;?>" onkeypress="return isNumberKey(event);"  name="update_recurring_maxfailed"></td>  <td><strong><?php echo $recurring_profile_startdate;?></strong><br><small><?php echo $mustnot;?></small></td>
            <td align="left"><input type="text"  value="" class="date" name="update_recurring_startdate"><br><small>Current Start Date: <?php echo $paypal_recurring_startdate;?></small></td>
		 </tr>
		  <tr>
		     <td><strong><?php echo $recurring_profile_totalbillingcycles;?></strong></td>
            <td align="left"><input type="text" value="<?php echo $paypal_recurring_totalbillingcycles;?>" onkeypress="return isNumberKey(event);"  name="update_recurring_totalbillingcycles"></td> <td><strong><?php echo $recurring_profile_regulartotalbillingcycles;?></strong></td>
            <td align="left"><input type="text" value="<?php echo $paypal_recurring_regulartotalbillingcycles;?>" onkeypress="return isNumberKey(event);"  name="update_recurring_regulartotalbillingcycles"></td>
		 </tr>
		  <tr>
		      <td><strong><?php echo $recurring_profile_amt;?></strong></td>           
		    <td align="left"><input type="text" value="<?php echo str_replace("$" , "", $paypal_recurring_amount);?>" onkeypress="return isNumberKeyWithDecimal(event);"  name="update_recurring_amt"></td>  
			
			<td><strong><?php echo $recurring_profile_regularamt;?></strong></td>           
		    <td align="left"><input type="text" value="<?php echo str_replace("$" , "", $paypal_recurring_regularamount);?>" onkeypress="return isNumberKeyWithDecimal(event);"  name="update_recurring_regularamt"></td>
		 </tr>
        
         
         </table>
         <h3 style="color:#003A88;">Credit Card Details (optional)</h3>
        <table class="form3">
		  <tr>
      <td><strong><?php echo $entry_cc_type; ?></strong></td>
      <td><select name="update_recurring_cc_type" id="update_recurring_cc_type">
	    <option value="" selected><?php echo "--Select One--"; ?></option>
          <?php foreach ($cards as $card) { ?>
          <option value="<?php echo $card['value']; ?>"><?php echo $card['text']; ?></option>
          <?php } ?>
        </select></td><td><strong><?php echo $entry_cc_number; ?></strong></td>
      <td><input type="text" name="update_recurring_cc_number" value="" /></td>
    </tr>
   <tr>
      <td><strong><?php echo $entry_cc_start_date; ?></strong><br>
        <?php echo $text_start_date; ?></td>
		
      <td><select name="update_recurring_cc_start_date_month">
	   <option value="" selected><?php echo "Choose"; ?></option>
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
        /
        <select name="update_recurring_cc_start_date_year">
		 <option value="" selected><?php echo "Choose"; ?></option>
          <?php foreach ($year_valid as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select></td> <td><strong><?php echo $entry_cc_expire_date; ?></strong></td>
      <td><select name="update_recurring_cc_expire_date_month" id="update_recurring_cc_expire_date_month">
	   <option value="" selected><?php echo "Choose"; ?></option>
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
        /
        <select name="update_recurring_cc_expire_date_year" id="update_recurring_cc_expire_date_year">
		 <option value="" selected><?php echo "Choose"; ?></option>
          <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select></td> 
    </tr>
    <tr>
     
     <td><strong><?php echo $entry_cc_cvv2; ?></strong></td> <td><input type="text" name="update_recurring_cc_cvv2" value="" size="3" /></td><td><strong><?php echo $entry_cc_issue; ?></strong></td>
      <td><input type="text" name="update_recurring_cc_issue" value="" size="1" />
        <?php echo $text_issue; ?></td>
    </tr>
  
   
		  </table>
		   <div style='display:none'>
			<div id='inline-content' style='padding:10px; background:#fff;height:500px;'>
			
            <h3>Manage Profile Status</h3>
			<?php if($paypal_recurring_status != "Cancelled" || $paypal_recurring_status != "Expired"){?>
			  <table class="form4">
          <tr>
          <td><strong>Alter Trial Status:</strong> 
            <span id="changestatus"><select name="status_type" id="status_type">               
              
			   <?php foreach($statuses as $status){
			   if($status['name'] == "Canceled"){?>
                <option value="<?php echo $status['order_status_id'];?>">Cancel</option>
				<?php }?>
				 <?php if($status['name'] == "Active"){?>
                <option value="<?php echo $status['order_status_id'];?>">Re-activate</option>
				<?php }?>
                <?php if($status['name'] == "Suspended"){?>
                <option value="<?php echo $status['order_status_id'];?>">Suspend</option>
				<?php }?>
					<?php }?>
              </select>
			  <p>
			   <?php echo $text_reactivate;?>
              <?php echo $text_suspend;?>
             <?php echo $text_cancel;?>
		</p>
			   </span>
             </td></tr><tr>
          <td><strong>Reason For Change:</strong> <small>(Will show in order history log)</small> <br /><br />
           <span id="changestatus">
            <select name="preload"  id="preload" onChange="preload();" style="width: 600px;">
			
			<?php foreach($reasons as $reason){?>
                <option value="<?php echo html_entity_decode($reason['reason'], ENT_QUOTES, 'UTF-8');?>" selected="selected"><?php echo $reason['reason'];?></option>
                     <?php }?>
					 
              </select>
			  <br /><br /><textarea name="reason" id="reason" cols="40" rows="3" style="width: 500px; font-family:Arial, Helvetica, sans-serif; font-size:14px;" ></textarea>
			    <p>Notify Customer? <input type="checkbox" name="notify-status" value="1" /></p>
			  </span>
            
            </td>
          </tr>
          </tr>
          </table>
		 
      
			<a onClick="status();" id="changeStatus" class="button">Execute</a>
			 <?php }else{?>
		  <span style="color:#F00;">This profile may not be altered. It already is canceled/expired.</span>
		  
		  <?php }?>
			</div>
            </div>
       
		  <a id="update" class="button">Save</a> <a id='inline' class="button" href="#inline-content">Change Recurring Profile Status</a>
		<script type="text/javascript"><!--
		$("#inline").colorbox({inline:true, width:"50%"});
		$("#inline-list").colorbox({inline:true, width:"50%"});
$('.colorbox').colorbox({
	overlayClose: true,
	opacity: 0.5
});
//--></script> 
		 <script type="text/javascript"><!--
		 function preload(){
		 $("#reason").val($("#preload").val());
		 
		 }
		 function status() {
	 if (!confirm ('Warning: This will change the status. If you cancel a trial in cannot be re-activated. Continue?')) {
                return false;
            
        }else{
			
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/recurring_order/changeSingleStatus&token=<?php echo $token; ?>&notify-status=' + encodeURIComponent($('input[name=\'notify-status\']').attr('checked') ? 1 : 0)+ '&profile_id=<?php echo $recurring;?>&order_id=<?php echo $this->request->get['order_id'];?>',
		dataType: 'json',
		data: $('#changestatus input[type=\'checkbox\']:checked, #changestatus select, #changestatus textarea'),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#changeStatus').attr('disabled', true);
			$('#changeStatus').after('<div class="attention"><img src="view/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#changeStatus').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(json) {
		      
		if (json['error']) {
				alert(json['error']); 

			}
		if(json['redirect']){
		location = json['redirect'];
			}	
			
			
		}
	});
}
}
			  $('#pp_pro_recurring_autobill').val("<?php echo $paypal_recurring_autobillamount;?>");
			  $('#update_recurring_cc_type').val("<?php echo strtoupper($paypal_recurring_cctype);?>");
			  $('#update_recurring_cc_expire_date_month').val("<?php echo substr($paypal_recurring_ccexpire, -6, 2);?>");
			  $('#update_recurring_cc_expire_date_year').val("<?php echo substr($paypal_recurring_ccexpire, -4);?>");
			
			 //--></script> 
			  </div>
			 
      <?php } ?>
           <?php }?>
            <div id="tab-recurring-refund" class="vtabs-content">
          <div id="triumph">
        <p><strong>Transaction ID:</strong> <?php if($tid != ""){ echo $tid; }else{ echo "There was no transaction for this order";}?> </p>
        
        <p>Refund Amount: <input name="refundtotal" type="text"  onkeypress="return isNumberKeyWithDecimal(event);" value="" size="20"/> <span id="ex"><small>ex. 34.95, 102.50, 0.50</small></span></p>
          <p>Reason For Refund / Notes</p>
           <p> <select name="refundpreload"  id="refundpreload" onChange="preloadrefund();">
               <?php foreach($therefundreasons as $reason){?>
                <option value="<?php echo html_entity_decode($reason['reason'], ENT_QUOTES, 'UTF-8');?>" selected="selected"><?php echo $reason['reason'];?></option>
                     <?php }?>
					 
              </select>
                  
                  
              </select><br /><br /><textarea name="refundnotes" id="refundnotes"cols="40" rows="8" style="width: 99%"></textarea></p>
              <p>
            <?php echo $entry_notify; ?>: <input type="checkbox" name="refundnotify" value="1" />
         
        
      
        <p><a id="performrefund" class="button"><span>Apply Refund</span></a></p>
       
         <script type="text/javascript"><!--
		 function isNumberKeyWithDecimal(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
		return false;
	} else {
		return true;
	}
}
function preloadrefund() {
	
	
	$('#refundnotes').val($('#refundpreload').val());
}


$('#performrefund').live('click', function() {

		
            if (!confirm ('Warning: This will refund the customers credit card. This cannot be undone. Are you sure?')) {
                return false;
            
        }else{
			$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/recurring_order/refund&token=<?php echo $token; ?>&order_id='+encodeURIComponent(<?php echo $order_id; ?>)+'&refundnotify=' + encodeURIComponent($('input[name=\'refundnotify\']').attr('checked') ? 1 : 0) + '',
		dataType: 'json',
		data: $('#triumph input[type=\'text\'],#triumph textarea'),
		beforeSend: function() {
			$('#performrefund').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
			
			if (json['error']) {
				alert(json['error']);
			}
			
			if (json['success']) {
				$('#triumph').before('<div class="success">' + json['success'] + '</div>');
				
			}
		}
	});	
		}
   									
												
	});	
	//--></script> 
    </div>
            </div>
      <?php if ($maxmind_id) { ?>
      <div id="tab-fraud" class="vtabs-content">
        <table class="form">
          <?php if ($country_match) { ?>
          <tr>
            <td><?php echo $text_country_match; ?></td>
            <td><?php echo $country_match; ?></td>
          </tr>
          <?php } ?>
          <?php if ($country_code) { ?>
          <tr>
            <td><?php echo $text_country_code; ?></td>
            <td><?php echo $country_code; ?></td>
          </tr>
          <?php } ?>
          <?php if ($high_risk_country) { ?>
          <tr>
            <td><?php echo $text_high_risk_country; ?></td>
            <td><?php echo $high_risk_country; ?></td>
          </tr>
          <?php } ?>
          <?php if ($distance) { ?>
          <tr>
            <td><?php echo $text_distance; ?></td>
            <td><?php echo $distance; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_region) { ?>
          <tr>
            <td><?php echo $text_ip_region; ?></td>
            <td><?php echo $ip_region; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_city) { ?>
          <tr>
            <td><?php echo $text_ip_city; ?></td>
            <td><?php echo $ip_city; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_latitude) { ?>
          <tr>
            <td><?php echo $text_ip_latitude; ?></td>
            <td><?php echo $ip_latitude; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_longitude) { ?>
          <tr>
            <td><?php echo $text_ip_longitude; ?></td>
            <td><?php echo $ip_longitude; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_isp) { ?>
          <tr>
            <td><?php echo $text_ip_isp; ?></td>
            <td><?php echo $ip_isp; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_org) { ?>
          <tr>
            <td><?php echo $text_ip_org; ?></td>
            <td><?php echo $ip_org; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_asnum) { ?>
          <tr>
            <td><?php echo $text_ip_asnum; ?></td>
            <td><?php echo $ip_asnum; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_user_type) { ?>
          <tr>
            <td><?php echo $text_ip_user_type; ?></td>
            <td><?php echo $ip_user_type; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_country_confidence) { ?>
          <tr>
            <td><?php echo $text_ip_country_confidence; ?></td>
            <td><?php echo $ip_country_confidence; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_region_confidence) { ?>
          <tr>
            <td><?php echo $text_ip_region_confidence; ?></td>
            <td><?php echo $ip_region_confidence; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_city_confidence) { ?>
          <tr>
            <td><?php echo $text_ip_city_confidence; ?></td>
            <td><?php echo $ip_city_confidence; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_postal_confidence) { ?>
          <tr>
            <td><?php echo $text_ip_postal_confidence; ?></td>
            <td><?php echo $ip_postal_confidence; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_postal_code) { ?>
          <tr>
            <td><?php echo $text_ip_postal_code; ?></td>
            <td><?php echo $ip_postal_code; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_accuracy_radius) { ?>
          <tr>
            <td><?php echo $text_ip_accuracy_radius; ?></td>
            <td><?php echo $ip_accuracy_radius; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_net_speed_cell) { ?>
          <tr>
            <td><?php echo $text_ip_net_speed_cell; ?></td>
            <td><?php echo $ip_net_speed_cell; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_metro_code) { ?>
          <tr>
            <td><?php echo $text_ip_metro_code; ?></td>
            <td><?php echo $ip_metro_code; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_area_code) { ?>
          <tr>
            <td><?php echo $text_ip_area_code; ?></td>
            <td><?php echo $ip_area_code; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_time_zone) { ?>
          <tr>
            <td><?php echo $text_ip_time_zone; ?></td>
            <td><?php echo $ip_time_zone; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_region_name) { ?>
          <tr>
            <td><?php echo $text_ip_region_name; ?></td>
            <td><?php echo $ip_region_name; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_domain) { ?>
          <tr>
            <td><?php echo $text_ip_domain; ?></td>
            <td><?php echo $ip_domain; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_country_name) { ?>
          <tr>
            <td><?php echo $text_ip_country_name; ?></td>
            <td><?php echo $ip_country_name; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_continent_code) { ?>
          <tr>
            <td><?php echo $text_ip_continent_code; ?></td>
            <td><?php echo $ip_continent_code; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ip_corporate_proxy) { ?>
          <tr>
            <td><?php echo $text_ip_corporate_proxy; ?></td>
            <td><?php echo $ip_corporate_proxy; ?></td>
          </tr>
          <?php } ?>
          <?php if ($anonymous_proxy) { ?>
          <tr>
            <td><?php echo $text_anonymous_proxy; ?></td>
            <td><?php echo $anonymous_proxy; ?></td>
          </tr>
          <?php } ?>
          <?php if ($proxy_score) { ?>
          <tr>
            <td><?php echo $text_proxy_score; ?></td>
            <td><?php echo $proxy_score; ?></td>
          </tr>
          <?php } ?>
          <?php if ($is_trans_proxy) { ?>
          <tr>
            <td><?php echo $text_is_trans_proxy; ?></td>
            <td><?php echo $is_trans_proxy; ?></td>
          </tr>
          <?php } ?>
          <?php if ($free_mail) { ?>
          <tr>
            <td><?php echo $text_free_mail; ?></td>
            <td><?php echo $free_mail; ?></td>
          </tr>
          <?php } ?>
          <?php if ($carder_email) { ?>
          <tr>
            <td><?php echo $text_carder_email; ?></td>
            <td><?php echo $carder_email; ?></td>
          </tr>
          <?php } ?>
          <?php if ($high_risk_username) { ?>
          <tr>
            <td><?php echo $text_high_risk_username; ?></td>
            <td><?php echo $high_risk_username; ?></td>
          </tr>
          <?php } ?>
          <?php if ($high_risk_password) { ?>
          <tr>
            <td><?php echo $text_high_risk_password; ?></td>
            <td><?php echo $high_risk_password; ?></td>
          </tr>
          <?php } ?>
          <?php if ($bin_match) { ?>
          <tr>
            <td><?php echo $text_bin_match; ?></td>
            <td><?php echo $bin_match; ?></td>
          </tr>
          <?php } ?>
          <?php if ($bin_country) { ?>
          <tr>
            <td><?php echo $text_bin_country; ?></td>
            <td><?php echo $bin_country; ?></td>
          </tr>
          <?php } ?>
          <?php if ($bin_name_match) { ?>
          <tr>
            <td><?php echo $text_bin_name_match; ?></td>
            <td><?php echo $bin_name_match; ?></td>
          </tr>
          <?php } ?>
          <?php if ($bin_name) { ?>
          <tr>
            <td><?php echo $text_bin_name; ?></td>
            <td><?php echo $bin_name; ?></td>
          </tr>
          <?php } ?>
          <?php if ($bin_phone_match) { ?>
          <tr>
            <td><?php echo $text_bin_phone_match; ?></td>
            <td><?php echo $bin_phone_match; ?></td>
          </tr>
          <?php } ?>
          <?php if ($bin_phone) { ?>
          <tr>
            <td><?php echo $text_bin_phone; ?></td>
            <td><?php echo $bin_phone; ?></td>
          </tr>
          <?php } ?>
          <?php if ($customer_phone_in_billing_location) { ?>
          <tr>
            <td><?php echo $text_customer_phone_in_billing_location; ?></td>
            <td><?php echo $customer_phone_in_billing_location; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ship_forward) { ?>
          <tr>
            <td><?php echo $text_ship_forward; ?></td>
            <td><?php echo $ship_forward; ?></td>
          </tr>
          <?php } ?>
          <?php if ($city_postal_match) { ?>
          <tr>
            <td><?php echo $text_city_postal_match; ?></td>
            <td><?php echo $city_postal_match; ?></td>
          </tr>
          <?php } ?>
          <?php if ($ship_city_postal_match) { ?>
          <tr>
            <td><?php echo $text_ship_city_postal_match; ?></td>
            <td><?php echo $ship_city_postal_match; ?></td>
          </tr>
          <?php } ?>
          <?php if ($score) { ?>
          <tr>
            <td><?php echo $text_score; ?></td>
            <td><?php echo $score; ?></td>
          </tr>
          <?php } ?>
          <?php if ($explanation) { ?>
          <tr>
            <td><?php echo $text_explanation; ?></td>
            <td><?php echo $explanation; ?></td>
          </tr>
          <?php } ?>
          <?php if ($risk_score) { ?>
          <tr>
            <td><?php echo $text_risk_score; ?></td>
            <td><?php echo $risk_score; ?></td>
          </tr>
          <?php } ?>
          <?php if ($queries_remaining) { ?>
          <tr>
            <td><?php echo $text_queries_remaining; ?></td>
            <td><?php echo $queries_remaining; ?></td>
          </tr>
          <?php } ?>
          <?php if ($maxmind_id) { ?>
          <tr>
            <td><?php echo $text_maxmind_id; ?></td>
            <td><?php echo $maxmind_id; ?></td>
          </tr>
          <?php } ?>
          <?php if ($error) { ?>
          <tr>
            <td><?php echo $text_error; ?></td>
            <td><?php echo $error; ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('#invoice-generate').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/createinvoiceno&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('#invoice').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			$('.loading').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('#tab-order').prepend('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json.invoice_no) {
				$('#invoice').fadeOut('slow', function() {
					$('#invoice').html(json['invoice_no']);
					
					$('#invoice').fadeIn('slow');
				});
			}
		}
	});
});

$('#credit-add').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/addcredit&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#credit').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');			
		},
		complete: function() {
			$('.loading').remove();
		},			
		success: function(json) {
			$('.success, .warning').remove();
			
			if (json['error']) {
				$('.box').before('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
                $('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#credit').html('<b>[</b> <a id="credit-remove"><?php echo $text_credit_remove; ?></a> <b>]</b>');
			}
		}
	});
});

$('#credit-remove').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/removecredit&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#credit').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');			
		},
		complete: function() {
			$('.loading').remove();
		},			
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
                $('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#credit').html('<b>[</b> <a id="credit-add"><?php echo $text_credit_add; ?></a> <b>]</b>');
			}
		}
	});
});

$('#reward-add').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/addreward&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#reward').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');			
		},
		complete: function() {
			$('.loading').remove();
		},									
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
                $('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');

				$('#reward').html('<b>[</b> <a id="reward-remove"><?php echo $text_reward_remove; ?></a> <b>]</b>');
			}
		}
	});
});

$('#reward-remove').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/removereward&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#reward').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');
		},
		complete: function() {
			$('.loading').remove();
		},				
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
                $('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#reward').html('<b>[</b> <a id="reward-add"><?php echo $text_reward_add; ?></a> <b>]</b>');
			}
		}
	});
});

$('#commission-add').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/addcommission&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#commission').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');			
		},
		complete: function() {
			$('.loading').remove();
		},			
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
                $('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
                
				$('#commission').html('<b>[</b> <a id="commission-remove"><?php echo $text_commission_remove; ?></a> <b>]</b>');
			}
		}
	});
});

$('#commission-remove').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/removecommission&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#commission').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');			
		},
		complete: function() {
			$('.loading').remove();
		},			
		success: function(json) {
			$('.success, .warning').remove();
						
			if (json['error']) {
				$('.box').before('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
                $('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#commission').html('<b>[</b> <a id="commission-add"><?php echo $text_commission_add; ?></a> <b>]</b>');
			}
		}
	});
});

$('#history .pagination a').live('click', function() {
	$('#history').load(this.href);
	
	return false;
});			

$('#history').load('index.php?route=sale/recurring_order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>');

$('#button-history').live('click', function() {
	$.ajax({
		url: 'index.php?route=sale/recurring_order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'order_status_id=' + encodeURIComponent($('select[name=\'order_status_id\']').val()) + '&notify=' + encodeURIComponent($('input[name=\'notify\']').attr('checked') ? 1 : 0) + '&append=' + encodeURIComponent($('input[name=\'append\']').attr('checked') ? 1 : 0) + '&comment=' + encodeURIComponent($('textarea[name=\'comment\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-history').attr('disabled', true);
			$('#history').prepend('<div class="attention"><img src="view/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-history').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(html) {
			$('#history').html(html);
			
			$('textarea[name=\'comment\']').val('');
			
			$('#order-status').html($('select[name=\'order_status_id\'] option:selected').text());
		}
	});
});
//--></script> 
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script> 
<script type="text/javascript"><!--
			
<?php if(isset($this->request->get['rec'])){?>
$('a[href=\'#tab-recurring-edit\']').trigger('click');
<?php }else if(isset($this->request->get['statuschange'])){?>
$('a[href=\'#tab-recurring\']').trigger('click');
<?php }?>

			$('.date').datepicker({dateFormat: 'yy-mm-dd'});
			function isNumberKeyWithDecimal(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
		return false;
	} else {
		return true;
	}
}
 function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	} else {
		return true;
	}
}

$('#update').bind('click', function() {
         
			$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/recurring_order/updateRecurringOrder&token=<?php echo $token; ?>&profile_id=<?php echo $recurring;?>&order_id=<?php echo $this->request->get['order_id'];?>',
		dataType: 'json',
		data: $('#tab-recurring-edit input[type=\'text\'], textarea , select'),
		beforeSend: function() {
			$('#update').before('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			
			$('.success, .warning').remove();
			if (json['error']) {
				alert(json['error']);
			}
			if (json['redirect']) 
			location = json['redirect'];	
			
			
		}
	});	
								
												
	});	
//--></script> 
<?php echo $footer; ?>