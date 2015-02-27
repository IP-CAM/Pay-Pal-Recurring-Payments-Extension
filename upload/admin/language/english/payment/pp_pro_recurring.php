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
// Heading
$_['heading_title']      = 'PayPal Website Payment Pro (Recurring)';

// Text 
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified PayPal Website Payment Pro (Recurring) Checkout account details!';
$_['text_pp_pro_recurring']        = '<a onclick="window.open(\'https://www.paypal.com/pdn-recurring\');"><img src="view/image/payment/paypalprorecurring.png" alt="PayPal Website Payment Pro (Recurring)" title="PayPal Website Payment Pro (Recurring)" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_authorization'] = 'Authorization';
$_['text_sale']          = 'Sale';
$_['text_random_ref']          = 'Use Random String';
$_['text_orderid_ref']          = 'Use Order ID';
$_['text_customerid_ref']          = 'Use Cusstomer ID';


$_['text_month']          = 'Month';
$_['text_semimonth']          = 'Semi-Month';
$_['text_day']          = 'Day';
$_['text_year']          = 'Year';

//Tabs
$_['text_general']     = 'General Settings';
$_['text_ipn']     = 'IPN Settings';
$_['jump_button']     = 'Mange Exisiting Orders';
$_['productjump_button']     = 'Mange Recurring Items';
$_['text_configuration']     = 'Configuration Settings';
$_['text_documentation']     = 'Official Documentation';
$_['text_trial_setup']     = 'Dynamic Trial Setup';

$_['text_emailtemplates']     = 'Email Settings';
// Entry
$_['entry_username']     = 'API Username:';
$_['entry_password']     = 'API Password:';
$_['entry_signature']    = 'API Signature:';
$_['entry_test']         = 'Test Mode:<br /><span class="help">Use the live or testing (sandbox) gateway server to process transactions?</span>';
$_['entry_transaction']  = 'Transaction Method:';
$_['entry_total']        = 'Total:<br /><span class="help">The checkout total the order must reach before this payment method becomes active.</span>';
$_['entry_expired_status']  = 'Expired Profile Status:';
$_['entry_refunded_status']  = 'Refunded Order Status:';

$_['entry_order_status'] = 'Order Status:';
$_['entry_declined_transaction_status'] = 'Declined Transaction Status:';
$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';
$_['entry_startdate']   = 'Start Date:';
$_['entry_reference']   = 'Reference:';
$_['entry_description']   = 'Description:';
$_['specialitems']   = 'Recurring Items Setup';
$_['specialitemstext']   = 'Here you can set up custom billing cycle information to items that were marked as Recurring in the product set up.';

$_['entry_billingfrequency']     = 'Default Billing Frequency:';
$_['entry_billingperiod']     = 'Default Billing Period:';
$_['entry_amount']       = 'Amount:';
$_['entry_userordertotal']   = 'Use Order Total and/or Combo Items Price';

$_['entry_userorderid']   = 'Use Order ID';
$_['entry_offer_free']   = 'No Charge<br><small>Please ready help bubble)</small>';
$_['entry_offer_free_text']   = 'All item(s) will be offered but at 0 charge. The only cost will be the original set cost of the recurring profile. <strong>NOTE:</strong> Typically the overall cost is calculated when setting up the profile where the cost of products to be added is included.. That is why this is checked off as default.';

