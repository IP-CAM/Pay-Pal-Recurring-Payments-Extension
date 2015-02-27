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
?><?php echo $header; ?> 
<div id="content"> 
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
   <?php if ($success_recurring) { ?>
  <div class="success"><?php echo $success_recurring; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/recurring.jpg" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"> <?php if ($this->config->get('pp_pro_recurring_signature')){?>	<a href="<?php echo $jump;?>" class="button"><?php echo $jump_button; ?></a><?php }?> <a href="<?php echo $productjump;?>" class="button"><?php echo $productjump_button; ?></a> <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
    <div id="tabs" class="htabs"><a href="#tab-ipn"><span style="font-size:13px;"><?php echo $tab_ipn; ?></span></a><a href="#tab-general"><span style="font-size:13px;"><?php echo $tab_general; ?></span></a><a href="#tab-configuration"><span style="font-size:13px;"><?php echo $tab_configuration; ?></span></a><a href="#tab-emailtemplates"><span style="font-size:13px;"><?php echo $tab_emailtemplates; ?></span></a>
    <a href="#tab-order"><span style="font-size:13px;"><?php echo $tab_order; ?></span></a> <a href="#tab-documentation"><span style="font-size:13px;"><?php echo $tab_documentation; ?></span></a></div>
    
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
       <div id="tab-general">
       
        <table class="form3">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_username; ?></td>
            <td><input type="text"  name="pp_pro_recurring_username" value="<?php echo $pp_pro_recurring_username; ?>" />
              <?php if ($error_username) { ?>
              <span class="error"><?php echo $error_username; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_password; ?></td>
            <td><input type="text" name="pp_pro_recurring_password" value="<?php echo $pp_pro_recurring_password; ?>" />
              <?php if ($error_password) { ?>
              <span class="error"><?php echo $error_password; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_signature; ?></td>
            <td><input type="text"   name="pp_pro_recurring_signature" value="<?php echo $pp_pro_recurring_signature; ?>" />
              <?php if ($error_signature) { ?>
              <span class="error"><?php echo $error_signature; ?></span>
              <?php } ?></td>
          </tr>
           <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('This will allow your customers to manage their subscription from their user account in the way of being able to cancel their account. If selected NO, they will not even see the option.',TITLE,'Customer Profile Management',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <b>Customer Profile Management</b><br /></td>
            <td><?php if ($pp_pro_recurring_usermanage) { ?>
              <input type="radio" name="pp_pro_recurring_usermanage" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="pp_pro_recurring_usermanage" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="pp_pro_recurring_usermanage" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="pp_pro_recurring_usermanage" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_test; ?></td>
            <td><?php if ($pp_pro_recurring_test) { ?>
              <input type="radio" name="pp_pro_recurring_test" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="pp_pro_recurring_test" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="pp_pro_recurring_test" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="pp_pro_recurring_test" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_transaction; ?></td>
            <td><select name="pp_pro_recurring_transaction">
                <?php if (!$pp_pro_recurring_transaction) { ?>
                <option value="0" selected="selected"><?php echo $text_authorization; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_authorization; ?></option>
                <?php } ?>
                <?php if ($pp_pro_recurring_transaction) { ?>
                <option value="1" selected="selected"><?php echo $text_sale; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_sale; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="pp_pro_recurring_total" value="<?php echo $pp_pro_recurring_total; ?>" /></td>
          </tr>          
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="pp_pro_recurring_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pp_pro_recurring_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
           <tr>
            <td><?php echo $entry_declined_transaction_status; ?><br /><small>(Use Failed or Denied)</small></td>
            <td><select name="pp_pro_recurring_declined_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pp_pro_recurring_declined_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
           <tr>
            <td><?php echo $entry_expired_status; ?> <br /><small>(Use Expired)</small></td>
            <td><select name="pp_pro_recurring_expired_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pp_pro_recurring_expired_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
           <tr>
            <td><?php echo $entry_refunded_status; ?> <br /><small>(Use Refunded)</small></td>
            <td><select name="pp_pro_recurring_refunded_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pp_pro_recurring_refunded_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="pp_pro_recurring_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $pp_pro_recurring_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="pp_pro_recurring_status">
                <?php if ($pp_pro_recurring_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          
        </table>
     </div>
       <div id="tab-configuration">
         <table class="form3">
        
           <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $description_text;?>',TITLE,'<?php echo $entry_description; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <span class="required">*</span><?php echo $entry_description; ?> </td>
              <td><input type="text"  size="80" maxlength="127" name="pp_pro_recurring_desc" value="<?php echo $pp_pro_recurring_desc; ?>"  />  <?php if ($error_description) { ?>
              <span class="error"><?php echo $error_description; ?></span>
              <?php } ?></td>
           
          </tr>
        <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $startdate_text;?>',TITLE,'<?php echo $entry_startdate; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <span class="required">*</span><?php echo $entry_startdate; ?><br /> <small><?php echo $entry_start_date; ?></small></td>
            <td><input type="text" name="pp_pro_recurring_start_date" size="3" maxlength="3" onkeypress="return isNumberKey(event);" value="<?php echo $pp_pro_recurring_start_date; ?>"  /> <?php echo $startdate_text_small;?> 
              </td>
          </tr>
           <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $periodtext;?>',TITLE,'<?php echo $entry_billingperiod; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <span class="required">*</span><?php echo $entry_billingperiod; ?> </td>
              <td><select name="pp_pro_recurring_billingperiod" id="pp_pro_recurring_billingperiod">
                <option value="Month" selected="selected"><?php echo $text_month; ?></option>
                <option value="SemiMonth"><?php echo $text_semimonth; ?></option>
                 <option value="Day"><?php echo $text_day; ?></option>
                  <option value="Year"><?php echo $text_year; ?></option>
              </select>
              </td>
           
          </tr>
            <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $frequency;?>',TITLE,'<?php echo $entry_billingfrequency; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <span class="required">*</span><?php echo $entry_billingfrequency; ?> </td>
            <td><input name="pp_pro_recurring_billingfrequency" type="text"  onkeypress="return isNumberKey(event);" value="<?php echo $pp_pro_recurring_billingfrequency; ?>" size="3" maxlength="3" /> <?php if ($error_frequency) { ?>
              <span class="error"><?php echo $error_frequency; ?></span>
              <?php } ?></td>
          </tr>
          
           <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $cycles_text;?>',TITLE,'<?php echo $entry_totalbilligcycles; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_totalbilligcycles; ?> <br /><small><?php echo $cycles;?></small></td>
              <td><input type="text" name="pp_pro_recurring_billingcycles" value="<?php echo $pp_pro_recurring_billingcycles;?>" size="4" maxlength="4" id="pp_pro_recurring_billingcycles" /> </td>
           
          </tr>
          
          
        </table>
         <table class="form3">
       <tr><td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $specialitemstext;?>',TITLE,'<?php echo $specialitems; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <strong><?php echo $specialitems;?></strong></td></tr>
        	</table>
            <style>#special-boxes:hover { background-color:#F2F2F2}</style>
            <?php if($recurringproducts){?>	
           
            <?php foreach($recurringproducts as $product){?>
            <div id="special-boxes" style="float:left;margin-right:5px;margin-bottom:5px;border: #CCC 1px solid;padding:3px;">
           <strong>Item Name:</strong><br> <?php echo $product['recurring_product_name'].'<br><br>';?>
           Status: <select name="iteminfo[<?php echo $value_row; ?>][item_data][status]" id="pp_pro_recurring_itemstatus_existing<?php echo $value_row; ?>">
                <option value="0" selected="selected">Disabled</option>
                <option value="1">Enabled</option>
               
              
              </select> 
               <br /> <br />
             <?php if($product['item_data']){?>
             <?php foreach($product['item_data'] as $data){?>
              <input type="hidden" name="iteminfo[<?php echo $value_row; ?>][item_data][pid]" value="<?php echo $product['recurring_product_id'];?>" />
            <span class="required">*</span>Billing Period: <select name="iteminfo[<?php echo $value_row; ?>][item_data][period]" id="pp_pro_recurring_itembillingperiod_existing<?php echo $value_row; ?>">
                <option value="" selected="selected">--Select--</option>
                <option value="Month"><?php echo $text_month; ?></option>
                <option value="SemiMonth"><?php echo $text_semimonth; ?></option>
                 <option value="Day"><?php echo $text_day; ?></option>
                  <option value="Year"><?php echo $text_year; ?></option>
              
              </select> 
               <br /> <br />
               
            <span class="required">*</span>Billing Frequency: <input type="text" name="iteminfo[<?php echo $value_row; ?>][item_data][frequency]" value="<?php echo $data['item_frequency'];?>" onkeypress="return isNumberKey(event);"  size="3" maxlength="3"  /> 
             
          <br /> <br />
          Billing Cycles: <input type="text" name="iteminfo[<?php echo $value_row; ?>][item_data][cycles]" value="<?php echo $data['item_cycles'];?>" size="4" maxlength="4"  />       
                 <br /> <br />
               
                <?php }?>
                
                
            <?php }else{?>
            Status: <select name="iteminfo[<?php echo $value_row; ?>][item_data][status]">
                <option value="0" selected="selected">Disabled</option>
                <option value="1">Enabled</option>
               
              
              </select> 
               <br /> <br />
           <input type="hidden" name="iteminfo[<?php echo $value_row; ?>][item_data][pid]" value="<?php echo $product['recurring_product_id'];?>" />
             <span class="required">*</span>Billing Period: <select name="iteminfo[<?php echo $value_row; ?>][item_data][period]" >
                <option value="" selected="selected">--Select--</option>
                <option value="Month"><?php echo $text_month; ?></option>
                <option value="SemiMonth"><?php echo $text_semimonth; ?></option>
                 <option value="Day"><?php echo $text_day; ?></option>
                  <option value="Year"><?php echo $text_year; ?></option>
              
              </select> 
               <br /> <br />
               
            <span class="required">*</span>Billing Frequency: <input type="text" name="iteminfo[<?php echo $value_row; ?>][item_data][frequency]" value="" onkeypress="return isNumberKey(event);"  size="3" maxlength="3"  /> 
             
          <br /> <br />
         Billing Cycles: <input type="text" name="iteminfo[<?php echo $value_row; ?>][item_data][cycles]" value="" size="4" maxlength="4"  />       
              
                 <br /> <br />
               <span style="color:#F00;">Recurring item has no settings. Default settings will be used.</span><br /><br />
               
             <?php }?>
             
               <script type="text/javascript">
			  <?php if($data['item_period']){?>
	$("#pp_pro_recurring_itembillingperiod_existing<?php echo $value_row; ?>").val("<?php echo $data['item_period'];?>");
<?php }?>
 <?php if($data['item_period']){?>
	$("#pp_pro_recurring_itemstatus_existing<?php echo $value_row; ?>").val("<?php echo $data['item_status'];?>");
<?php }?>
			  </script>
                <?php $value_row++; ?>
                  </div>
             <?php }?>
           
            <?php }else{?>
           You currently have no products marked as recurring items. Because of this, the Default Billing Cycle Information will be used for all items.
            <?php }?>
            <div style="clear:left"></div>
          <table class="form3">
            <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $amount_text;?>',TITLE,'<?php echo $entry_amount; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <span class="required">*</span><?php echo $entry_amount; ?> </td>
            <td><?php echo $entry_userordertotal; ?> <input type="radio" name="pp_pro_recurring_usetotal" value="1" id="pp_pro_recurring_usetotal" <?php if($pp_pro_recurring_usetotal == "1" || !$pp_pro_recurring_usetotal){?> checked <?php }?> /> <?php echo $entry_no; ?><input type="radio" name="pp_pro_recurring_usetotal" id="pp_pro_recurring_usetotal" value="0" <?php if($pp_pro_recurring_usetotal == "0"){?> checked <?php }?>/><span id="pp_pro_recurring_amount" <?php if($pp_pro_recurring_usetotal != "0"){?> style="display:none;" <?php }?>><input type="text" name="pp_pro_recurring_amount"  value="<?php echo $pp_pro_recurring_amount; ?>"  size="8" maxlength="8" onkeypress="return isNumberKeyWithDecimal(event);" /> <small><?php echo $must;?></small></span> </td>
          </tr>          
          
           <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $reference_text;?>',TITLE,'<?php echo $entry_reference; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_reference; ?> </td>
 <td><?php echo $entry_userorderid; ?> <input type="radio" name="pp_pro_recurring_useorderid" value="1" id="pp_pro_recurring_useorderid" checked /><br /><small>(The Order ID must be used)</small>  </td></tr>
              
               <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $subscribername_text;?>',TITLE,'<?php echo $entry_subscribername; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"> <img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_subscribername; ?> <br /><small><?php echo $subscribeminitext;?></small></td>
              <td><input type="text" name="pp_pro_recurring_subscribername" value="<?php echo $pp_pro_recurring_subscribername;?>" size="50" maxlength="80" id="pp_pro_recurring_subscribername" /> </td>
          </tr>
          <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $autobill_text;?>',TITLE,'<?php echo $entry_autobill; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_autobill; ?> </td>
            <td><select name="pp_pro_recurring_autobill" id="pp_pro_recurring_autobill">
                <?php if ($pp_pro_recurring_autobill) { ?>
                <option value="AddToNextBilling" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="NoAutoBill"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="AddToNextBilling"><?php echo $text_enabled; ?></option>
                <option value="NoAutoBill" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
           <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $maxfailed_text;?>',TITLE,'<?php echo $entry_maxfailed; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_maxfailed; ?> </td>
              <td><input type="text" name="pp_pro_recurring_maxfailed" size="2" onkeypress="return isNumberKey(event);" maxlength="2" value="<?php echo $pp_pro_recurring_maxfailed;?>"  id="pp_pro_recurring_maxfailed" /> </td>
           
          </tr>
          <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $initialamount_text;?>',TITLE,'<?php echo $entry_initialamount; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_initialamount; ?> </td>
              <td><input type="text" name="pp_pro_recurring_initialamount" value="<?php echo $pp_pro_recurring_initialamount;?>"  size="8" maxlength="8" onkeypress="return isNumberKeyWithDecimal(event);" id="pp_pro_recurring_initialamount" /> </td>
           
          </tr>
          <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $initialamountfail_text;?>',TITLE,'<?php echo $entry_initialamountfail; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_initialamountfail; ?> </td>
              <td><select name="pp_pro_recurring_initialamountfail" id="pp_pro_recurring_initialamountfail">
               
                <option value="CancelOnFailure" selected="selected"><?php echo $entry_CancelOnFailure; ?></option>
                <option value="ContinueOnFailure"><?php echo $entry_ContinueOnFailure; ?></option>
              
              </select> </td>
        
          </tr>
          <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $taxamount_text;?>',TITLE,'<?php echo $entry_taxamount; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_taxamount; ?> <br /><small><?php echo $entry_taxamount_tiny; ?></small></td>
              <td><input type="text" name="pp_pro_recurring_taxamount" value="<?php echo $pp_pro_recurring_taxamount;?>"  size="8" maxlength="8" onkeypress="return isNumberKeyWithDecimal(event);" id="pp_pro_recurring_taxamount" /> </td>
           
          </tr>
           <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $shippingamount_text;?>',TITLE,'<?php echo $entry_shippingamount; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_shippingamount; ?> <br /><small><?php echo $entry_shippingamount_tiny; ?></small></td>
              <td><input type="text" name="pp_pro_recurring_shippingamount" value="<?php echo $pp_pro_recurring_shippingamount;?>"  size="8" maxlength="8" onkeypress="return isNumberKeyWithDecimal(event);" id="pp_pro_recurring_shippingamount" /> </td>
           
          </tr>
        </table>
         <table class="form3"><tr><td><strong><?php echo $text_trial_setup;?></strong></td></tr></table>
        <table class="form3">
        
          <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $trial_text;?>',TITLE,' <?php echo $entry_trial; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <span class="required">*</span><?php echo $entry_trial; ?> </td>
            <td> <?php if ($pp_pro_recurring_trial) { ?>
              <input type="radio" name="pp_pro_recurring_trial" id="pp_pro_recurring_trial" value="0"  />
              <?php echo $entry_no_trial; ?>
              <input type="radio" name="pp_pro_recurring_trial" id="pp_pro_recurring_trial" value="1" checked="checked"/>
              <?php echo $text_yes; ?>
              <?php } else { ?>
              <input type="radio" name="pp_pro_recurring_trial" id="pp_pro_recurring_trial" value="0" checked="checked"/>
              <?php echo $entry_no_trial; ?>
              <input type="radio" name="pp_pro_recurring_trial" id="pp_pro_recurring_trial" value="1" />
              <?php echo $text_yes ?>
              <?php } ?>
          
            </td>
          </tr>         
        
        
        </table>
     
        <div id="trial-info" <?php if (!$pp_pro_recurring_trial) { ?> style="display:none;" <?php }?>>
       
  
  <?php if($trialinfo){ ?>
     
   <?php foreach($trialinfo as $info){ ?>      
   
       <div id="trial-info<?php echo $value_row_trial; ?>">
      <table class="form3">
       
      <p><?php if($info['trial_id'] == "na"){?><a title="Remove" onclick="removeNew('<?php echo $value_row_trial; ?>');"><img src="view/image/remove_trial.png" style="position:relative; top:3px;" alt="Remove Trial" /></a><?php }else{?> <a title="Remove" onclick="removeOld('<?php echo $info['trial_id'];?>','<?php echo $value_row_trial; ?>');"><img src="view/image/remove_trial.png" style="position:relative; top:3px;" alt="Remove Trial" /></a><?php }?> <strong>Trial <?php echo $value_row_trial; ?></strong></p>
       <tr>
            <td> <span class="required">*</span><?php echo $entry_trialbillingperiod; ?> </td>
              <td><select name="trialinfo[<?php echo $value_row_trial; ?>][trial_data][period]" id="pp_pro_recurring_trialbillingperiod<?php echo $value_row_trial; ?>">
                <option value="" selected="selected">--Select--</option>
                <option value="Month"><?php echo $text_month; ?></option>
                <option value="SemiMonth"><?php echo $text_semimonth; ?></option>
                 <option value="Day"><?php echo $text_day; ?></option>
                  <option value="Year"><?php echo $text_year; ?></option>
              
              </select> 
              <script type="text/javascript">
			  <?php if($info['trial_period']){?>
	$("#pp_pro_recurring_trialbillingperiod<?php echo $value_row_trial; ?>").val("<?php echo $info['trial_period'];?>");
<?php }?>
			  </script>
              
              </td>
        
          </tr>
         
           
           	<?php if($trialproducts){?>	
            <tr>
            
<td><span class="required">*</span>Assign Trial Product: </td><td><select name="trialinfo[<?php echo $value_row_trial; ?>][trial_data][product]" id="pp_pro_recurring_trialproduct<?php echo $value_row_trial; ?>" style="width:158px;">
	<option value="" selected="selected">Select Trial Product</option>
	<?php foreach ($trialproducts as $row){?>	
	<option value="<?php echo $row['product_id'];?>"><?php echo $row['name'];?></option>
	<?php } ?>
	</select>
      <script type="text/javascript">
		<?php if($info['trial_product']){?>
	$("#pp_pro_recurring_trialproduct<?php echo $value_row_trial; ?>").val("<?php echo $info['trial_product'];?>");
<?php }?>
	 </script>
    </td>
     </tr>
	<?php }else{ ?>
	<tr><td><strong>There are no trial products set up at this time. Remember that a trial will initiate even though there is no visible reference to it in the store front.</strong></td>
    </tr>
	<?php } ?>
    
           <tr>
            <td> <span class="required">*</span><?php echo $entry_trialbillingfrequency; ?> </td>
              <td><input type="text" name="trialinfo[<?php echo $value_row_trial; ?>][trial_data][frequency]" value="<?php echo $info['trial_frequency'];?>" onkeypress="return isNumberKey(event);"  size="3" maxlength="3" id="pp_pro_recurring_trialbillingfrequency<?php echo $value_row_trial; ?>" /> 
             </td>
           
          </tr>
          
        
            <tr>
            <td><?php echo $entry_trialcycles; ?></td>
              <td><input type="text" name="trialinfo[<?php echo $value_row_trial; ?>][trial_data][cycles]" value="<?php echo $info['trial_cycles'];?>" size="4" maxlength="4" id="pp_pro_recurring_trialcycles<?php echo $value_row_trial; ?>" /> </td>
           
          </tr>
         
         
           <tr>
            <td><span class="required">*</span><?php echo $entry_trialamount; ?> </td>
              <td><input type="text" name="trialinfo[<?php echo $value_row_trial; ?>][trial_data][amount]" value="<?php echo $info['trial_amount'];?>"  size="8" maxlength="8" onkeypress="return isNumberKeyWithDecimal(event);" id="pp_pro_recurring_trialamount<?php echo $value_row_trial; ?>" /> <?php if ($error_trialamount) { ?>
              <span class="error"><?php echo $error_trialamount; ?></span>
              <?php } ?></td>
           
         </tr>
       
         
            </table>
             </div>
     
           <?php $value_row_trial++; ?>
         
<?php }?>
   <?php  }else{
    echo "No Trials Set";
    }
    ?>
 <?php if($trialinfonew){ ?>
     
   <?php foreach($trialinfonew as $info){ ?>      
   
       <div id="trial-info<?php echo $value_row_trial; ?>">
      <table class="form3">
       
      <p><?php if($info['trial_id'] == "na"){?><a title="Remove" onclick="removeNew('<?php echo $value_row_trial; ?>');"><img src="view/image/remove_trial.png" style="position:relative; top:3px;" alt="Remove Trial" /></a><?php }else{?> <a title="Remove" onclick="removeOld('<?php echo $info['trial_id'];?>','<?php echo $value_row_trial; ?>');"><img src="view/image/remove_trial.png" style="position:relative; top:3px;" alt="Remove Trial" /></a><?php }?> <strong>Trial <?php echo $value_row_trial; ?></strong></p>
       <tr>
            <td> <span class="required">*</span><?php echo $entry_trialbillingperiod; ?> </td>
              <td><select name="trialinfonew[<?php echo $value_row_trial; ?>][trial_data][period]" id="pp_pro_recurring_trialbillingperiod<?php echo $value_row_trial; ?>">
              <option value="" selected="selected">--Select--</option>
                <option value="Month"><?php echo $text_month; ?></option>
                <option value="SemiMonth"><?php echo $text_semimonth; ?></option>
                 <option value="Day"><?php echo $text_day; ?></option>
                  <option value="Year"><?php echo $text_year; ?></option>
              
              </select> 
              <script type="text/javascript">
			  <?php if($info['trial_period']){?>
	$("#pp_pro_recurring_trialbillingperiod<?php echo $value_row_trial; ?>").val("<?php echo $info['trial_period'];?>");
<?php }?>
			  </script>
              
              </td>
        
          </tr>
         
           
           	<?php if($trialproducts){?>	
            <tr>
            
<td><span class="required">*</span>Assign Trial Product: </td><td><select name="trialinfonew[<?php echo $value_row_trial; ?>][trial_data][product]" id="pp_pro_recurring_trialproduct<?php echo $value_row_trial; ?>" style="width:158px;">
	<option value="" selected="selected">Select Trial Product</option>
	<?php foreach ($trialproducts as $row){?>	
	<option value="<?php echo $row['product_id'];?>"><?php echo $row['name'];?></option>
	<?php } ?>
	</select>
      <script type="text/javascript">
			  <?php if($info['trial_product']){?>
	$("#pp_pro_recurring_trialproduct<?php echo $value_row_trial; ?>").val("<?php echo $info['trial_product'];?>");
<?php }?>
	 </script>
    </td>
     </tr>
	<?php }else{ ?>
	<tr><td><strong>There are no trial products set up at this time. Remember that a trial will initiate even though there is no visible reference to it in the store front.</strong></td>
    </tr>
	<?php } ?>
    
           <tr>
            <td> <span class="required">*</span><?php echo $entry_trialbillingfrequency; ?> </td>
              <td><input type="text" name="trialinfonew[<?php echo $value_row_trial; ?>][trial_data][frequency]" value="<?php echo $info['trial_frequency'];?>" onkeypress="return isNumberKey(event);"  size="3" maxlength="3" id="pp_pro_recurring_trialbillingfrequency<?php echo $value_row_trial; ?>" /> 
             </td>
           
          </tr>
          
        
            <tr>
            <td><?php echo $entry_trialcycles; ?></td>
              <td><input type="text" name="trialinfonew[<?php echo $value_row_trial; ?>][trial_data][cycles]" value="<?php echo $info['trial_cycles'];?>" size="4" maxlength="4" id="pp_pro_recurring_trialcycles<?php echo $value_row_trial; ?>" /> </td>
           
          </tr>
         
         
           <tr>
            <td><span class="required">*</span><?php echo $entry_trialamount; ?> </td>
              <td><input type="text" name="trialinfonew[<?php echo $value_row_trial; ?>][trial_data][amount]" value="<?php echo $info['trial_amount'];?>"  size="8" maxlength="8" onkeypress="return isNumberKeyWithDecimal(event);" id="pp_pro_recurring_trialamount<?php echo $value_row_trial; ?>" /> <?php if ($error_trialamount) { ?>
              <span class="error"><?php echo $error_trialamount; ?></span>
              <?php } ?></td>
           
         </tr>
       
         
            </table>
             </div>
     
          
           <?php $value_row_trial++; ?>
         
<?php }?>
   <?php 
    }
    ?>
    <div id="addmoretrials"><a title="Add Trial" rel="nofollow" onClick="plusOneTrial();"><img src="view/image/add_trial.png"  alt="Add A Trial (Click)" /></a> </div>
          </div>
     </div>
<script type="text/javascript"><!--
function removeNew(divrow){
	value_row--;
	div_row--;
	
	$('#trial-info' + divrow +'').remove();
}

function removeOld(trialid,divrow){
	
	 $.ajax({
		type: 'POST',
		url: 'index.php?route=payment/pp_pro_recurring/getTrialCount&token=<?php echo $token; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('.success').remove();
			$('#trial-info' + divrow +'').before('<div class="attention"><img src="view/image/loading.gif" alt="" /> <?php echo "Looking for trial data..."; ?></div>');
		},
		complete: function() {			
			$('.attention').remove();
		},
		success: function(count_data) {
		 for (i = 0; i < count_data.length; i++) {
				
           var pcount = count_data[i]['trial_product_count'];	
		   var tcount = count_data[i]['trial_count'];		
		} 
		
		
	if (!confirm ('Are you sure you want to delete? You currently have' + ' ' + pcount + ' ' + 'active trial products, and'+ ' ' + tcount + ' ' + 'active trials.')) {
                return false;
            
        }else{
	
   $.ajax({
		type: 'POST',
		url: 'index.php?route=payment/pp_pro_recurring/deleteTrial&token=<?php echo $token; ?>&trialid=' + encodeURIComponent(trialid),
		dataType: 'json',
		beforeSend: function() {
			
			$('#trial-info' + divrow +'').before('<div class="attention"><img src="view/image/loading.gif" alt="" /> <?php echo "System Working..."; ?></div>');
		},
		complete: function() {			
			$('.attention').remove();
		},
		success: function(json) {
		      
		if (json['error']) {
				alert(json['error']); 
			}
		if(json['success']){
		$('#trial-info').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
					
		$('.success').fadeIn('slow');
		value_row--;
	     div_row--;	
         $('#trial-info' + divrow +'').remove();
			}	
			
			
		}
	});
	
		}
			
			
			
		}
	});
	
	
}	
value_row = <?php echo $value_row_trial; ?>;
 div_row = <?php echo $value_row_trial; ?>;
  function plusOneTrial(){
		 $.ajax({
		type: 'POST',
		url: 'index.php?route=payment/pp_pro_recurring/getTrialCount&token=<?php echo $token; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('.success').remove();
			$('#addmoretrials').before('<div class="zoey"><img src="view/image/loading.gif" alt="" /></div>');
		},
		complete: function() {			
			$('.zoey').remove();
		},
		success: function(count_data) {
		 for (i = 0; i < count_data.length; i++) {
				
           var pcount = count_data[i]['trial_product_count'];	
		 	
		} 
		if(pcount < 1) {
		alert('To access this feature you must create one or more trial products first!');
		}else if(value_row > pcount) {
			alert('You are attempting to create more trials than there are trial products. You currently have' + ' ' + pcount + ' ' + 'active trial products, and'+ ' ' + value_row + ' ' + 'active trials.');
		}else{
    html  = '<div id="trial-info' + div_row + '" >';
	html  += '<p><a onclick="removeNew(\'' + div_row + '\');"><img src="view/image/remove_trial.png" style="position:relative; top:3px;" alt="Remove Trial" /></a> <strong> Trial' + ' ' + div_row + '</strong></p>';
	html += '<table class="form3">';
	//
	html += '<tr><td><span class="required">*</span><?php echo $entry_trialbillingperiod; ?></td>';
	html += '<td><select name="trialinfonew[' + value_row + '][trial_data][period]" id="pp_pro_recurring_trialbillingperiod<?php echo $value_row_trial; ?>">';
	html += '<option value="">' + "--Select--" +'</option>';
	html += '<option value="Month">' + "<?php echo $text_month; ?>" +'</option>';
	html += '<option value="SemiMonth">' + "<?php echo $text_semimonth; ?>" +'</option>';
	html += '<option value="Day">' + "<?php echo $text_day; ?>" +'</option>';
	html += '<option value="Year">' + "<?php echo $text_year; ?>" +'</option>';
	html += '</select></td></tr>';
	//
    html += '<tr><td><span class="required">*</span>'+' Assign Trial Product:'+'</td>';
	<?php if($trialproducts){?>	
	html += '<td><select name="trialinfonew[' + value_row + '][trial_data][product]" style="width:358px;">';
	html += '<option value="" selected="selected">' + "Select Trial Product" +'</option>';
	<?php foreach ($trialproducts as $row){?>	
	html += '<option value="' + "<?php echo $row['product_id'];?>" + '">' + "<?php echo $row['name'];?>" +'</option>';
	<?php } ?>
	html += '</select></td></tr>';
	<?php }else{ ?>
	html += '<tr><td><strong>There are no trial products set up at this time. Remember that a trial will initiate even though there is no visible reference to it in the store front.</strong></td></tr>';
	<?php } ?>
	//
	html += '<tr><td><span class="required">*</span><?php echo $entry_trialbillingfrequency; ?> </td>';
	html += '<td><input type="text" name="trialinfonew[' + value_row + '][trial_data][frequency]" value="" onkeypress="return isNumberKey(event);"  size="3" maxlength="3" id="pp_pro_recurring_trialbillingfrequency<?php echo $value_row_trial; ?>" />  </td></tr>';
	//
	html += '<tr><td><?php echo $entry_trialcycles; ?></td>';
	html += '<td><input type="text" name="trialinfonew[' + value_row + '][trial_data][cycles]" value="" size="4" maxlength="4" id="pp_pro_recurring_trialcycles<?php echo $value_row_trial; ?>" /> <?php if ($error_trialcycles) { ?><span class="error"><?php echo $error_trialcycles; ?></span><?php } ?></td></tr>';
	//
	html += '<tr><td><span class="required">*</span><?php echo $entry_trialamount; ?></td>';
	html += '<td><input type="text" name="trialinfonew[' + value_row + '][trial_data][amount]" value=""  size="8" maxlength="8" onkeypress="return isNumberKeyWithDecimal(event);" id="pp_pro_recurring_trialamount<?php echo $value_row_trial; ?>" /> <?php if ($error_trialamount) { ?><span class="error"><?php echo $error_trialamount; ?></span><?php } ?></td></tr>';
    html += '</table>';   
	html  += '</div>';    
      
	$('#addmoretrials').before(html);
	
	value_row++;
	div_row++;
		}
		}
		});
  }
  //--></script> 
         <div id="tab-ipn">
         <table class="form3">  <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $pp_pro_recurring_showipn_text;?>',TITLE,'<?php echo $entry_ipn_display; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_ipn_display; ?> 
 </td><td align="left">
  
 <?php if($this->config->get('pp_pro_recurring_showipn') != '0'){?>
  <?php echo $entry_yes; ?> <input type="radio" name="pp_pro_recurring_showipn" value="1"  checked  /> <?php echo $entry_no_trial; ?> <input type="radio" name="pp_pro_recurring_showipn" value="0"  />
 <?php }elseif($this->config->get('pp_pro_recurring_showipn') == '0'){?>
  <?php echo $entry_yes; ?> <input type="radio" name="pp_pro_recurring_showipn" value="1"  checked  /> <?php echo $entry_no_trial; ?> <input type="radio" name="pp_pro_recurring_showipn" value="0"  checked  />
 <?php }else{?>
  <?php echo $entry_yes; ?> <input type="radio" name="pp_pro_recurring_showipn" value="1"  checked  /> <?php echo $entry_no_trial; ?> <input type="radio" name="pp_pro_recurring_showipn" value="0"  />
 <?php }?></td></tr>
 
  <tr>
            <td><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $pp_pro_recurring_send_new_text;?>',TITLE,'<?php echo $entry_ipn_send_new; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_ipn_send_new; ?> 
</td><td align="left">
 <?php if($this->config->get('pp_pro_recurring_send_new') != '0'){?>
  <?php echo $entry_yes; ?> <input type="radio" name="pp_pro_recurring_send_new" value="1"  checked  /> <?php echo $entry_no_trial; ?> <input type="radio" name="pp_pro_recurring_send_new" value="0"  />
 <?php }elseif($this->config->get('pp_pro_recurring_send_new') == '0'){?>
  <?php echo $entry_yes; ?> <input type="radio" name="pp_pro_recurring_send_new" value="1"  checked  /> <?php echo $entry_no_trial; ?> <input type="radio" name="pp_pro_recurring_send_new" value="0"  checked  />
 <?php }else{?>
  <?php echo $entry_yes; ?> <input type="radio" name="pp_pro_recurring_send_new" value="1"  checked  /> <?php echo $entry_no_trial; ?> <input type="radio" name="pp_pro_recurring_send_new" value="0"  />
 <?php }?></td></tr></table>
   <div style="width:900px;"><?php echo $text_ipn;?>
  
   <div style="margin-bottom:20px;margin-top:30px;"> <b>Your IPN URL:</b> <span style=" background-color:#F8F8F8;padding:10px;border:#CCC 1px solid; font-size:13px;"> <?php echo HTTP_CATALOG .'index.php?route=payment/recurringcallback';?> </span></div>
   <p><strong>IF YOU ARE USING A PAY PAL BUSINESS ACCOUNT</strong></p>
  <ul style="list-style:decimal;"><li style="margin-bottom:15px;"> Login to your Paypal account and click on Profile<br /><br /><img src="view/image/profile.jpg" alt="" /></li>
   <li style="margin-bottom:15px;">Now click on Instant Payment Notification Preferences</li>
   <li style="margin-bottom:15px;">Now click Edit Settings. (if your ipn is turned off make sure to click the button that say's TURN IPN ON first)<br /><br /><img src="view/image/settings.jpg" alt="" /></li>
   <li style="margin-bottom:15px;">Copy the IPN URL given to you above and paste it in the box, SAVE<br /><br /><img src="view/image/save.jpg" alt="" /></li>
   
   
   </ul>
   <p><strong>IF YOU ARE USING A PAY PAL PREMIER ACCOUNT</strong></p>
   If using a premier account it is a little different. Go to PROFILE> SELLING TOOLS > INSTANT PAYMENT NOTIFICATION > (update) > Choose IPN SETTINGS.
   </div>
   </div>
         <div id="tab-emailtemplates">
 You may read more about these settings in the Official Documentation. You also can use the tool tips. 
  <table class="form3">
  <tr> <td><div style="padding:10px; font-weight:bold;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $entry_email_confirm1_text;?>',TITLE,'<?php echo $entry_email_confirm1;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_email_confirm1;?> 
              <?php if ($entry_email_confirm1_enable){?>
           
              <input type="radio" name="entry_email_confirm1_enable" value="1" checked="checked" />
              <?php echo $entry_enable; ?>
              <input type="radio" name="entry_email_confirm1_enable" value="0" />
              <?php echo $entry_disable; ?>
           
              <?php }else{?>
               <input type="radio" name="entry_email_confirm1_enable" value="1"  />
              <?php echo $entry_enable; ?>
              <input type="radio" name="entry_email_confirm1_enable" value="0" checked="checked"/>
              <?php echo $entry_disable; ?>
              <?php }?>
              
              
              </div>
              <div><?php echo $email_template_info;?></div>
              
             
             <div style="width:700px;float:left;"><textarea name="email_confirm1" cols="40" rows="5"><?php echo $email_confirm1;?></textarea></div>
             <div style="float:left;margin-left:20px;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $template_logo_text;?>',TITLE,'<?php echo $template_logo; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $template_logo; ?>
             <br /><div class="image"><img src="<?php echo $template_logo_1; ?>" alt="" id="thumb-logo-1" />
                  <input type="hidden" name="email_template_logo_1" value="<?php echo $email_template_logo_1; ?>" id="logo-1" />
                  <br />
                  <a onclick="image_upload('logo-1', 'thumb-logo-1');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb-logo-1').attr('src', '<?php echo $no_image; ?>'); $('#logo-1').attr('value', '');"><?php echo $text_clear; ?></a></div></div>
            
</td></tr>
 <tr> <td><div style="padding:10px; font-weight:bold;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $entry_email_confirm2_text;?>',TITLE,'<?php echo $entry_email_confirm2;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_email_confirm2;?> 
            <?php if ($entry_email_confirm2_enable){?>
       
              <input type="radio" name="entry_email_confirm2_enable" value="1" checked="checked" />
              <?php echo $entry_enable; ?>
              <input type="radio" name="entry_email_confirm2_enable" value="0" />
              <?php echo $entry_disable; ?>            
               <?php }else{?>
                <input type="radio" name="entry_email_confirm2_enable" value="1"  />
              <?php echo $entry_enable; ?>
              <input type="radio" name="entry_email_confirm2_enable" value="0" checked="checked"/>
              <?php echo $entry_disable; ?>
                <?php } ?>
              </div>  
                <div><?php echo $email_template_info;?></div>
             
              <div style="width:700px;float:left;"><textarea name="email_confirm2" cols="40" rows="5"><?php echo $email_confirm2;?></textarea></div><div style="float:left;margin-left:20px;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $template_logo_text;?>',TITLE,'<?php echo $template_logo; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $template_logo; ?>
             <br /><div class="image"><img src="<?php echo $template_logo_2; ?>" alt="" id="thumb-logo-2" />
                  <input type="hidden" name="email_template_logo_2" value="<?php echo $email_template_logo_2; ?>" id="logo-2" />
                  <br />
                  <a onclick="image_upload('logo-2', 'thumb-logo-2');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb-logo-2').attr('src', '<?php echo $no_image; ?>'); $('#logo-2').attr('value', '');"><?php echo $text_clear; ?></a></div></div>
            
</td></tr>
 <tr> <td><div style="padding:10px; font-weight:bold;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $entry_email_confirm3_text;?>',TITLE,'<?php echo $entry_email_confirm3;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_email_confirm3;?> 
            <?php if ($entry_email_confirm3_enable) { ?>
             <input type="radio" name="entry_email_confirm3_enable" value="1" checked="checked" />
              <?php echo $entry_enable; ?>
              <input type="radio" name="entry_email_confirm3_enable" value="0" />
              <?php echo $entry_disable; ?>            
                <?php }else{?>
                 <input type="radio" name="entry_email_confirm3_enable" checked="checked" />
              <?php echo $entry_enable; ?>
              <input type="radio" name="entry_email_confirm3_enable" value="0" value="1" />
                 <?php } ?>
              </div>   
               <div><?php echo $email_template_info;?></div>
             
             <div style="width:700px;float:left;"> <textarea name="email_confirm3" cols="40" rows="5"><?php echo $email_confirm3;?></textarea></div><div style="float:left;margin-left:20px;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $template_logo_text;?>',TITLE,'<?php echo $template_logo; ?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $template_logo; ?>
             <br /><div class="image"><img src="<?php echo $template_logo_3; ?>" alt="" id="thumb-logo-3" />
                  <input type="hidden" name="email_template_logo_3" value="<?php echo $email_template_logo_3; ?>" id="logo-3" />
                  <br />
                  <a onclick="image_upload('logo-3', 'thumb-logo-3');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb-logo-3').attr('src', '<?php echo $no_image; ?>'); $('#logo-3').attr('value', '');"><?php echo $text_clear; ?></a></div></div>
            
</td></tr>
<tr> <td><div style="padding:10px; font-weight:bold;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $entry_screen_confirm1_text;?>',TITLE,'<?php echo $entry_screen_confirm1;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_screen_confirm1;?> </div> <?php echo $entry_screen_confirm1_info;?> 
             
              <textarea name="screen_confirm1" cols="40" rows="5"><?php echo $screen_confirm1;?></textarea>
            
</td></tr>
 <tr><td><div style="padding:10px; font-weight:bold;"> <a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $entry_screen_confirm2_text;?>',TITLE,'<?php echo $entry_screen_confirm2;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_screen_confirm2;?> </div> <?php echo $entry_screen_confirm2_info;?> 
             
             <textarea name="screen_confirm2" cols="40" rows="5"><?php echo $screen_confirm2;?></textarea>
            
</td></tr>
 <tr> <td><div style="padding:10px; font-weight:bold;"><a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $entry_screen_confirm3_text;?>',TITLE,'<?php echo $entry_screen_confirm3;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $entry_screen_confirm3;?></div>  <?php echo $entry_screen_confirm3_info;?> 
             
             <textarea name="screen_confirm3" cols="40" rows="5"><?php echo $screen_confirm3;?></textarea>
            
</td></tr>
  
  </table>
  </div>
  
   <div id="tab-order">
 <table class="form3">
   <tr><td> <a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $reasons_text;?>',TITLE,'<?php echo $reasons;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $reasons;?><br /><br />
                <span id="reasonstext"><textarea name="profilereasons" cols="100" id="pmon"  rows="5" ></textarea></span><br />
                <a id="send-reason" class="button" style="margin-top:10px;"><?php echo $save;?></a>
              <div id="reasons" style="-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;height:150px; overflow:auto; padding:5px;background-color:#F7F7F7; border:#999 1px solid;margin-top:10px;">
              <?php if($thereasons){?>
                <?php $counter = 1;?>
              <?php foreach($thereasons as $areason){
              echo $counter.'.'.$areason['reason'].'<br>';
              $counter++;
             }?>
             <?php }else{?>
             There are no current reasons!
             
             <?php }?>
              
              
              </div>
              
              
              
              </td></tr>
              
               <tr><td> <a href="javascript:%20void(0)" onmouseover="Tip('<?php echo $refund_reasons_text;?>',TITLE,'<?php echo $refundreasons;?>',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a> <?php echo $refundreasons;?><br /><br />
                <span id="refundreasonstext"><textarea name="profilereasons" cols="100" id="pmon2"  rows="5" ></textarea></span><br />
                <a id="send-reason-refund" class="button" style="margin-top:10px;"><?php echo $save;?></a>
              <div id="refundreasons" style="-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;height:150px; overflow:auto; padding:5px;background-color:#F7F7F7; border:#999 1px solid;margin-top:10px;">
              <?php if($therefundreasons){?>
                <?php $counter = 1;?>
              <?php foreach($therefundreasons as $areason){
              echo $counter.'.'.$areason['reason'].'<br>';
              $counter++;
             }?>
             <?php }else{?>
             There are no current reasons!
             
             <?php }?>
              
              
              </div>
              
              
              
              </td></tr>
             
            </table>
  </div>
   <div id="tab-documentation">
   <div style=" font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:1.3; width:900px;">
   <h2>System Requirements</h2>
   <p><strong>In order to use this extension you must have the following:</strong></p>
   <ul>
   <li>A Registered PayPal Account (Premier or Business). If you do not have one then go here to register: <a href="https://www.paypal.com/webapps/mpp/paypal-payments-pro" target="_blank">https://www.paypal.com/webapps/mpp/paypal-payments-pro</a></li>
   <li>PayPal Payments Pro Account + Recurring Payments Add-On. You can check out the details: <a href="https://www.paypal.com/webapps/mpp/paypal-payments-pro" target="_blank">https://www.paypal.com/webapps/mpp/paypal-payments-pro</a>. For now only Pro is offered. In the future, Pay Pal Advanced will be offered as well. We don't recommend using advanced because it uses an iFrame an looks unprofessional.</li>
   <li>An SSL Certificate. You need to provide your customers a secure checkout since everything is done right from your Open Cart Store and "not" paypal website. Here are two articles I wrote concerning SSL and OpenCart:<ul><li><a href="http://blog.arvixe.com/opencart-how-to-configure-ssl/" target="_blank">http://blog.arvixe.com/opencart-how-to-configure-ssl/</a></li><li><a href="http://blog.arvixe.com/opencart-ssl-do-i-need-one/" target="_blank">http://blog.arvixe.com/opencart-ssl-do-i-need-one/</a></li></ul></li>
   <li>Opencart vQmod Patching System. You can download it here: <a href="http://code.google.com/p/vqmod/" target="_blank">http://code.google.com/p/vqmod/</a></li>
   <li>Opencart v1.5.3.0 +</li>
   <li>Requires Individuals located in the US, UK, or CANADA. </li>
   <li>Coming soon: Hosted Versions for: Australia, France, Hong Kong, Italy, Japan, Singapore, Spain, UK </li>
   </ul>
     <h2>Extension Description</h2>
     <p>Paypal Recurring Payments is a great way to gather recurring payments from customers. Some people call them Subscription based payments. This extension gives you a very advanced way to set up the Recurring Profile and later, manage it. 
     We've worked hard to make just about every option available to you in order to allow full integration/customizing with your customers needs. THIS IS NOT PAYPAL SUBSCRIPTIONS. I needed to clarify that because they are two very different applications. Here are just a few of the main features involving this extension:</p>
     <ul>
     <li>Detailed/Advanced Recurring Profile Setup</li>
     <li>Complete Real-time Refund Manager for Paypal Transactions</li>
     <li>Trial Offer Setup + ability to add/edit products offered after Trial has expired.</li>
      <li>Advanced Recurring Profile Management/Updating from Administration</li>
       <li>No need to login to Pay Pal to mess with settings and/or manage profiles. Entering your IPN info into your Paypal Account is the extent of which you will be needing to login.</li>
       <li>Custom Email Templates for many scenarios concerning recurring orders.</li>
       <li>Custom Recurring Item Creation in the Catalog to make it easy to offer subscription based items.</li>
       <li>Custom Customer Group is used for those that enroll, and when no longer enrolled are set back to default.</li>
       <li>Advanced validation that cover all areas to ensure your profile will work once created.</li>
       <li>Plenty of documentation and tooltips.</li>
        <li>Even if managed from the PAY PAL side, IPN will send messages to update the OpenCart Database.</li>
       <li>Easy install with vQmod</li>
       <li>Advanced IPN callback platform to handle all responses sent from PayPal to Opencart concerning recurring orders</li>
     </ul>
     <h2>Creating A Recurring Profile</h2>
     <p>There are MANY ways to set up a Recurring Profile and it really depends on what you are offering. Some will only be offering a monthly service where there are no physical products involved, and others will be shipping out items to their customers each week/month etc. This system allows you to set this up how you want it.
     To give you an example on how to set this up we have chosen a very common scenario where you will be setting up a Subscription with a Trial Period at the beginning.</p>
  
     
     <ul>
     <li>First you must set up your Recurring Items by creating a product in CATALOG>PRODUCTS. This extension includes custom fields that can be found in the DATA TAB. You'll want to mark certain items as RECURRING ITEMS and certain items as TRIAL ITEMS. Trial items will normally have a recurring item tied to it as a COMBO. There is also a MAX QUANTITY field which will normally be set to one. Most subscription based offers are ONE ITEM. If someone tries to insert a product into the cart that is NOT marked as recurring then it will push the recurring item OUT of the cart. These items are unique and should be treated as such. Most won't be mixing items anyway and will only have recurring items to show to the public. Since you are offering a Trial you need to create an item that is for the Trial as well. Create your item(s) and press SAVE. You can see the items in the demo by going <a href="http://involutionmedia.com/ocedemos/index.php?route=product/category&path=59" target="_blank">HERE</a>.</li>
     <li>Next you need to go to EXTENSIONS>PAYMENTS>PayPal Website Payment Pro (Recurring) and click on edit. Once in there you will have a variety of settings to choose from. To see the settings that are specifically for this example just login to the demo
      username: demo password: demo and see the settings yourself. We made sure to check off TRIAL whereby activating it for the profile and we set the parameters. If you want to offer a FREE TRIAL then leave the amount at 0.00. We also specified a product that we want to offer AFTER the Trial has expired. <b>NOTE:</b> There were 4 order statuses INSERTED into the order_status table that compliment this extension which are ACTIVE, SUSPENDED, CANCELED, AND FAILED. You'll want to set your complete order status to ACTIVE and the failed order status to FAILED, or DENIED. Here are some important points when setting up the actual FLOW of a recurring profile
   
      <li>If you offer a trial make sure to set the regular profile start date AFTER the total trial day's have expired. For example if your Trial will run for 2 weeks you will want to set the regular start date for 14 days from the initial order.</li>
       <li>If you offer an INITIAL AMOUNT, this will be charged to the customer card at the time of initial order. For example if you are offering a trial and you want to only charge for shipping then you will want to fill in the INITIAL AMOUNT with the shipping cost, then make the trial billing cycle amount the same for however long the trial will last.</li>
        <li>Initial Amount Fail Action:  This is an important setting because it determines whether or not a customer's profile is created if their credit card is declined upon initial order. If you choose CONTINUE ON FAILURE then the amount owed will be billed to the next cycle. NOTE: some transactions won't even go through and they won't go past step 5 of the checkout. It all depends on the response from the merchant bank.</li>
     
 
     
     </ul>
     <p><strong>Other important details:</strong></p>
     <ul>
     <li>The recurring system is smart enough to detect certain conditions. If you create one more more Trial Items without setting up Trials in EXTENSIONS>PAYMENTS it will disallow customers to checkout with those items. </li>
     </ul>
      <h2>Managing A Recurring Profile</h2>
     <p> To manage a recurring profile you simply need to go to SALES>RECURRING ORDERS and click on Manage Profile. Or, if you are in the EXTENSIONS>PAYMENTS page I added a button that says MANAGE EXISTING ORDERS. That will quick jump you there. Once in the SALES>RECURRING ORDERS you can quick jump "back" to the settings page by clicking on a button entitled EDIT RECURRING SETTINGS.
      You are allowed to update certain criteria in the Recurring Profile + change the overall status. The input must be accurate and valid or the system will not allow it.</p>
       <h2>Having Two PAY PAL Payment Gateways (Pay Pal Pro + Recurring)</h2>
     <p> If you find it necessary to be running two payment gateways with PAY PAL (for example you have recurring items or regular items then the IPN URL that you installed for the recurring will also be used for the other payment procedure. Obviously this is bad, so I inserted a call back URL in the Pay Pal Pro module to go to a folder on the server. This will ensure that there is 0 conflict.</p>
      <h2>FAQ's</h2>
      <p><strong>If a customer wants to cancel their subscription, how will they let us know?</strong><br />
      The customer will have to call in , OR you can use the default RMA form to allow people to request cancelation. Typically it is required to call in or fill out a form to cancel.
      </p>
       <p><strong>How much will Pay Pal charge me in order to use Recurring Payments?</strong><br />
          This indeed will cost extra. It is a package that you must add on after you sign up with Pro. Check out Merchant Tools for pricing, or call Pay Pal.
      </p>
       <p><strong>Will this extension work with all languages I have installed on my Opencart?</strong><br />
       All of the language files for this extension are written in English. In order to change the language you will need to do the following:
       <ul><li>Locate all ENGLISH language files and copy them into another language folder. </li><li>Edit the text to your liking. Now when you switch languages it will show up. Otherwise, English will only show.</li></ul>
      </p>
      <p><strong>Will this extension work with all currencies I have installed on my Opencart?</strong><br />
      Yes.
      </p>
       <p><strong>Will customers be sent to the paypal website to checkout?  </strong><br />
     No, all transactions take place on the opencart page.
      </p>
       <p><strong>Does the purchase of the extension include upgrades?</strong><br />
     Yes.
      </p>
      <h2>Using The Refund Manager</h2>
      <p>This extension allows you to apply a refund to an individuals order. You can apply a refund by clicking on the Refund Manager Tab in recurring order details. </p>
      <h2>Using Multiple Languages</h2>
      <p>The extension comes as Enlgish but every single bit of text is part of a few language files. You are free to just duplicate the files and stick them in your own language folder. The vQmod will make sure to reference whichever language is chosen by the system settings.</p>
      <h2>Installation Instructions</h2>
   <p><strong>In order to successfully install the extension please follow these instructions:</strong></p>
   <ul>
      <li>Sign up with Paypal. Do this first. Sign up for Website Payments Pro and get the Recurring Payments PLUGIN. AFTER you do all of this, then carry on with the rest of the instructions below.</li>
       <li>If you have not installed VQMOD for your opencart you must do this first, else the extension will not work and you will receive errors. You can download it here: <a href="http://code.google.com/p/vqmod/" target="_blank">http://code.google.com/p/vqmod/</a></li>
      <li>If you have not installed an SSL you need to do so. Purchase a cheap SSL from your hosting company and just get it taken care of. Paypal Website Payments Pro requires it.</li>
   <li>Unzip the extension to a location on your local drive that will not conflict with any other OpenCart files.</li>
   <li>With FTP, upload the entire contents of the UPLOAD folder to your store location. Nothing will be over-written.</li>
   <li>Login to your admin www.yourdomain.com/admin and navigate to EXTENSIONS>PAYMENTS>PayPal Website Payment Pro (Recurring) and click on INSTALL. When you do this, the extension will install itself along with all of the SQL queries needed to allow it to function.</li>
   <li>Now navigate to CATALOG>PRODUCTS and set up your recurring items. If you are going to run a trial then this is where you want to set that up as well. The 3 custom fields you want to pay attention to are under the DATA TAB in product details.</li>
   <li>Now, navigate to EXTENSIONS>PAYMENTS>PayPal Website Payment Pro (Recurring) and click EDIT. You will see many TABS of items to configure. You will want to read the information in the OFFICIAL DOCUMENTATION TAB to get a better understanding on how to use the system.</li>
 <li>Go to the IPN CONFIGURATION, grab the IPN URL given to you and set it up within your Paypal Account. You will need to login and follow the instructions I gave you in this TAB.</li>
</ul>
<p><strong>Make sure to set the following statuses under the GENERAL CONFIGURATION TAB:</strong></p>
<ul><li>Declined Transaction Status</li><li>Expired Profile Status</li><li>Order Status</li><li>Refund Status</li></ul>
        <h2>IPN Information</h2>
       <p>The recurring system you purchased relies heavily on IPN (instant-payment-notification) in order to keep the communication lines open between Paypal and Opencart. Without it, there is no way to receive updates to orders. You MUST login to your Paypal account and configure the IPN URL per the instructions found under the IPN CONFIGURATION TAB.</p>
    <h2>Customer Support</h2>
  <p><strong>For customer support please contact AVVICI at:</strong></p>
joe@opencartvqmods.com<br />
001-704-907-1048<br />
Skype: involution.media
   </div>
  </div>
  
      </form>
      
    </div>
  
  </div>
  
</div>
<!--Do not edit this code below -->
<script type="text/javascript"><!--
<?php if ($error_description) { ?>
alert('Please fill required fields. Check all TABS.');
<?php }?>
		
		
				  
$('#send-reason').live('click', function() {
           
			$.ajax({
		type: 'POST',
		url: 'index.php?route=payment/pp_pro_recurring/addReason&token=<?php echo $token; ?>',
		dataType: 'json',
		data: $('#reasonstext textarea'),
		beforeSend: function() {
			$('#reasonstext').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
			
			if (json['error']) {
				alert(json['error']);				
			}else{
			    var counter = json.length;
			
				$('#reasonstext').before('<div class="success">' + 'Success: Reason Saved!' + '</div>');
					$('#pmon').val("");
				$('#reasons').html("");
				for (i = 0; i < json.length; i++) {
						$('#reasons').prepend('<span class="number-reason">' + counter  + '.' + '</span>' + json[i]['reason']+'<br>');
					counter--;
					
				}
				
			}
		}
	});	
								
												
	});	
	
	$('#send-reason-refund').live('click', function() {
           
			$.ajax({
		type: 'POST',
		url: 'index.php?route=payment/pp_pro_recurring/addRefundReason&token=<?php echo $token; ?>',
		dataType: 'json',
		data: $('#refundreasonstext textarea'),
		beforeSend: function() {
			$('#refundreasonstext').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			$('.success, .warning').remove();
			
			if (json['error']) {
				alert(json['error']);				
			}else{
			    var counter = json.length;
			
				$('#refundreasonstext').before('<div class="success">' + 'Success: Reason Saved!' + '</div>');
					$('#pmon2').val("");
				$('#refundreasons').html("");
				for (i = 0; i < json.length; i++) {
						$('#refundreasons').prepend('<span class="number-reason">' + counter  + '.' + '</span>' + json[i]['reason']+'<br>');
					counter--;
					
				}
				
			}
		}
	});	
								
												
	});		  
		  
		  
		 
$('.colorbox').colorbox({
	overlayClose: true,
	opacity: 0.5
});
//--></script> 
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
CKEDITOR.replace('email_confirm1', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
CKEDITOR.replace('email_confirm2', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
CKEDITOR.replace('email_confirm3', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
CKEDITOR.replace('screen_confirm1', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
CKEDITOR.replace('screen_confirm2', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
CKEDITOR.replace('screen_confirm3', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script> 

<script type="text/javascript"><!--
<?php if($trialproducts && !$pp_pro_recurring_trial) { ?>
alert('The system has detected that you have active trial products but no trial(s) configured. You need to make sure to configure one or more trials or customers will not be able to check out with trial items');
<?php }else if($trial_count_products != $trial_count){?>	
alert('The system has detected that you have more active trial products then there are active trials. Please configure more trials or customers run the risk in not being able to check out with trial items');
<?php }?>

<?php if($this->config->get('pp_pro_recurring_billingperiod')){?>
	$("#pp_pro_recurring_billingperiod").val("<?php echo $this->config->get('pp_pro_recurring_billingperiod');?>");
<?php }?>
<?php if($this->config->get('pp_pro_recurring_autobill')){?>
	$("#pp_pro_recurring_autobill").val("<?php echo $this->config->get('pp_pro_recurring_autobill');?>");
<?php }?>
<?php if($this->config->get('pp_pro_recurring_initialamountfail')){?>
	$("#pp_pro_recurring_initialamountfail").val("<?php echo $this->config->get('pp_pro_recurring_initialamountfail');?>");
<?php }?>
$('#pp_pro_recurring_trial').live('change', function() {
	 var selectedEffect = "blind";	
	 var options = {};
	var value = $(this).val();
	if(value == "1"){
		$( "#trial-info" ).show( selectedEffect, options, 100);
		
	}else{
		$( "#trial-info" ).hide( selectedEffect, options, 100);
	}
	
	});
$('#pp_pro_recurring_usetotal').live('change', function() {	
	var value = $(this).val();
	if(value == "0"){
		$('#pp_pro_recurring_amount').show();
		$('#pp_pro_recurring_amount').focus();
	}else{
		$('#pp_pro_recurring_amount').hide();
	}
	
	});
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
$('#tabs a').tabs(); 
$('.date').datepicker({dateFormat: 'yy-mm-dd'});
//--></script> 
<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script> 
<?php echo $footer; ?>