<?php
/*
 * Plugin Name: SKT PayPal for WooCommerce
 * Plugin URI: https://www.sktthemes.org/shop/woocommerce-paypal-checkout-plugin/
 * Description: SKT PayPal is a WooCommerce PayPal checkout plugin developed by SKT Themes. It offers you the best checkout payment processing solution. However, It does accept PayPal and credit/debit cards. Plus by modifying your PayPal settings for country coverage and currency acceptance it enable global coverage settings.
 * Author: SKT Themes
 * Author URI: https://www.sktthemes.org/
 * Version: 1.5
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: skt-paypal-for-woocommerce
 */

if ( !defined('ABSPATH')) exit;
// Set Constants
define( 'SKT_PAYPAL_FOR_WOOCOMMERCE_PAYMENT_GATEWAY_DIR', dirname( __FILE__ ) );
define( 'SKT_PAYPAL_FOR_WOOCOMMERCE_PAYMENT_GATEWAY_URI', plugins_url( '', __FILE__ ) );

/*
* Install plugin
*/
function skt_paypal_for_woocommerce_activation_plugin(){
	flush_rewrite_rules();
	global $wpdb;
}
register_activation_hook( __FILE__, 'skt_paypal_for_woocommerce_activation_plugin' );

function skt_paypal_for_woocommerce_deactivation_plugin(){
	flush_rewrite_rules();
	global $wpdb;
}
register_activation_hook( __FILE__, 'skt_paypal_for_woocommerce_deactivation_plugin' );

// Show woocommerce not installed notice.
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
  if (is_admin()) {
  } 
} else {
	if ( ! function_exists ( 'skt_paypal_for_woocommerce_admin_alert_notice' ) ) {
		function skt_paypal_for_woocommerce_admin_alert_notice() {
	?>
		    <div class="error">
		      <p><?php esc_attr_e( 'SKT Paypal for WooCommerce plugin requires WooCommerce plugin installed & activated.', 'skt-paypal-for-woocommerce' ); ?></p>
		    </div>
		<?php
		}
	}
  add_action( 'admin_notices', 'skt_paypal_for_woocommerce_admin_alert_notice' );
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters('active_plugins', get_option( 'active_plugins' )))) {
	return;
}

add_action( 'wp_enqueue_scripts', 'skt_paypal_for_woocommerce_custom_style' );
function skt_paypal_for_woocommerce_custom_style() {
	wp_enqueue_style( 'skt-paypal-for-woocommerce-style-css', SKT_PAYPAL_FOR_WOOCOMMERCE_PAYMENT_GATEWAY_URI . '/css/style.css', 'style-css-stylesheet' );
}

add_action( 'admin_enqueue_scripts', 'skt_paypal_for_woocommerce_normal_admin_enqueue' );
function skt_paypal_for_woocommerce_normal_admin_enqueue() {
	load_plugin_textdomain( 'skt-paypal-for-woocommerce', false, basename(dirname(__FILE__)).'/languages' );
}

require( SKT_PAYPAL_FOR_WOOCOMMERCE_PAYMENT_GATEWAY_DIR . '/backend/paypal-checkout.php' );