$_['entry_must']        = '(Not recommended unless you are not displaying prices on <strong>regular</strong> items.)';
$_['entry_same']        = '(For this, the reference # will be the same for every profile created)';
$_['entry_totalbilligcycles']        = 'Default Total Billing Cycles:';
$_['entry_cycles']        = 'If left blank, then infinite cycles until updated';
$_['entry_start_date']        = 'Please read the TOOL TIP for important information regarding this paramater.';
$_['entry_frequency']        = '<strong>Note: This will be used if you do not assign a custom value to those items marked RECURRING.</strong> Number of billing periods that make up one billing cycle. The combination of billing frequency and billing period must be less than or equal to one year. <strong>For example:</strong> if the billing cycle is Month, the maximum value for billing frequency is 12. Similarly, if the billing cycle is Week, the maximum value for billing frequency is 52.';
$_['entry_periodtext']   = '<strong>Note: This will be used if you do not assign a custom value to those items marked RECURRING.</strong> Unit for billing during this subscription period. For SemiMonth, billing is done on the 1st and 15th of each month.  <strong>Note:</strong> The combination of BillingPeriod and BillingFrequency cannot exceed one year.';
$_['cycles_text']   = '<strong>Note: This will be used if you do not assign a custom value to those items marked RECURRING.</strong> Number of billing cycles for payment period. For the regular payment period, if no value is specified or the value is 0, the regular payment period continues until the profile is canceled or deactivated. For the regular payment period, if the value is greater than 0, the regular payment period will expire after the trial period is finished and continue at the billing frequency for TotalBillingCycles cycles.';
$_['startdate_text']   = 'If you want the profile to start on the day of the order then <strong>LEAVE BLANK.</strong> <strong>Note:</strong> The profile may take up to 24 hours for activation. This is the date when billing for this profile begins.  Here you may choose a number of days the profile will start after <strong>initial payment</strong>. If left blank, the profile will start the day of the initial order whether there is an initial payment set or not. Note: If you set up a trial subscription then make sure to start the main profile AFTER the trial ends. It is up to you to do the math. For example if your trial has 3 billing cycles for 15 days each, you will want to set the profile to start 45 days from date of order.';
$_['startdate_text_small'] = "from date of order (<small>meaning initial order</small>)";
$_['description_text']   = 'Description of the recurring payment. Character length and limitations: 127 characters';
$_['amount_text']   = 'Billing amount for each regular billing cycle during this payment period. This amount is dynamically generated according to the settings you chose in CATALOG>PRODUCTS when setting up Combo Items. This is for Regular Profile which means AFTER a trial has expired. It is recommended you use the first setting and let the system generate the price according to your settings.';
$_['reference_text']   = 'The merchants own unique reference or invoice number. The Order ID MUST be used in this case. Without it, later IPN messages will not be able to be recognized.';
$_['entry_subscribername']   = 'Subscriber Name:';
$_['subscribername_text']   = 'Full name of the person receiving the product or service paid for by the recurring payment. If left blank, the first and last billing name is used from Open Cart checkout.';

$_['subscribeminitext']   = 'If left blank, the first and last billing name is used from Open Cart checkout.';
$_['entry_autobill']   = 'Autobill Amount Action';
$_['autobill_text']   = 'Indicates whether you would like PayPal to automatically bill the outstanding balance amount in the next billing cycle. The outstanding balance is the total amount of any previously failed scheduled payments that have yet to be successfully paid. Normally this is not enabled';
$_['entry_initialamount']   = 'Initial Amount:';
$_['initialamount_text']   = 'Initial non-recurring payment amount due immediately upon profile creation. Use an initial amount for enrolment or set-up fees. All amounts included in the request must have the same currency.';
$_['entry_maxfailed']   = 'Max Failed Payments:';
$_['maxfailed_text']   = 'Number of scheduled payments that can fail before the profile is automatically suspended. An IPN message is sent to Open Cart when the specified number of failed payments is reached.';

$_['entry_initialamountfail']   = 'Initial Amount Fail Action:';
$_['entry_ContinueOnFailure']   = 'Continue On Failure';
$_['entry_CancelOnFailure']   = 'Cancel On Failure';
$_['initialamountfail_text']   = '<strong>ContinueOnFailure</strong> – By default, PayPal suspends the pending profile in the event that the initial payment amount fails. You can override this default behavior by setting this field to ContinueOnFailure. Then, if the initial payment amount fails, PayPal adds the failed payment amount to the outstanding balance for this recurring payment profile. When you specify ContinueOnFailure, a success code is returned to you in the CreateRecurringPaymentsProfile response and the recurring payments profile is activated for scheduled billing immediately. You should check your IPN messages or PayPal account for updates of the payment status. <strong>CancelOnFailure</strong> – If this field is not set or you set it to CancelOnFailure, PayPal creates the recurring payment profile, but places it into a pending status until the initial payment completes. If the initial payment clears, PayPal notifies you by IPN that the pending profile has been activated. If the payment fails, PayPal notifies you by IPN that the pending profile has been canceled.';

$_['entry_taxamount']   = 'Tax Amount';
$_['taxamount_text']   = 'Tax amount for each billing cycle during this payment period. <strong>Note:</strong> IF YOU FILL OUT A TAX AMOUNT IT WILL BE ADDED IN ADDITION TO THE TAX OPENCART IS USING.';
$_['entry_taxamount_tiny']   = 'If left blank, tax will be used set up by Open Cart Store. This is normally left blank';

