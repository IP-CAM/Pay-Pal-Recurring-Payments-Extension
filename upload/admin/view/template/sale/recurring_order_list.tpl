<?php //==============================================================================
// Pay Pal Recurring Payments
// 
// Author: Avvici, Involution Media
// E-mail: joseph@involutionmedia.com
// Website: http://www.involutionmedia.com
//==============================================================================
//Edit code with care. Always comment your code to take notes on what was changed so you can go back to it later.
//============================================================================== ?>
<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/recurring.jpg" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').attr('action', '<?php echo $invoice; ?>'); $('#form').attr('target', '_blank'); $('#form').submit();" class="button"><?php echo $button_invoice; ?></a><a onclick="$('#form').attr('action', '<?php echo $delete; ?>'); $('#form').attr('target', '_self'); $('#form').submit();" class="button"><?php echo $button_delete; ?></a></div>
    </div>
    <div class="content">
   
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
               <td class="right"><?php if ($sort == 'o.paypal_recurringprofile_id') { ?>
                <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_profile_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order; ?>"><?php echo $column_profile_id; ?></a>
                <?php } ?></td>
              <td class="right"><?php if ($sort == 'o.order_id') { ?>
                <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'customer') { ?>
                <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'status') { ?>
                <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                <?php } ?></td>
                 
			 
			  <td class="left">
			   <?php echo $hastrial;?>
              
              </td>
               <td class="left">
			   <?php echo $column_parent_orders;?> <a href="javascript:%20void(0)" onmouseover="Tip('Every Recurring Profile will normally have many recurring orders as part of that profile. Every time a credit card is charged at the end of a billing cycle, that counts as a parent order. One PARENT ORDER & many CHILD ORDERS under the same Profile ID. You can view all of the parent orders in the order details',TITLE,'Recurring Order Details',DURATION,0,FOLLOWMOUSE,true,STICKY,false,PADDING, 5,WIDTH,400)" onmouseout="UnTip()"><img src="view/image/help.png" style="position:relative;top:2px;" alt="" width="16" height="16" border="0"/></a>
              
              </td>
               <td class="left">
			   <?php echo $column_recurring_profile_aggtotal;?>
              
              </td>
            
              <td class="left"><?php if ($sort == 'o.date_added') { ?>
                <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'o.date_modified') { ?>
                <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
                <?php } ?></td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
               <td align="right"><input type="text" name="filter_profile_id" value="<?php echo $filter_profile_id; ?>"  style="text-align: right;" /></td>
              <td align="right"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
              <td><input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" /></td>
              <td><select name="filter_order_status_id">
                  <option value="*"></option>
                  <?php if ($filter_order_status_id == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_missing; ?></option>
                  <?php } ?>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
          
			
			<td></td>
            <td></td>
			<td></td>
              <td><input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" size="12" class="date" /></td>
              <td><input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" size="12" class="date" /></td>
              <td align="right"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></td>
            </tr>
            <?php if ($orders) { ?>
            <?php $counter = 0;?>
            <?php foreach ($orders as $order) { ?>
        
       <div style='display:none'>
			<div style="padding:10px; background:#fff;height:400px;overflow:auto;" id='inline-content<?php if($order['parent_orders'] != "0"){ echo $counter; }?>'>
            <div style= "float:left; font-size:18px;">Recurring Orders <br /><small>Profile ID: (<?php echo $order['profile_id']; ?>)<br /><a href="<?php echo $order['manage'];?>">[ Manage Profile ]</a></small></div>
            <div style= "float:right; font-size:12px;">Profile Status<br /> <small>(<?php if($order['status'] == 'Active'){ echo '<span style="color:#090;">' . $order['status'] . '</span>'; }elseif($order['status'] == 'Suspended'){ echo '<span style="color:#F60;">' . $order['status'] . '</span>' ; }else{ echo $order['status']; } ?>)</small></div>
            <div style="clear:both;margin-bottom:30px;"></div>
  <?php if ($order['parent_orders_list']) { ?>
  
  <?php foreach ($order['parent_orders_list'] as $parentorder) { ?>
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
            <tr>
              <td style="text-align: center;"><?php if ($order['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                <?php } ?></td>
              <td class="right"><?php echo $order['profile_id']; ?></td>
              <td class="right"><?php echo $order['order_id']; ?></td>
              <td class="left"><?php echo $order['customer']; ?></td>
              <td class="left"><?php if($order['status'] == "Active"){ echo '<span style="color:#090;">'.$order['status'].'</span>'; }elseif($order['status'] == "Suspended"){ echo '<span style="color:#F60;">'.$order['status'].'</span>'; }else{ echo $order['status'];} ?></td>
               <td class="left"><?php echo $order['is_trial']; ?></td>
               <td class="left"><?php if($order['parent_orders'] != "0"){ ?>  <a id="inline<?php echo $counter;?>"  href="#inline-content<?php echo $counter;?>"><img src="view/image/log_small.png" style="position:relative; top:2px;" alt="View Recent" /> <small>(quick view)</small></a> <?php echo $order['parent_orders']; ?>  <?php }else{ echo $order['parent_orders'];  }?></td>
              <td class="right"><?php echo $order['total_gross']; ?></td>
              <td class="left"><?php echo $order['date_added']; ?></td>
              <td class="left"><?php echo $order['date_modified']; ?></td>
              <td class="right"><?php foreach ($order['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>
            </tr>
            <script> $("#inline<?php if($order['parent_orders'] != '0'){ echo $counter; }?>").colorbox({inline:true, width:"50%"}); </script>
            <?php $counter++;?>
            
            <?php } ?>
         
            
            <?php } else { ?>
            <tr>
             
			 <td class="center" colspan="11"> <?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
   
</div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=sale/recurring_order&token=<?php echo $token; ?>';
	
	
	var filter_profile_id = $('input[name=\'filter_profile_id\']').attr('value');
	
	if (filter_profile_id) {
		url += '&filter_profile_id=' + encodeURIComponent(filter_profile_id);
	}
	
	
	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	
	var filter_customer = $('input[name=\'filter_customer\']').attr('value');
	
	if (filter_customer) {
		url += '&filter_customer=' + encodeURIComponent(filter_customer);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
	
	if (filter_order_status_id != '*') {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	

	
	
	var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	var filter_date_modified = $('input[name=\'filter_date_modified\']').attr('value');
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
				
	location = url;
}
//--></script>  
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script> 
<script type="text/javascript"><!--
$.widget('custom.catcomplete', $.ui.autocomplete, {
	_renderMenu: function(ul, items) {
		var self = this, currentCategory = '';
		
		$.each(items, function(index, item) {
			if (item.category != currentCategory) {
				ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
				
				currentCategory = item.category;
			}
			
			self._renderItem(ul, item);
		});
	}
});

$('input[name=\'filter_customer\']').catcomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						category: item.customer_group,
						label: item.name,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_customer\']').val(ui.item.label);
						
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
});
//-->
$('.colorbox').colorbox({
	overlayClose: true,
	opacity: 0.5,
	innerHeight: 370,
});

</script> 
<?php echo $footer; ?>