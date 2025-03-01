<?php  // phpcs:ignore WordPress.Files.FileName
/**
 * The main plugin class
 *
 * @package YITH WooCommerce Category Accordion\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Category_Accordion' ) ) {
	/**
	 * The main class
	 *
	 * @author YITH
	 * @since 1.0.0
	 */
	class YITH_WC_Category_Accordion {
		/**
		 * The unique instance of the class
		 *
		 * @var YITH_WC_Category_Accordion
		 */
		protected static $_instance = null;
		/**
		 * The instance of YIT Plugin panel
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $_panel = null;
		/**
		 * The plugin panel name
		 *
		 * @var string
		 */
		protected $_panel_page = 'yith_wc_category_accordion';
		/**
		 * The premium template name
		 *
		 * @var string
		 */
		protected $_premium = 'premium.php';
		/**
		 * Will contain the suffix .min for load the minify scripts
		 *
		 * @var string
		 */
		public $suffix = '';

		/**
		 * The construct
		 *
		 * @author YITH
		 * @since 1.0.0
		 */
		public function __construct() {
			// Load Plugin Framework.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			// Add action links.
			add_filter(
				'plugin_action_links_' . plugin_basename( YWCCA_DIR . '/' . basename( YWCCA_FILE ) ),
				array(
					$this,
					'action_links',
				)
			);
			// Add row meta.
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			// Add menu field under YITH_PLUGIN.
			add_action( 'yith_wc_category_accordion_premium', array( $this, 'premium_tab' ) );
			add_action( 'admin_menu', array( $this, 'add_category_accordion_menu' ), 5 );

			// register widget.
			add_action( 'widgets_init', array( $this, 'register_accordion_widget' ) );
			// enqueue style.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_script' ) );
			$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		}

		/**
		 * Return the single instance of the class
		 *
		 * @author YITH
		 * @since 1.0.0
		 * @return YITH_WC_Category_Accordion
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Load the plugin Framework
		 *
		 * @author YITH
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Add the action links to plugin admin page
		 *
		 * @param array $links | links plugin array.
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return array
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, defined( 'YWCCA_PREMIUM' ) );

			return $links;
		}

		/**
		 * Plugin_row_meta.
		 *
		 * Add the action links to plugin admin page.
		 *
		 * @param array  $new_row_meta_args The new plugin meta.
		 * @param array  $plugin_meta The plugin meta.
		 * @param string $plugin_file The filename of plugin.
		 * @param array  $plugin_data The plugin data.
		 * @param string $status The plugin status.
		 * @param string $init_file The filename of plugin.
		 * @return   array
		 * @since    1.0
		 * @author  YITH
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCCA_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug'] = YWCCA_SLUG;
			}

			return $new_row_meta_args;
		}


		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  void
		 */
		public function premium_tab() {
			$premium_tab_template = YWCCA_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_category_accordion_menu() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = apply_filters(
				'yith_category_accordion_admin_tabs',
				array(
					'premium' => __( 'Premium Version', 'yith-woocommerce-category-accordion' ),
				)
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Category Accordion',
				'menu_title'       => 'Category Accordion',
				'capability'       => 'manage_options',
				'plugin_slug'      => YWCCA_SLUG,
				'parent'           => '',
				'class'            => yith_set_wrapper_class(),
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCCA_DIR . '/plugin-options',
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Register the plugin widget
		 *
		 * @author YITH
		 * @since 1.0.0
		 */
		public function register_accordion_widget() {
			register_widget( 'YITH_Category_Accordion_Widget' );
		}

		/**
		 * Include style and script
		 *
		 * @author YITH
		 * @since 1.0.0
		 */
		public function enqueue_style_script() {

			wp_register_style( 'ywcca_accordion_style', YWCCA_ASSETS_URL . 'css/ywcca_style.css', array(), YWCCA_VERSION );
			wp_register_script( 'ywcca_accordion', YWCCA_ASSETS_URL . 'js/ywcca_accordion' . $this->suffix . '.js', array( 'jquery' ), YWCCA_VERSION, true );

			$ywcca_params = apply_filters( 'ywcca_script_params', array() );

			wp_localize_script( 'ywcca_accordion', 'ywcca_params', $ywcca_params );

		}

	}
}
