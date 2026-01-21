<?php
add_filter( 'woocommerce_payment_gateways', 'skt_paypal_for_woocommerce_add_gateway_class' );
function skt_paypal_for_woocommerce_add_gateway_class( $paypalgatewaysforpp ) {
	$paypalgatewaysforpp[] = 'SKT_PAYPAL_FOR_WOOCOMMERCE_Gateway';
	return $paypalgatewaysforpp;
}

add_action( 'plugins_loaded', 'skt_paypal_for_woocommerce_payment_init_gateway_class' );
function skt_paypal_for_woocommerce_payment_init_gateway_class() {
	class SKT_PAYPAL_FOR_WOOCOMMERCE_Gateway extends WC_Payment_Gateway {
 		public function __construct() {
 			$this->id = 'skt_paypal_for_woocommerce_paypal';
			$this->icon = SKT_PAYPAL_FOR_WOOCOMMERCE_PAYMENT_GATEWAY_URI.'/image/paypal.png';
			$this->has_fields = true;
			$this->method_title = __( 'SKT PayPal For Woocommerce', 'skt-paypal-for-woocommerce' );
			$this->method_description = __( 'Allow payment through PayPal.', 'skt-paypal-for-woocommerce' );
			$this->supports = array(
				'products','subscriptions'
			);
			$this->init_form_fields();
			$this->init_settings();
			$this->title = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->enabled = $this->get_option( 'enabled' );
			$this->testmode = 'yes' === $this->get_option( 'testmode' );
			$this->testskt_paypal_for_woocommerce_private_key = $this->get_option( 'testskt_paypal_for_woocommerce_private_key' );
			$this->testskt_paypal_for_woocommerce_publishable_key = $this->get_option( 'testskt_paypal_for_woocommerce_publishable_key' );
			$this->testskt_paypal_for_woocommece_secret_key = $this->get_option( 'testskt_paypal_for_woocommece_secret_key' );
			$this->skt_paypal_for_woocommerce_private_key = $this->get_option( 'skt_paypal_for_woocommerce_private_key' );
			$this->skt_paypal_for_woocommerce_live_secret_key = $this->get_option( 'skt_paypal_for_woocommerce_live_secret_key' );
			$this->publishable_key = $this->get_option( 'skt_paypal_for_woocommerce_publishable_key' );
			$this->button_layout   = $this->get_option( 'button_layout' );
			$this->button_color    = $this->get_option( 'button_color' );
			$this->button_shape    = $this->get_option( 'button_shape' );
			$this->button_label    = $this->get_option( 'button_label' );
			$this->button_height   = $this->get_option( 'button_height' );
			$this->invoice_prefix  = $this->get_option( 'invoice_prefix' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
 		}
  
 		public function init_form_fields(){
			$this->form_fields = array(
				'enabled' => array(
					'title'       => __( 'Enable/Disable', 'skt-paypal-for-woocommerce' ),
					'label'       => __( 'Enable PayPal', 'skt-paypal-for-woocommerce' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				),
				'title' => array(
					'title'       => __( 'Title', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'skt-paypal-for-woocommerce' ),
					'default'     => __( 'PayPal', 'skt-paypal-for-woocommerce' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Description', 'skt-paypal-for-woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'skt-paypal-for-woocommerce' ),
					'default'     => __( 'Pay with your credit card via PayPal.', 'skt-paypal-for-woocommerce' ),
				),
				'testmode' => array(
					'title'       => __( 'Sandbox Mode', 'skt-paypal-for-woocommerce' ),
					'label'       => __( 'Enable Sandbox Mode', 'skt-paypal-for-woocommerce' ),
					'type'        => 'checkbox',
					'description' => __( 'Place the payment gateway in test mode using test API keys.', 'skt-paypal-for-woocommerce' ),
					'default'     => 'yes',
					'desc_tip'    => true,
				),
				'testskt_paypal_for_woocommerce_publishable_key' => array(
					'title'       => __( 'Sandbox API(Client Id)', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text'
				),
				'testskt_paypal_for_woocommece_secret_key' => array(
					'title'       => __( 'Sandbox Secret Key', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text'
				),
				'testskt_paypal_for_woocommerce_private_key' => array(
					'title'       => __( 'Sandbox PayPal Business Email', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text',
				),
				'skt_paypal_for_woocommerce_publishable_key' => array(
					'title'       => __( 'Live API (Client Id)', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text'
				),
				'skt_paypal_for_woocommerce_live_secret_key' => array(
					'title'       => __( 'Live Secret Key', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text'
				),
				'skt_paypal_for_woocommerce_private_key' => array(
					'title'       => __( 'Live PayPal Business Email', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text'
				),
				'button_color'   => array(
	                'title'       => __( 'Button Color', 'skt-paypal-for-woocommerce' ),
	                'description' => __( "Controls the background color of the primary button. Use 'Gold' to leverage PayPal's recognition and preference, or change it to match your site design or aesthetic.", 'skt-paypal-for-woocommerce' ),
                	'desc_tip'    => true,
                	'type'        => 'select',
	                'options'     => array(
	                    'gold' => 'Gold (Recommended)',
	                    'blue'  => 'Blue',
	                    'silver' => 'Silver',
	                    'black'  => 'Black'
	                )
	            ),
				'button_shape'   => array(
                	'title'       => __( 'Button Shape', 'skt-paypal-for-woocommerce' ),
                	'description' => __( "The pill-shaped button's unique and powerful shape signifies PayPal in people's minds. Use the rectangular button as an alternative when pill-shaped buttons might pose design challenges.", 'skt-paypal-for-woocommerce' ),
					'desc_tip'    => true,
                	'type'        => 'select',
	                'options'     => array(
	                	'pill'  => 'Pill',
	                    'rect' => 'Rectangle'
	                )
	            ),
	            'button_label'   => array(
                	'title'       => __( 'Button Label', 'skt-paypal-for-woocommerce' ),
                	'type'        => 'select',
                	'description' => __( "PayPal offers different labels on the 'PayPal Checkout' buttons, allowing you to select a suitable label.", 'skt-paypal-for-woocommerce' ),
					'desc_tip'    => true,
	                'options'     => array(
	                    'paypal' => 'PayPal',
	                    'checkout'  => 'PayPal Checkout',
	                    'buynow'  => 'PayPal Buy Now',
	                    'pay'  => 'Pay With PayPal'
	                )
	            ),
				'button_layout'   => array(
                'title'       => __( 'Button Layout', 'skt-paypal-for-woocommerce' ),
                'description' => __( "If additional funding sources are available to the buyer through PayPal, such as Card, then multiple buttons are displayed in the space provided. Choose 'vertical' for a dynamic list of alternative and local payment options, or 'horizontal' when space is limited.", 'skt-paypal-for-woocommerce' ),
					'desc_tip'    => true,
                	'type'        => 'select',
	                'options'     => array(
	                    'vertical' => 'Vertical',
	                    'horizontal'  => 'Horizontal'
	                )
	            ),
	            'button_height'   => array(
                	'title'       => __( 'Button Height', 'skt-paypal-for-woocommerce' ),
                	'description' => __( "Control the height of button to match your site design or aesthetic.", 'skt-paypal-for-woocommerce' ),
					'desc_tip'    => true,
                	'type'        => 'select',
	                'options'     => array(
	                    '25' => '25px',
	                    '26' => '26px',
	                    '27' => '27px',
	                    '28' => '28px',
	                    '29' => '29px',
	                    '30'  => '30px',
	                    '31'  => '31px',
	                    '32'  => '32px',
	                    '33'  => '33px',
	                    '34'  => '34px',
	                    '35' => '35px',
	                    '36' => '36px',
	                    '37' => '37px',
	                    '38' => '38px',
	                    '39' => '39px',
	                    '40'  => '40px',
	                    '41' => '41px',
	                    '42' => '42px',
	                    '43' => '43px',
	                    '44' => '44px',
	                    '45' => '45px',
	                    '46' => '46px',
	                    '47' => '47px',
	                    '48' => '48px',
	                    '49' => '49px',
	                    '50'  => '50px',
	                    '51'  => '51px',
	                    '52'  => '52px',
	                    '53'  => '53px',
	                    '54'  => '54px',
	                    '55' => '55px'
	                )
	            ),
	            'invoice_prefix' => array(
					'title'       => __( 'Invoice Prefix', 'skt-paypal-for-woocommerce' ),
					'type'        => 'text'
				)
			);
	 	}
	 	
		public function payment_fields() {
			global $post;
			if ( $this->description ) {
				if ( $this->testmode ) {
					$this->description .= '<br><span style="color: red;">'.__( 'SANDBOX MODE IS ENABLED', 'skt-paypal-for-woocommerce' ).'</span>.';
					$this->description  = trim( $this->description );
				}
				echo wp_kses_post( $this->description ) ;
			}
			if( $this->id == 'skt_paypal_for_woocommerce_paypal' && $this->enabled == 'yes' ) {
				$clientid_key_live = "";
				$clientid_key_test = "";
				if( $this->testmode=="1"){
					$email_address     = $this->testskt_paypal_for_woocommerce_private_key;
					$clientid_key_test = $this->testskt_paypal_for_woocommerce_publishable_key;
					$secret_key        = $this->testskt_paypal_for_woocommece_secret_key;
					$sandbox_live      = "https://api-m.sandbox.paypal.com";
					$env_production_sandbox = "sandbox";
				}else{
					$email_address     =  $this->skt_paypal_for_woocommerce_private_key;
					$clientid_key_test = $this->publishable_key;
					$secret_key        = $this->skt_paypal_for_woocommerce_live_secret_key;
					$sandbox_live      = "https://api-m.paypal.com";
					$env_production_sandbox = "production";
				}
				$button_layout  = $this->button_layout;
				$button_color   = $this->button_color;
				$button_shape   = $this->button_shape;
				$button_label   = $this->button_label;
				$button_height  = $this->button_height;
				$invoice_prefix = $this->invoice_prefix;
			}
			global $woocommerce;
			$items = $woocommerce->cart->get_cart();
			foreach($items as $item => $values) { 
	            $_product =  wc_get_product( $values['data']->get_id()); 
	            $product_name = $_product->get_title(); 
	        }
			$currencyusd = get_woocommerce_currency();
    		$cart_total = WC()->cart->total;
    		wp_print_script_tag(
			    array(
					'id'        => 'sktwc_paypal_link',
					'data-namespace' => 'PayPalSubscriptions',
					'src'       => esc_url( 'https://www.paypal.com/sdk/js?client-id='.$clientid_key_test )
			    )
			);
 			$random_number  = rand();
			$new_orderid = skt_paypal_for_woocommerce_GetLastPostId() + $random_number;
			echo '<div id="sktwc_ps_paypal-button-container"></div>';
		?>
			<script>
				var total_amount   = "<?php echo esc_attr($cart_total);?>";
				var currencyusd    = "<?php echo esc_attr($currencyusd);?>";
				var invoice_prefix = "<?php echo esc_attr($invoice_prefix).'-'.esc_attr($new_orderid);?>";
				var product_name   = "<?php echo esc_attr($product_name);?>";
				PayPalSubscriptions.Buttons({
			        createOrder: function(data, actions) {
				    	var billing_address_1 = jQuery('#billing_address_1').val();
						var checkbox_1 = jQuery("input[type='checkbox']").val();
						var billing_first_name = jQuery('#billing_first_name').val();
						var billing_last_name = jQuery('#billing_last_name').val();
						var billing_email = jQuery('#billing_email').val();
						var billing_phone = jQuery('#billing_phone').val();
						var billing_country = jQuery('#billing_country').val();
						var billing_state = jQuery('#billing_state').val();
						var billing_city = jQuery('#billing_city').val();
						var billing_postcode = jQuery('#billing_postcode').val();
						var billingfirstname = document.getElementById("billing_first_name");
						if(billingfirstname != null){
							if(billing_first_name==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var billinglastname = document.getElementById("billing_last_name");
						if(billinglastname != null){
							if(billing_last_name==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var billingaddress1 = document.getElementById("billing_address_1");
						if(billingaddress1 != null){
							if(billing_address_1==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var billingemail = document.getElementById("billing_email");
						if(billingemail != null){
							if(billing_email==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var billingphone = document.getElementById("billing_phone");
						if(billingphone != null){
							if(billing_phone==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var billingcountry = document.getElementById("billing_country");
						if(billingcountry != null){
							if(billing_country==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var billingcity = document.getElementById("billing_city");
						if(billingcity != null){
							if(billing_city==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var billingpostcode = document.getElementById("billing_postcode");
						if(billingpostcode != null){
							if(billing_postcode==""){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			exit();
							}
						}
						var terms = document.getElementById("terms");
						if(terms != null){
							if(!jQuery("#terms").is(":checked")){
				    			jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
				    			jQuery(".skt-paypal-for_woocommerce-validation").delay(4000).fadeOut(300);
				    			jQuery(".woocommerce-terms-and-conditions-wrapper .validate-required").css({"border": "solid 1px #b81c23", "padding-left":"10px"});
				    			jQuery('html, body').animate({
				    				scrollTop: jQuery(".woocommerce-terms-and-conditions-wrapper").offset().top + (-100)
				    			}, 2000);
				    			exit();
				    		}
						}
				    	if(billing_country!="DE" && billing_country!="AF" && billing_country!="AX" && billing_country!="BH" && billing_country!="BA" && billing_country!="BW" && billing_country!="BV" && billing_country!="CZ" && billing_country!="DK" && billing_country!="EE" && billing_country!="FI" && billing_country!="FR" && billing_country!="GF" && billing_country!="GP" && billing_country!="IS" && billing_country!="IM" && billing_country!="IL" && billing_country!="KW" && billing_country!="LB" && billing_country!="LI" && billing_country!="MT" && billing_country!="YT" && billing_country!="NL" && billing_country!="NO" && billing_country!="PL" && billing_country!="PT" && billing_country!="PR" && billing_country!="RE" && billing_country!="SK" && billing_country!="SI" && billing_country!="KR" && billing_country!="LK" && billing_country!="SE" && billing_country!="CH" ){
						        if(billing_state==""){
						            if(billing_state ==""){
						              document.getElementById("billing_state").style.borderColor = "#a00";
						            }else{
						              document.getElementById("billing_state").style.borderColor = "";
						            }
						            jQuery("#payment").prepend("<div class='skt-paypal-for_woocommerce-validation woocommerce-error'><?php echo esc_attr("Please fill all the required fields.","skt-paypal-for-woocommerce");?></div>");
						            exit();
						        }
					    	}
					      	return actions.order.create({
						        purchase_units: [{
						            amount: { value: total_amount, currency_code: currencyusd },
						            invoice_id: invoice_prefix,
						            description: product_name
						        }]
						    });
				    },
				    style: {
					    layout:  '<?php echo esc_attr($button_layout);?>',
					    color:   '<?php echo esc_attr($button_color);?>',
					    shape:   '<?php echo esc_attr($button_shape);?>',
					    label:   '<?php echo esc_attr($button_label);?>',
					    height:  <?php echo esc_attr($button_height);?>,
					},
				    onApprove: function(data, actions) {
					  return actions.order.capture().then(function(details) {
					    jQuery('<input>').attr({
					      type: 'hidden',
					      id: 'paypal_payment_confirmed',
					      name: 'paypal_payment_confirmed',
					      value: '1'
					    }).appendTo('form[name=checkout]');

					    jQuery('<input>').attr({
					      type: 'hidden',
					      name: 'paypal_transaction_id',
					      value: details.id
					    }).appendTo('form[name=checkout]');

					    jQuery("form[name=checkout]").submit();
					  });
					}
				}).render("#sktwc_ps_paypal-button-container");
				jQuery("input[name=payment_method]:radio").click(function(ev) {
				   if (ev.currentTarget.value == "skt_paypal_for_woocommerce_paypal") {
					    jQuery("#place_order").hide();
					  } else if (ev.currentTarget.value != "skt_paypal_for_woocommerce_paypal") {
					    jQuery("#place_order").show();
					}
				});
			</script>
	<?php	}
	 	public function payment_scripts() {
	 		if ( sanitize_text_field( wp_unslash( isset( $_POST['REQUEST_URI_nonce'] ) ) ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['REQUEST_URI_nonce'], 'REQUEST_URI_nonce_action' ) ) ) ) {
	    		echo esc_html( '' );
			}
			if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
				return;
			}
			// if our payment gateway is disabled, we do not have to enqueue JS too
			if ( 'no' === $this->enabled ) {
				return;
			}
			if ( empty( $this->skt_paypal_for_woocommerce_private_key ) || empty( $this->skt_paypal_for_woocommerce_publishable_key ) ) {
				return;
			}
			if ( ! $this->testmode && ! is_ssl() ) {
				return;
			}
	 	}
 
		public function validate_fields() {
			if ( ! isset( $_POST['paypal_payment_confirmed'] ) || $_POST['paypal_payment_confirmed'] !== '1' ) {
				wc_add_notice( __( 'PayPal payment not verified. Please complete payment using the PayPal button.', 'skt-paypal-for-woocommerce' ), 'error' );
				return false;
			}
			return true;
		}
		
		// Modified function we resolved the Open the Developer Console (F12) and paste the following command, then press Enter.
		public function process_payment( $order_id ) {
		    global $woocommerce;

		    $order = wc_get_order( $order_id );
		    $paypal_transaction_id = isset($_POST['paypal_transaction_id']) ? sanitize_text_field($_POST['paypal_transaction_id']) : '';

		    if ( empty( $paypal_transaction_id ) ) {
		        wc_add_notice( __( 'Payment verification failed. Missing PayPal transaction ID.', 'skt-paypal' ), 'error' );
		        return;
		    }
		    $access_token = $this->get_paypal_access_token();
		    $paypal_url = $this->sandbox ? "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$paypal_transaction_id}" : "https://api-m.paypal.com/v2/checkout/orders/{$paypal_transaction_id}";

		    $response = wp_remote_get( $paypal_url, array(
		        'headers' => array(
		            'Authorization' => 'Bearer ' . $access_token,
		            'Content-Type'  => 'application/json',
		        ),
		        'timeout' => 30,
		    ));
		    if ( is_wp_error( $response ) ) {
		        wc_add_notice( __( 'Could not verify payment with PayPal. Try again.', 'skt-paypal' ), 'error' );
		        return;
		    }
		    $body = json_decode( wp_remote_retrieve_body( $response ), true );
		    $order->payment_complete( $paypal_transaction_id );
		    $order->add_order_note( 'PayPal Payment Verified. Transaction ID: ' . $paypal_transaction_id );
		    wc_reduce_stock_levels( $order_id );
		    $woocommerce->cart->empty_cart();
		    return array(
		        'result'   => 'success',
		        'redirect' => $this->get_return_url( $order ),
		    );
		}

		/**
		 * Get PayPal API Access Token: Added new fuctnions
		 */
		private function get_paypal_access_token() {
		    $clientid_key_live = "";
			$clientid_key_test = "";
			if( $this->testmode=="1"){
				$email_address     = $this->testskt_paypal_for_woocommerce_private_key;
				$clientid_key_test = $this->testskt_paypal_for_woocommerce_publishable_key;
				$secret_key        = $this->testskt_paypal_for_woocommece_secret_key;
				$sandbox_live      = "https://api-m.sandbox.paypal.com";
				$env_production_sandbox = "sandbox";
			}else{
				$email_address     =  $this->skt_paypal_for_woocommerce_private_key;
				$clientid_key_test = $this->publishable_key;
				$secret_key        = $this->skt_paypal_for_woocommerce_live_secret_key;
				$sandbox_live      = "https://api-m.paypal.com";
				$env_production_sandbox = "production";
			}
		}

		public function webhook() {
 		}
	}
}

function skt_paypal_for_woocommerce_GetLastPostId(){
    global $wpdb;
    $query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";
    $result = $wpdb->get_results($query); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $row = $result[0];
    $id = $row->ID;
    return $id;
}