$_['pp_pro_recurring_showipn_text']   = 'Confirmations email will be sent to all of your customers enrolled whenever a transaction has taken place. Paypal sends an IPN message to Opencart and an email is sent. For more on IPN check out the documentation. We recommend that you leave this ENABLED.';
$_['pp_pro_recurring_send_new_text']   = 'When a new recurring profile is created an email is sent to the Admin letting them know. Here, you can choose to disable this feature.';

$_['entry_yes']   = 'Yes';
$_['entry_no_trial']   = 'No';
$_['entry_no']   = 'Custom';

$_['type_a']   = 'Type A';
$_['type_b']   = 'Type B'; 
$_['entry_master_type']   = 'Choose A Master Profile Type:';
$_['entry_master_type_text']   =   'Out of the many ways to set up a recurring profile we have narrowed the list down to two basic ways. It all depends on your customers needs and what type of company you are running. Choosing one or the other will help the system determain certain templates to send as well as help the configuration of order totals and how products relate.';
$_['entry_master_type_text_b']   = 'Append Products (enabled) For store owners that have the need to run a <strong>TRIAL OFFER</strong> where at the end of the TRIAL they start the regular subscription, BUT with a different item(s). Perhaps also a store owner will be shipping physical goods or offering downloads upon each regular billing cycle. Choosing this will enable the <strong>APPEND PRODUCTS</strong> feature and enable a custom email template(s) which is configurable below. Since there are products involved the confirmation email(s) will show products just like default. <strong>NOTE:</strong> Order totals are NOT defined by the cost of products. A single recurring profile amount must be set in the parameters below. Therefore it is up to you to total the cost of all products + possible shipping, and make THAT the total of the profile.';
$_['entry_master_type_text_a']   = 'Append Products (disabled) For store owners that are setting up a typical subscription where the customer is charged per billing cycle just to be a member. Most of the time this is for a SERVICE instead of a physical product to be shipped. You can still choose this if you are offering a Trial but you will <strong>NOT</strong> be able to alter/add items when the trial ends and regular begins.  You will set up a Recurring Product as a normal item just like you would do it for a real product, except you will choose <strong>Mark As Recurring Item.</strong> Choosing this will enable a custom email template(s) which is configurable below. Since there are no extra products involved then the order confirmation email(s) will only show the default text + the Recurring Item. <strong>NOTE:</strong> Order totals are NOT defined by the cost of products. A single recurring profile amount must be set in the parameters below. Therefore it is up to you to total the cost of all products + possible shipping, and make THAT the total of the profile. ';


$_['entry_trial']   = 'Create Trial(s)?';
$_['trial_text']   = 'An optional subscription period before the regular payment period begins. A trial period may not have the same billing cycles and payment amounts as the regular payment period. Multiple Trials can be created because you might have the need to offer many at the same time with different expirations and prices.<br><br><strong>Trial Billing Period:</strong><br>Unit for billing during this subscription period; required if you specify an optional trial period. For SemiMonth, billing is done on the 1st and 15th of each month.<br><br><strong>Trial Billing Frequency:</strong><br>Number of billing periods that make up one billing cycle; required if you specify an optional trial period. The combination of billing frequency and billing period must be less than or equal to one year. For example, if the billing cycle is Month, the maximum value for billing frequency is 12. Similarly, if the billing cycle is Week, the maximum value for billing frequency is 52.  <strong>Note:</strong> If the billing period is SemiMonth, the billing frequency must be 1.<br><br><strong>Trial Billing Cycles</strong><br>(optional) Number of billing cycles for trial payment period.';
$_['entry_trialbillingperiod']   = 'Trial Billing Period:';
$_['trialbillingperiod_text']   = '';

$_['trialbillingfrequency_text']   = '';
$_['entry_trialbillingfrequency']   = 'Trial Billing Frequency';

$_['trialbillingcyclesoption']   = '<strong>Append product</strong><br> <small>(Trial billing cycles)</small>';
$_['trialbillingcyclesoption_text']   = '';		
$_['entry_regularbillingcyclesoption']   = '<strong>After Trial Append Product</strong> <br><small>(Regular billing cycles)</small>';
$_['entry_regularbillingcyclesoption_text']   = 'Sometimes store owners set up recurring payments that include a trial that involve a unique product/service. Furthermore, some trials have the same product as the regular period where only the COST changes. This option only works after a trial has ended, and the regular recurring profile has begun;  it allows you to add a product/products to the billing cycle beginning with the first, and every other billing cycle afterwards. <strong>NOTE:</strong> you need to set up the regular recurring amount to include the cost of the new product(s). If you forgot, and need to change the recurring profile total in an existing profile then do so in SALES>RECURRING ORDERS-manage. To make the item(s) free then simply alter the recurring profile amount to deduct the cost of the items you APPEND.<br><br> IMPORTANT: to utalize this feature properly you to choose <strong>Use Initial Order Total</strong> when defining the Recurring Profie Amount.';


