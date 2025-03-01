<?php
/**
 * Plugin Name: YITH WooCommerce Category Accordion
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-category-accordion/
 * Description: With <code><strong>YITH WooCommerce Category Accordion</strong></code>you can add an accordion menu to your sidebars in a few clicks to view product or post categories! <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.6.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-category-accordion
 * Domain Path: /languages/
 * WC requires at least: 5.8
 * WC tested up to: 6.1
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Category Accordion
 * @version 1.6.0
 */

/*
Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
/**
 * Show error if WooCommerce isn't active
 *
 * @author YITH
 * @since 1.0.0
 */
function yith_ywcca_free_install_woocommerce_admin_notice() {   ?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Category Accordion  is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-category-accordion' ); ?></p>
	</div>
	<?php
}

/**
 * Show error if the premium version is active
 *
 * @author YITH
 * @since 1.0.0
 */
function yith_ywcca_install_free_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'You can\'t activate the free version of YITH WooCommerce Category Accordion while you are using the premium one.', 'yith-woocommerce-category-accordion' ); ?></p>
	</div>
	<?php
}


if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! defined( 'YWCCA_VERSION' ) ) {
	define( 'YWCCA_VERSION', '1.6.0' );
}

if ( ! defined( 'YWCCA_FREE_INIT' ) ) {
	define( 'YWCCA_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YWCCA_FILE' ) ) {
	define( 'YWCCA_FILE', __FILE__ );
}

if ( ! defined( 'YWCCA_DIR' ) ) {
	define( 'YWCCA_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWCCA_URL' ) ) {
	define( 'YWCCA_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWCCA_ASSETS_URL' ) ) {
	define( 'YWCCA_ASSETS_URL', YWCCA_URL . 'assets/' );
}

if ( ! defined( 'YWCCA_TEMPLATE_PATH' ) ) {
	define( 'YWCCA_TEMPLATE_PATH', YWCCA_DIR . 'templates/' );
}

if ( ! defined( 'YWCCA_INC' ) ) {
	define( 'YWCCA_INC', YWCCA_DIR . 'includes/' );
}

if ( ! defined( 'YWCCA_SLUG' ) ) {
	define( 'YWCCA_SLUG', 'yith-woocommerce-category-accordion' );
}


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCCA_DIR . 'plugin-fw/init.php' ) ) {
	require_once YWCCA_DIR . 'plugin-fw/init.php';
}

yit_maybe_plugin_fw_loader( YWCCA_DIR );


if ( ! function_exists( 'YITH_Category_Accordion_Init' ) ) {

	/**
	 * Unique access to instance of YITH_Category_Accordion class
	 *
	 * @author YITH
	 * @since 1.0.3
	 */
	function YITH_Category_Accordion_Init() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName

		load_plugin_textdomain( 'yith-woocommerce-category-accordion', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once YWCCA_INC . 'class.yith-category-accordion-widget.php';
		require_once YWCCA_INC . 'class.yith-woocommerce-category-accordion.php';

		global $YIT_Category_Accordion;// phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$YIT_Category_Accordion = YITH_WC_Category_Accordion::get_instance(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
	}
}

add_action( 'yith_wc_category_accordion_init', 'YITH_Category_Accordion_Init' );

if ( ! function_exists( 'yith_category_accordion_install' ) ) {
	/**
	 * Install category accordion
	 *
	 * @author YITH
	 * @since 1.0.3
	 */
	function yith_category_accordion_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywcca_free_install_woocommerce_admin_notice' );
		} elseif ( defined( 'YWCCA_PREMIUM' ) ) {
			add_action( 'admin_notices', 'yith_ywcca_install_free_admin_notice' );
			deactivate_plugins( plugin_basename( __FILE__ ) );
		} else {
			do_action( 'yith_wc_category_accordion_init' );
		}

	}
}

add_action( 'plugins_loaded', 'yith_category_accordion_install', 11 );
