<?php
/**
 * Plugin Name: Authorize.Net WooCommerce Addon.
 * Plugin URI: https://wordpress.org/plugins/authorizenet-woocommerce-addon/
 * Description: This plugin adds a payment option in WooCommerce for customers to pay with their Credit Cards Via Authorize.Net.
 * Version: 1.0.2
 * Author: Syed Nazrul Hassan
 * Author URI: https://nazrulhassan.wordpress.com/
 * License: GPLv2
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function authorizenet_init()
{

include(plugin_dir_path( __FILE__ )."lib/AuthorizeNet.php");

function add_authorizenet_gateway_class( $methods ) 
{
	$methods[] = 'WC_Authorizenet_Gateway'; 
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'add_authorizenet_gateway_class' );

if(class_exists('WC_Payment_Gateway'))
{
	class WC_Authorizenet_Gateway extends WC_Payment_Gateway 
	{
		
		public function __construct()
		{

		$this->id               = 'authorizenet';
		$this->icon             = apply_filters( 'woocommerce_authorizenet_icon', plugins_url( 'images/authorizenet.png' , __FILE__ ) );
		$this->has_fields       = true;
		$this->method_title     = 'Authorize.Net Cards Settings';		
		$this->init_form_fields();
		$this->init_settings();
		$this->supports                     = array(  'products',  'refunds');
		$this->title			           = $this->get_option( 'authorizenet_title' );
		$this->authorizenet_apilogin        = $this->get_option( 'authorizenet_apilogin' );
		$this->authorizenet_transactionkey  = $this->get_option( 'authorizenet_transactionkey' );
		$this->authorizenet_sandbox         = $this->get_option( 'authorizenet_sandbox' ); 
		$this->authorizenet_authorize_only  = $this->get_option( 'authorizenet_authorize_only' ); 
		$this->authorizenet_cardtypes       = $this->get_option( 'authorizenet_cardtypes'); 

		if(!defined("AUTHORIZE_NET_SANDBOX"))
		{ define("AUTHORIZE_NET_SANDBOX", ($this->authorizenet_sandbox 	   =='yes'? true : false)); }
		if(!defined("AUTHORIZENET_TRANSACTION_MODE"))
		{ define("AUTHORIZENET_TRANSACTION_MODE", ($this->authorizenet_authorize_only =='yes'? true : false));}
		
		
		if('yes' == AUTHORIZE_NET_SANDBOX )
		{	if(!defined("AUTHORIZENET_API_LOGIN_ID"))
			{define("AUTHORIZENET_API_LOGIN_ID",    $this->authorizenet_apilogin );       }
			if(!defined("AUTHORIZENET_TRANSACTION_KEY"))
			{define("AUTHORIZENET_TRANSACTION_KEY", $this->authorizenet_transactionkey ); }
			if(!defined("AUTHORIZENET_SANDBOX"))
			{ define("AUTHORIZENET_SANDBOX", true); }
			
		}
		else
		{ 	
			if(!defined("AUTHORIZENET_API_LOGIN_ID"))
			{define("AUTHORIZENET_API_LOGIN_ID",    $this->authorizenet_apilogin );       }
			if(!defined("AUTHORIZENET_TRANSACTION_KEY"))
			{define("AUTHORIZENET_TRANSACTION_KEY", $this->authorizenet_transactionkey ); }
			if(!defined("AUTHORIZENET_SANDBOX"))
			{define("AUTHORIZENET_SANDBOX", false);    }
		}
		
		if (is_admin()) 
		{
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
		}

		public function admin_options()
		{
		?>
		<h3><?php _e( 'Authorize.Net addon for WooCommerce', 'woocommerce' ); ?></h3>
		<p><?php  _e( 'Authorize.Net is a payment gateway service provider allowing merchants to accept credit card.', 'woocommerce' ); ?></p>
		<table class="form-table">
		  <?php $this->generate_settings_html(); ?>
		</table>
		<?php
		}

		public function init_form_fields()
		{
		$this->form_fields = array(
		'enabled' => array(
		  'title' => __( 'Enable/Disable', 'woocommerce' ),
		  'type' => 'checkbox',
		  'label' => __( 'Enable Authorize.Net', 'woocommerce' ),
		  'default' => 'yes'
		  ),
		'authorizenet_title' => array(
		  'title' => __( 'Title', 'woocommerce' ),
		  'type' => 'text',
		  'description' => __( 'This controls the title which the buyer sees during checkout.', 'woocommerce' ),
		  'default' => __( 'Authorize.Net', 'woocommerce' ),
		  'desc_tip'      => true,
		  ),
		'authorizenet_apilogin' => array(
		  'title' => __( 'API Login ID', 'woocommerce' ),
		  'type' => 'text',
		  'description' => __( 'This is the API Login ID Authorize.net.', 'woocommerce' ),
		  'default' => '',
		  'desc_tip'      => true,
		  'placeholder' => 'Authorize.Net API Login ID'
		  ),
		
		'authorizenet_transactionkey' => array(
		  'title' => __( 'Transaction Key', 'woocommerce' ),
		  'type' => 'text',
		  'description' => __( 'This is the Transaction Key of Authorize.Net.', 'woocommerce' ),
		  'default' => '',
		  'desc_tip'      => true,
		  'placeholder' => 'Authorize.Net Transaction Key'
		  ),
		
		'authorizenet_sandbox' => array(
		  'title'       => __( 'Authorize.Net sandbox', 'woocommerce' ),
		  'type'        => 'checkbox',
		  'label'       => __( 'Enable Authorize.Net sandbox (Live Mode if Unchecked)', 'woocommerce' ),
		  'description' => __( 'If checked its in sanbox mode and if unchecked its in live mode', 'woocommerce' ),
		  'desc_tip'      => true,
		  'default'     => 'no'
		),
		
		'authorizenet_authorize_only' => array(
		 'title'       => __( 'Authorize Only', 'woocommerce' ),
		 'type'        => 'checkbox',
		 'label'       => __( 'Enable Authorize Only Mode (Authorize & Capture If Unchecked)', 'woocommerce' ),
		 'description' => __( 'If checked will only authorize the credit card only upon checkout.', 'woocommerce' ),
		 'desc_tip'      => true,
		 'default'     => 'no',
		),
		'authorizenet_cardtypes' => array(
			 'title'    => __( 'Accepted Cards', 'woocommerce' ),
			 'type'     => 'multiselect',
			 'class'    => 'chosen_select',
			 'css'      => 'width: 350px;',
			 'desc_tip' => __( 'Select the card types to accept.', 'woocommerce' ),
			 'options'  => array(
				'mastercard'       => 'MasterCard',
				'visa'             => 'Visa',
				'discover'         => 'Discover',
				'amex' 		    => 'American Express',
				'jcb'		    => 'JCB',
				'dinersclub'       => 'Dinners Club',
			 ),
			 'default' => array( 'mastercard', 'visa', 'discover', 'amex' ),
		)
		
	  );
  		}

		public function payment_fields()
		{			
		?>
		<table>
		    <tr>
		    	<td><label for="authorizenet_cardno"><?php echo __( 'Card No.', 'woocommerce') ?></label></td>
			<td><input type="text" name="authorizenet_cardno" class="input-text" placeholder="Credit Card No"  /></td>
		    </tr>
		    <tr>
		    	<td><label for="authorizenet_expiration_date"><?php echo __( 'Expiration Date', 'woocommerce') ?>.</label></td>
			<td>
			   <select name="authorizenet_expmonth" style="height: 33px;">
			      <option value=""><?php _e( 'Month', 'woocommerce' ) ?></option>
			      <option value='01'>01</option>
			      <option value='02'>02</option>
			      <option value='03'>03</option>
			      <option value='04'>04</option>
			      <option value='05'>05</option>
			      <option value='06'>06</option>
			      <option value='07'>07</option>
			      <option value='08'>08</option>
			      <option value='09'>09</option>
			      <option value='10'>10</option>
			      <option value='11'>11</option>
			      <option value='12'>12</option>  
			    </select>
			    <select name="authorizenet_expyear" style="height: 33px;">
			      <option value=""><?php _e( 'Year', 'woocommerce' ) ?></option>
			      <?php
			      $years = array();
			      for ( $i = date( 'y' ); $i <= date( 'y' ) + 15; $i ++ ) 
			      {
					printf( '<option value="20%u">20%u</option>', $i, $i );
			      } 
			      ?>
			    </select>
			</td>
		    </tr>
		    <tr>
		    	<td><label for="authorizenet_cardcvv"><?php echo __( 'Card CVC', 'woocommerce') ?></label></td>
			<td><input type="text" name="authorizenet_cardcvv" class="input-text" placeholder="CVC" /></td>
		    </tr>
		</table>
	        <?php  
		} // end of public function payment_fields()

		/*Get Card Types*/
		function get_card_type($number)
		{
		
		    $number=preg_replace('/[^\d]/','',$number);
		    if (preg_match('/^3[47][0-9]{13}$/',$number))
		    {
		        return 'amex';
		    }
		    elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',$number))
		    {
		        return 'dinersclub';
		    }
		    elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/',$number))
		    {
		        return 'discover';
		    }
		    elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/',$number))
		    {
		        return 'jcb';
		    }
		    elseif (preg_match('/^5[1-5][0-9]{14}$/',$number))
		    {
		        return 'mastercard';
		    }
		    elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$number))
		    {
		        return 'visa';
		    }
		    else
		    {
		        return 'unknown';
		    }
		}// End of getcard type function
		
		
		// Function to check IP
		
		function get_client_ip() 
		{
			$ipaddress = '';
			if (getenv('HTTP_CLIENT_IP'))
				$ipaddress = getenv('HTTP_CLIENT_IP');
			else if(getenv('HTTP_X_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
			else if(getenv('HTTP_X_FORWARDED'))
				$ipaddress = getenv('HTTP_X_FORWARDED');
			else if(getenv('HTTP_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_FORWARDED_FOR');
			else if(getenv('HTTP_FORWARDED'))
				$ipaddress = getenv('HTTP_FORWARDED');
			else if(getenv('REMOTE_ADDR'))
				$ipaddress = getenv('REMOTE_ADDR');
			else
				$ipaddress = '0.0.0.0';
			return $ipaddress;
		}
		
		// End function to check IP 
		
		

		public function process_payment( $order_id )
		{
		global $woocommerce;
		$wc_order 	= new WC_Order( $order_id );
		$cardtype = $this->get_card_type(sanitize_text_field($_POST['authorizenet_cardno']));
			
         		if(!in_array($cardtype ,$this->authorizenet_cardtypes ))
         		{
         			wc_add_notice('Merchant do not support accepting in '.$cardtype,  $notice_type = 'error' );
         			return array (
								'result'   => 'success',
								'redirect' => WC()->cart->get_checkout_url(),
							   );
				die;
         		}
		
		
		$card_num         = sanitize_text_field($_POST['authorizenet_cardno']);
		$exp_year         = sanitize_text_field($_POST['authorizenet_expyear']);
		$exp_month        = sanitize_text_field($_POST['authorizenet_expmonth']);
		$cvc              = sanitize_text_field($_POST['authorizenet_cardcvv']); 
		
		$sale = new AuthorizeNetAIM;
		$sale->amount     = $wc_order->order_total;;
		$sale->card_num   = $card_num;
		$sale->exp_date   = $exp_year.'/'.$exp_month;
		$sale->card_code  = $cvc; 
		
		$customer = (object)array();
		$customer->first_name 			= $wc_order->billing_first_name;
		$customer->last_name 			= $wc_order->billing_last_name;
		$customer->company 				= $wc_order->billing_company;
		$customer->address 				= $wc_order->billing_address_1 .' '. $wc_order->billing_address_2;
		$customer->city 				= $wc_order->billing_city;
		$customer->state 				= $wc_order->billing_state;
		$customer->zip 				= $wc_order->billing_postcode;
		$customer->country 				= $wc_order->billing_country;
		$customer->phone 				= $wc_order->billing_phone;
		$customer->email 				= $wc_order->billing_email;
		$customer->cust_id 				= $wc_order->user_id;
		$customer->invoice_num 			= $wc_order->get_order_number();
		$customer->description        	= get_bloginfo('blogname').' Order #'.$wc_order->get_order_number();
		$customer->ship_to_first_name		= $wc_order->shipping_first_name;
		$customer->ship_to_last_name		= $wc_order->shipping_last_name;
		$customer->ship_to_company    	= $wc_order->shipping_company;
		$customer->ship_to_address		= $wc_order->shipping_address_1.' '. $wc_order->shipping_address_2;
		$customer->ship_to_city			= $wc_order->shipping_city;
		$customer->ship_to_state			= $wc_order->shipping_state;
		$customer->ship_to_zip			= $wc_order->shipping_postcode;
		$customer->ship_to_country		= $wc_order->shipping_country;
		$customer->delim_char              = '|';
		$customer->encap_char              = '';
		$customer->customer_ip 			= $this->get_client_ip();
		$customer->tax			     	= $wc_order->get_total_tax();
		$customer->freight       		= $wc_order->get_total_shipping();
		$customer->header_email_receipt 	= 'Order Receipt '.get_bloginfo('blogname');
		$customer->footer_email_receipt 	= 'Thank you for Using '.get_bloginfo('blogname');

		$sale->setFields($customer);
		
		
		if('yes' == AUTHORIZENET_TRANSACTION_MODE)
		{
			$response = $sale->authorizeOnly();
		}
		else
		{
			$response = $sale->authorizeAndCapture();
		}
		

		if ($response) 
		{
		
			if( (1 == $response->approved) || (4 == $response->approved) )
			{
			
			$wc_order->add_order_note( __( $response->response_reason_text. 'on'.date("d-m-Y h:i:s e"). 'with Transaction ID = '.$response->transaction_id.' using '.strtoupper($response->transaction_type).' and authorization code '.$response->authorization_code , 'woocommerce' ) );
			$wc_order->payment_complete($response->transaction_id);
			WC()->cart->empty_cart();
			
			$transactionmetas = array( 
								'approved' 			=> $response->approved,
								'declined' 			=> $response->declined,
								'error' 				=> $response->error,
								'held' 				=> $response->held,
								'response_code' 		=> $response->response_code,
								'response_subcode' 		=> $response->response_subcode,
								'response_reason_code'   => $response->response_reason_code,
								'authorization_code'  	=> $response->authorization_code,
								'card_type'        		=> $response->card_type,
								'transaction_type'       => $response->transaction_type,
								'account_number'   		=> $response->account_number,
								'cavv_response'		=> $response->cavv_response,
								'card_code_response'     => $response->card_code_response
								);
			
			add_post_meta( $order_id, '_'.$order_id.'_'.$response->transaction_id.'_metas', $transactionmetas);
			
			
			
				return array (
				  'result'   => 'success',
				  'redirect' => $this->get_return_url( $wc_order ),
				);
			}
			else 
			{
				
			$wc_order->add_order_note( __( 'Authorize.Net payment failed.'.$response->response_reason_text.'--'.$response->error_message.' on'.date("d-m-Y h:i:s e").' using '.strtoupper($response->transaction_type) , 'woocommerce' ) );	 
				wc_add_notice($response->error_message, $notice_type = 'error' );
			}
		
		
		} 
		else 
		{
			$wc_order->add_order_note( __( 'Authorize.Net payment failed.'.$response->response_reason_text.'--'.$response->error_message.' on'.date("d-m-Y h:i:s e").' using '.strtoupper($response->transaction_type) , 'woocommerce' ) );	 
			wc_add_notice($response->error_message, $notice_type = 'error' );
		}
		
		} // end of function process_payment()
		
		
		public function process_refund( $order_id, $amount = NULL, $reason = '' )
		{
			
			global $woocommerce;
		     $wc_order 	= new WC_Order( $order_id );
			$trx_id		= get_post_meta( $order_id , '_transaction_id', true );
			$trx_metas   	= get_post_meta( $order_id , '_'.$order_id.'_'.$trx_id.'_metas',true);
			$last_four   	= isset( $trx_metas['account_number'] ) ? esc_attr( $trx_metas['account_number'] ) : '';
			
			$refund  	   	= new AuthorizeNetAIM;
			$customer = (object)array();
			$customer->first_name 			= $wc_order->billing_first_name;
			$customer->last_name 			= $wc_order->billing_last_name;
			$customer->company 				= $wc_order->billing_company;
			$customer->address 				= $wc_order->billing_address_1 .' '. $wc_order->billing_address_2;
			$customer->city 				= $wc_order->billing_city;
			$customer->state 				= $wc_order->billing_state;
			$customer->zip 				= $wc_order->billing_postcode;
			$customer->country 				= $wc_order->billing_country;
			$customer->phone 				= $wc_order->billing_phone;
			$customer->email 				= $wc_order->billing_email;
			$customer->cust_id 				= $wc_order->user_id;
			$customer->invoice_num 			= $wc_order->get_order_number();
			$customer->description        	= get_bloginfo('blogname').' Order #'.$wc_order->get_order_number();
			$customer->ship_to_first_name		= $wc_order->shipping_first_name;
			$customer->ship_to_last_name		= $wc_order->shipping_last_name;
			$customer->ship_to_company    	= $wc_order->shipping_company;
			$customer->ship_to_address		= $wc_order->shipping_address_1.' '. $wc_order->shipping_address_2;
			$customer->ship_to_city			= $wc_order->shipping_city;
			$customer->ship_to_state			= $wc_order->shipping_state;
			$customer->ship_to_zip			= $wc_order->shipping_postcode;
			$customer->ship_to_country		= $wc_order->shipping_country;
			$customer->delim_char              = '|';
			$customer->encap_char              = '';
			$customer->customer_ip 			= $this->get_client_ip();
			$customer->tax			     	= $wc_order->get_total_tax();
			$customer->freight       		= $wc_order->get_total_shipping();
			$customer->header_email_receipt 	= 'Refund From '.get_bloginfo('blogname').' '.$reason;
			$customer->footer_email_receipt 	= 'Thank you for Using '.get_bloginfo('blogname');
			$refund->setFields($customer);
			$refundtrx   	= $refund->credit($trx_id,$amount,$last_four);
	
			
			if(1 == $refundtrx->approved)
			{
			
				$wc_order->add_order_note( __( $refundtrx->response_reason_text. 'on'.date("d-m-Y h:i:s e"). 'with Transaction ID = '.$refundtrx->transaction_id .' using '.strtoupper($refundtrx->transaction_type).' and authorization code '.$refundtrx->authorization_code , 'woocommerce' ) );
				return true;
			}
	          else
	          {
	          
	          	if(2 == $refundtrx->response_subcode || 54 == $refundtrx->response_reason_code)
	          	{
	          		
	          		$refundtrx   	= $refund->void($trx_id);
	          		
					if(1 == $refundtrx->approved)
					{
			
					$wc_order->add_order_note( __( $refundtrx->response_reason_text. 'on '.date("d-m-Y h:i:s e"). 'with Transaction ID = '.$refundtrx->transaction_id .' using '.strtoupper($refundtrx->transaction_type).' and authorization code '.$refundtrx->authorization_code , 'woocommerce' ) );
						return true;
					} 
					else
			
					{
					
				$wc_order->add_order_note( __( $refundtrx->response_reason_text.'--'.$refundtrx->error_message.' on '.date("d-m-Y h:i:s e").' using '.strtoupper($refundtrx->transaction_type) , 'woocommerce' ) );	 		 
						  
						return false;
					}
			
	          	}
	          	else
	          	{     
	            $wc_order->add_order_note( __($refundtrx->response_reason_text.'--'.$refundtrx->error_message.' on '.date("d-m-Y h:i:s e").' using '.strtoupper($refundtrx->transaction_type) , 'woocommerce' ) );	 		 
						  
	          	
	          	return false;
	          	}
	             return false;
	          }
	       return false;   	
		}// end of process_refund function()
		

	}  // end of class WC_Authorizenet_Gateway

} // end of if class exist WC_Gateway

}

add_action( 'plugins_loaded', 'authorizenet_init' );