$_['trialcycles_text']   = '';
$_['entry_trialcycles']   = 'Trial Total Billing Cycles';
$_['entry_offer_continue']   = 'Display appended products price/total';
$_['entry_offer_continue_text']   = 'This displays the individual item price and item total for those products Appended to the profile on the email(s) as well as the Recurring Orders details page. <strong>NOTE:</strong> It will not affect the profile total. This is only for visual reference. If left unchecked the products will display only their name. <strong>REMINDER:</strong> The Recurring Profile needs to use the Order Total for the amount both for trial period and regular. Appending products does not alter the profile price at all. You have to do the math and perhaps even UPDATE the profile to match the cost of Appended Products';

$_['trialamount_text']   = 'Billing amount for each billing cycle during this payment period; required if you specify an optional trial period. This amount does not include shipping and tax amounts.';
$_['entry_trialamount']   = 'Trial Amount';

$_['shippingamount_text']   = 'Shipping amount for each billing cycle during this payment period. <strong>NOTE:</strong> THIS WILL BE ADDED TO THE DEFAULT SHIPPING CHARGES SET UP IN YOUR OPENCART STORE';
$_['entry_shippingamount']   = 'Shipping Amount';
$_['entry_shippingamount_tiny']   = 'If left blank, the system will use shipping charges set up with Open Cart and the checkout process';


$_['entry_email_confirm1']   = 'Send Email Confirmation 1:';
$_['entry_email_confirm2']   = 'Send Email Confirmation 2:';
$_['entry_email_confirm3']   = 'Send Email Confirmation 3:';
$_['entry_screen_confirm1']   = 'Confirmation Page 1:';
$_['entry_screen_confirm2']   = 'Confirmation Page 2:';
$_['entry_screen_confirm3']   = 'Confirmation Page 3:';

$_['entry_email_confirm1_text']   = 'Default order success email confirmation. This is sent for transactions where the credit card was approved and the individual was enrolled in a recurring profile. Recurring Payments uses its own custom template. If disabled, no email will be sent, only the success screen will be shown.';

$_['entry_email_confirm2_text']   = 'This confirmation is similar to the default but displays text to let the individual know that there was a problem with their payment and/or that it was declined. This is NOT the same as if they were to enter a bad credit card number. This involves issues on the Pay Pal Side with the Merchant Bank and the card holder. The customer also is made aware that the outstanding balance will be applied to the next billing cycle and that their profile is active. If disabled, no email will be sent, only the success screen will be shown.';

$_['entry_email_confirm3_text']   = 'This confirmation is similar to the default but displays text to let the individual know that there was a problem with their payment and/or that it was declined. This is NOT the same as if they were to enter a bad credit card number. This involves issues on the Pay Pal Side with the Merchant Bank and the card holder.The customer also is made aware that the their recurring profile is suspended until payment clears. If no clear, then it will cancel. If disabled, no email will be sent, only the success screen will be shown.';

$_['entry_screen_confirm1_text']   = 'The is the web page that the customer is re-directed to on a normal checkout success where credit card is approved. Recurring Payments uses its own custom page because of the special nature of the order. Make sure to fill out this field with information relative to the enrollment/subscription.';

$_['entry_screen_confirm2_text']   = 'This custom page is where the customer is redirected after a failed transaction, and displays text to let the individual know that there was a problem with their payment and/or that it was declined. This is NOT the same as if they were to enter a bad credit card number. This involves issues on the Pay Pal Side with the Merchant Bank and the card holder. The customer also is made aware that the outstanding balance will be applied to the next billing cycle and that their profile is active. If disabled, no email will be sent, only the success screen will be shown.';

$_['entry_screen_confirm3_text']   = 'This custom page is where the customer is redirected after a failed transaction, and displays text to let the individual know that there was a problem with their payment and/or that it was declined. This is NOT the same as if they were to enter a bad credit card number. This involves issues on the Pay Pal Side with the Merchant Bank and the card holder. The customer also is made aware that the their recurring profile is suspended until payment clears. If no clear, then it will cancel. If disabled, no email will be sent, only the success screen will be shown.';

$_['email_template_info']   = '<p>List any additional information you would like the customer to see below that is relative to your enrollemnt plan. It will be displayed at the top of the email. If left empty, the default text will show. </p><p><a href="%s"  class="colorbox" rel="colorbox"><strong>Preview Template</strong></a></p>';

$_['entry_screen_confirm1_info']   = '<p>List any additional information you would like the customer to see below that is relative to your enrollemnt plan. It will be displayed at the top of the email. If left empty, the default text will show. </p><p><a href="%s"  class="colorbox" rel="colorbox"><strong>Preview Screen</strong></a></p>';

$_['entry_screen_confirm2_info']   = '<p>List any additional information you would like the customer to see below that is relative to your enrollemnt plan. It will be displayed at the top of the email. If left empty, the default text will show. </p><p><a href="%s"  class="colorbox" rel="colorbox"><strong>Preview Screen</strong></a></p>';

$_['entry_screen_confirm3_info']   = '<p>List any additional information you would like the customer to see below that is relative to your enrollemnt plan. It will be displayed at the top of the email. If left empty, the default text will show. </p><p><a href="%s"  class="colorbox" rel="colorbox"><strong>Preview Screen</strong></a></p>';

$_['display_order_list_text']   = 'Displays whether order is recurring or not on the NORMAL ORDER LIST PAGE.  This is helpful for websites that use more than one payment option or for those that just want to have a quick look without having to go into the order details. Manage order will take you to the custom recurring details page.<strong>MAY NOT WORK WITH CUSTOM ORDER DISPLAY EXTENSIONS.</strong>';

$_['display_order_list_trial_text']   = 'Displays if recurring order has a trial period on the NORMAL ORDER LIST PAGE. <strong>MAY NOT WORK WITH CUSTOM ORDER DISPLAY EXTENSIONS.</strong>';

$_['reasons_text']   = 'When manageing a recurring profile from within the order details page,  you have the option of <strong>PRE-LOADING</strong> a text comment. Here you may create various comments to <strong>PRE-LOAD</strong>. These comments will be added to the database when managing an order. For example when you CANCEL you will want to give a reason as to why.';
$_['refund_reasons_text']   = 'When manageing a refunds from within the order details page,  you have the option of <strong>PRE-LOADING</strong> a text comment. Here you may create various comments to <strong>PRE-LOAD</strong>. These comments will be added to the database when managing an order. ';

$_['template_logo_text']   = '<p>You can replace your store logo here. By default customers will see this at the top of their email confirmation.</p>';
$_['template_logo']   = 'Edit Confirmation Email Logo';
$_['text_image_manager']           = 'Image Manager';
$_['text_browse']                  = 'Browse Files';
$_['text_clear']                   = 'Clear Image';
$_['text_order']                   = 'Text Settings';
$_['text_catalog']                   = 'Store Front Settings';

$_['entry_display_order_list']       = 'Display Recurring Status (Default Order List)';
$_['entry_display_in_orderlist']       = 'Show Recurring Orders In:  (Default Order List)';

$_['display_in_orderlist_text']       = 'This will allow you to see the recurring orders in the default order list display. Manageing them will still take you to the recurring order page.';

$_['entry_display_trial_order_list']       = 'Display Recurring Trial Status (Default Order List)';
$_['ipn_text']       = '<p style="color:#FF0000;font-weight:bold;">It is critical that you follow these simple instructions to set up your IPN URL.</p> <p>Without IPN, your database will have no way of being updated with the continuous recurring orders coming through. Every time a recurring order is made Paypal sends data to Opencart via IPN. This extension parses that data and executes functions whereby maintaining the entire system. You <strong>MUST</strong> set this up correctly. Below are step by step instructions:</p>';
$_['reasons']       = 'Profile Management (Preloaded Reasons)';
$_['refundreasons']       = 'Refund Management (Preloaded Reasons)';
$_['type_text']          = 'Type Here...';
$_['save_text']          = 'Save';
$_['may_not_work']          = 'May not work with custom order list display extensions';

$_['entry_enable']   = 'Enable';
$_['entry_disable']   = 'Disable';

$_['entry_ipn_send_new']   = 'Send IPN Emails to Admin:';
$_['entry_ipn_display']   = 'Send IPN Emails to Customers:';
// Error
$_['error_permission']   = 'Warning: You do not have permission to modify payment PayPal Website Payment Pro Checkout!';
$_['error_username']     = 'API Username Required!'; 
$_['error_password']     = 'API Password Required!'; 
$_['error_signature']    = 'API Signature Required!'; 
$_['error_required']    = 'Required Field!'; 
?>