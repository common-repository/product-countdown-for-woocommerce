<?php
/*
Plugin Name: Product Time Countdown for WooCommerce
Plugin URI: https://wordpress.org/plugins/product-countdown-for-woocommerce/
Description: Live WooCommerce product time countdown.
Version: 1.6.0
Author: ProWCPlugins
Author URI: https://prowcplugins.com
Text Domain: product-countdown-for-woocommerce
Domain Path: /langs
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC tested up to: 9.0.2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('PCFWC_PC_FILE', __FILE__);
define('PCFWC_TEXTDOMAIN', 'product-countdown-for-woocommerce');
define('PCFWC_PC_DIR', plugin_dir_path(PCFWC_PC_FILE));
define('PCFWC_PC_URL', plugins_url('/', PCFWC_PC_FILE));

if ( ! class_exists( 'ProWC_Product_Countdown' ) ) :

/**
 * Main ProWC_Product_Countdown Class
 *
 * @class   ProWC_Product_Countdown
 * @version 1.4.2
 * @since   1.0.0
 */
final class ProWC_Product_Countdown {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.6.0';

	/**
	 * @var   ProWC_Product_Countdown The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main ProWC_Product_Countdown Instance
	 *
	 * Ensures only one instance of ProWC_Product_Countdown is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  ProWC_Product_Countdown - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * ProWC_Product_Countdown Constructor.
	 *
	 * @version 1.4.2
	 * @since   1.0.0
	 * @access  public
	 */
	function __construct() {

		// Set up localisation
		load_plugin_textdomain( 'product-countdown-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=prowc_product_countdown' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'product-countdown-for-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a href="https://prowcplugins.com/downloads/product-time-countdown-for-woocommerce/">' .
				__( 'Unlock All', 'product-countdown-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function includes() {
		// Core
		require_once( 'includes/class-prowc-product-countdown-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.4.2
	 * @since   1.4.2
	 */
	public $settings;
	public function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		require_once( 'includes/settings/class-prowc-product-countdown-settings-section.php' );
		$this->settings = array();
		$this->settings['general'] = require_once( 'includes/settings/class-prowc-product-countdown-settings-general.php' );
		// Version updated
		if ( get_option( 'prowc_product_countdown_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
		add_action('admin_enqueue_scripts', array($this, 'prowc_product_countdown_admin_style'));
		add_action('admin_init',  array($this,'prowc_product_countdown_notice_update'));
		add_action('admin_init',  array($this,'prowc_product_countdown_plugin_notice_remindlater'));
		add_action('admin_init',  array($this,'prowc_product_countdown_plugin_notice_review'));
		add_action('admin_notices', array($this,'prowc_product_countdown_admin_upgrade_notice'));
		add_action('admin_notices', array($this,'prowc_product_countdown_admin_review_notice'));
		add_action('plugins_loaded', array($this,'prowc_product_countdown_check_version'));
		register_activation_hook( __FILE__, array($this,'prowc_pcfwc_check_activation_hook'));

		// Admin notice
		if (!class_exists('WooCommerce')) {
			add_action('admin_notices', array( $this, 'fail_load') );
			return;
		}
	}

	/**
	 * Database options upgrade.
	 *
	 * @version 1.5.1
	 */
	function prowc_product_countdown_check_version() {
		if ( version_compare( $this->version, '1.5.0', '<' )) {
			global $wpdb;
			$table_options = $wpdb->prefix . 'options';
			$old_keys = wp_cache_get( 'prowc_old_keys' );
			
			if ( false === $old_keys ) {
				$old_keys = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `%s` WHERE `option_name` LIKE %s", $table_options, '%alg_wc_%' ) );
				wp_cache_set( 'prowc_old_keys', $old_keys );
			}
	
			if ( is_array( $old_keys ) || is_object( $old_keys ) ) {
				foreach ( $old_keys as $val ) {
					$option_name = $val->option_name;
					$new_key = str_replace( 'alg_wc_', 'prowc_', $option_name );
					$old_option_value = get_option( $option_name );
					update_option( $new_key, $old_option_value );
					delete_option( $option_name );
				}
			}
		}
	}

	/**
	 * version_updated.
	 *
	 * @version 1.4.2
	 * @since   1.4.0
	 */
	function version_updated() {
		update_option( 'prowc_product_countdown_version', $this->version );
	}

	function prowc_product_countdown_notice_update() {
		$remdate = gmdate('Y-m-d', strtotime('+ 7 days'));
		$rDater = get_option('prowc_product_countdown_plugin_notice_nopemaybelater');
		if(!get_option('prowc_product_countdown_plugin_notice_remindlater')){
			update_option('prowc_product_countdown_plugin_notice_remindlater',$remdate);
			update_option('prowc_product_countdown_plugin_reviewtrack', 0);
		}
		
		if($rDater && gmdate('Y-m-d') >= $rDater) {
			update_option('prowc_product_countdown_plugin_notice_remindlater',$remdate);
		}
	}

	/**
	 * Add Product Time Countdown settings tab to WooCommerce settings.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'includes/settings/class-prowc-settings-product-countdown.php' );
		return $settings;
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Admin Notice for WooCommerce Install & Active.
	 *
	 * @version 1.4.7
	 * @since   1.4.7
	 * @return  string
	 */
	function prowc_PC_installed() {

		$file_path = 'woocommerce/woocommerce.php';
		$installed_plugins = get_plugins();

		return isset($installed_plugins[$file_path]);
	}

	/**
	 * Plugin active date.
	 *
	 * @version 1.5.2
	 * @since   1.5.2
	 */
	function prowc_pcfwc_check_activation_hook() {
		$get_activation_time = gmdate('Y-m-d', strtotime('+ 3 days'));
		add_option('prowc_pcfwc_activation_time', $get_activation_time ); 
	}

	/**
	 * Admin Notice for WooCommerce Install & Active.
	 *
	 * @version 1.4.7
	 * @since   1.4.7
	 * @return  string
	 */
	function fail_load() {
		if(function_exists('WC')){
			return;
		}
		$screen = get_current_screen();
		if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
			return;
		}

		$plugin = 'woocommerce/woocommerce.php';
		if ($this->prowc_PC_installed()) {
			if (!current_user_can('activate_plugins')) {
				return;
			}
			$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);

			$message = '<p><strong>' . esc_html__('Product Time Countdown for WooCommerce', 'product-countdown-for-woocommerce') . '</strong>' . esc_html__(' plugin is not working because you need to activate the Woocommerce plugin.', 'product-countdown-for-woocommerce') . '</p>';
			$message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $activation_url, __('Activate Woocommerce Now', 'product-countdown-for-woocommerce')) . '</p>';
		} else {
			if (!current_user_can('install_plugins')) {
				return;
			}

			$install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=woocommerce'), 'install-plugin_woocommerce');

			$message = '<p><strong>' . esc_html__('Product Time Countdown for WooCommerce', 'product-countdown-for-woocommerce') . '</strong>' . esc_html__(' plugin is not working because you need to install the WooCoomerce plugin', 'product-countdown-for-woocommerce') . '</p>';
			$message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $install_url, __('Install WooCommerce Now', 'product-countdown-for-woocommerce')) . '</p>';
		}

		echo '<div class="error"><p>' . wp_kses_post($message) . '</p></div>';
	}

	/**
	 * enqueue admin script.
	 *
	 * @version 1.5.1
	 */
	function prowc_product_countdown_admin_style(){
		wp_enqueue_style('prowc-wc-pc-style', PCFWC_PC_URL . '/includes/css/admin-style.css', array(), '1.0' );
		wp_enqueue_script('prowc-wc-product-countdown-script', PCFWC_PC_URL . '/includes/js/admin-script.js', array ( 'jquery' ), 1.1, true);

		//admin rating popup js
		wp_enqueue_script('prowc-pcfwc-sweetalert-min', PCFWC_PC_URL . '/includes/js/sweetalert.min.js', array ( 'jquery' ), 1.1, true);
	}

	/* Admin Notice for upgrade plan Start */
	function prowc_product_countdown_admin_upgrade_notice() {
		$rDate = get_option('prowc_product_countdown_plugin_notice_remindlater');
		if (gmdate('Y-m-d') >= $rDate && !get_option('prowc_product_countdown_plugin_notice_dismissed')) {
			?>
			<div class="notice is-dismissible prowc_product_countdown_prowc_notice">
				<div class="prowc_product_countdown_wrap">
					<div class="prowc_product_countdown_gravatar">
						<img alt="" src="<?php echo esc_url(PCFWC_PC_URL . '/includes/img/prowc_logo.png'); ?>">
					</div>
					<div class="prowc_product_countdown_authorname">
						<div class="notice_texts">
							<a href="<?php echo esc_url('https://prowcplugins.com/downloads/product-time-countdown-for-woocommerce/?utm_source=product-time-countdown-for-woocommerce&utm_medium=referral&utm_campaign=settings'); ?>" target="_blank"><?php esc_html_e('Upgrade Product Time Countdown for WooCommerce', 'product-countdown-for-woocommerce'); ?> </a> <?php esc_html_e('to get additional features, security, and support. ', 'product-countdown-for-woocommerce'); ?> <strong><?php esc_html_e('Get 20% OFF', 'product-countdown-for-woocommerce'); ?></strong><?php esc_html_e(' your upgrade, use coupon code', 'product-countdown-for-woocommerce'); ?> <strong><?php esc_html_e('WP20', 'product-countdown-for-woocommerce'); ?></strong>
						</div>
						<div class="prowc_product_countdown_desc">
							<div class="notice_button">
								<?php wp_nonce_field( 'prowc_remind_later_nonce', 'nonce' ); ?>
								<a class="prowc_product_countdown_button button-primary" href="<?php echo esc_url('https://prowcplugins.com/downloads/product-time-countdown-for-woocommerce/?utm_source=product-time-countdown-for-woocommerce&utm_medium=referral&utm_campaign=settings'); ?>" target="_blank"><?php echo esc_html__('Buy Now', 'product-countdown-for-woocommerce'); ?></a>
								<a href="?prowc-pc-plugin-remindlater"><?php echo esc_html__('Remind me later', 'product-countdown-for-woocommerce'); ?></a>
								<a href="?prowc-pc-plugin-dismissed"><?php echo esc_html__('Dismiss Notice', 'product-countdown-for-woocommerce'); ?></a>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"></span>
				</button>
			</div>
	<?php }
	}
	function prowc_product_countdown_plugin_notice_remindlater() {
		$curDate = gmdate('Y-m-d', strtotime(' + 7 days'));
		$rlDate = gmdate('Y-m-d', strtotime(' + 15 days'));
		if ( isset( $_GET['prowc-pc-plugin-remindlater'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'prowc_remind_later_nonce' ) ) {
			update_option('prowc_product_countdown_plugin_notice_remindlater', $curDate);
			update_option('prowc_product_countdown_plugin_reviewtrack', 1);
			update_option('prowc_product_countdown_plugin_notice_nopemaybelater', $rlDate);
		}
		if (isset($_GET['prowc-pc-plugin-dismissed'])) {
			update_option('prowc_product_countdown_plugin_reviewtrack', 1);
			update_option('prowc_product_countdown_plugin_notice_nopemaybelater', $rlDate);
			update_option('prowc_product_countdown_plugin_notice_dismissed', 'true');
		}
		if(isset($_GET['prowc-wc-pcfwc-plugin-remindlater-rating'])){
			update_option('prowc_pcfwc_notice_remindlater_rating', $curDate);
		}
		if (isset($_GET['prowc-wc-pcfwc-plugin-dismissed-rating'])) {
			update_option('prowc_pcfwc_notice_dismissed_rating', 'true');
		}
	}
	/* Admin Notice for upgrade plan End */

	/* Admin Notice for Plugin Review Start */
	function prowc_product_countdown_admin_review_notice() {

		$plugin_data = get_plugin_data( __FILE__ );	
		$plugin_name = $plugin_data['Name'];
		$rating_rDate = get_option('prowc_pcfwc_notice_remindlater_rating');
		$activationDate = get_option('prowc_pcfwc_activation_time');

		$rDater = get_option('prowc_product_countdown_plugin_notice_nopemaybelater');
		$algtrack = get_option('prowc_product_countdown_plugin_reviewtrack');
		
		if (gmdate('Y-m-d') >= $activationDate && gmdate('Y-m-d') >= $rating_rDate && !get_option('prowc_pcfwc_notice_dismissed_rating')) {
			?>
				<div class="notice notice-info  is-dismissible">
					<p><?php  // translators: Placeholder %s represents the plugin name.
					printf( esc_html__( 'How are you liking the %s?', 'product-countdown-for-woocommerce' ), esc_html( $plugin_name ) ); ?></p>
					<div class="pcfwc_starts_main_div">
						<div class="stars pcfwc-star">
							<input type="radio" name="star" class="star-1 pcfwc" id="pcfwc-star-1" value="1" />
							<label class="star-1" for="pcfwc-star-1">1</label>
							<input type="radio" name="star" class="star-2 pcfwc" id="pcfwc-star-2" value="2" />
							<label class="star-2" for="pcfwc-star-2">2</label>
							<input type="radio" name="star" class="star-3 pcfwc" id="pcfwc-star-3" value="3" />
							<label class="star-3" for="pcfwc-star-3">3</label>
							<input type="radio" name="star" class="star-4 pcfwc" id="pcfwc-star-4" value="4" />
							<label class="star-4" for="pcfwc-star-4">4</label>
							<input type="radio" name="star" class="star-5 pcfwc" id="pcfwc-star-5" value="5" />
							<label class="star-5" for="pcfwc-star-5">5</label>
							<span></span>
						</div>
						<div class="notice_button">
							<a href="?prowc-wc-pcfwc-plugin-remindlater-rating" class="button-secondary" ><?php esc_html__('Remind me later', 'product-countdown-for-woocommerce'); ?></a>
							<a href="?prowc-wc-pcfwc-plugin-dismissed-rating" class="button-secondary" ><?php esc_html__('Dismiss Notice', 'product-countdown-for-woocommerce'); ?></a>
						</div>
					</div>
				</div>
			<?php
		}
	
		if ($rDater != "")
			if (gmdate('Y-m-d') >= $rDater && $algtrack && !get_option('prowc_product_countdown_plugin_notice_alreadydid')) {
			?>
			<div class="notice is-dismissible prowc_product_countdown_prowc_notice">
				<div class="prowc_product_countdown_wrap">
					<div class="prowc_product_countdown_gravatar">
						<img alt="" src="<?php echo esc_url(PCFWC_PC_URL . '/includes/img/prowc_logo.png'); ?>">
					</div>
					<div class="prowc_product_countdown_authorname">
						<div class="notice_texts">
							<strong><?php esc_html_e('Are you enjoying Product Time Countdown for WooCommerce?', 'product-countdown-for-woocommerce'); ?></strong>
						</div>
						<div class="prowc_product_countdown_desc">
							<div class="notice_button">
								<button class="prowc_product_countdown_button button-primary prowc_product_countdown_yes"><?php echo esc_html__('Yes!', 'product-countdown-for-woocommerce'); ?></button>
								<a class="prowc_product_countdown_button button action" href="?prowc-wc-pc-plugin-alreadydid"><?php echo esc_html__('Not Really!', 'product-countdown-for-woocommerce'); ?></a>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>

				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"></span>
				</button>
				<div class="prowc_product_countdown_prowc_notice_review_yes">
					<div class="notice_texts">
						<?php echo esc_html__('That\'s awesome! Could you please do me a BIG favor and give it 5-star rating on WordPress to help us spread the word and boost our motivation?' , 'product-countdown-for-woocommerce'); ?>
					</div>
					<div class="prowc_product_countdown_desc">
						<div class="notice_button">
							<?php wp_nonce_field( 'prowc_remind_nopemay_nonce', 'nonce' ); ?>
							<a class="prowc_product_countdown_button button-primary" href="<?php echo esc_url('https://wordpress.org/support/plugin/product-countdown-for-woocommerce/reviews/?filter=5#new-post'); ?>" target="_blank"><?php echo esc_html__('Okay You Deserve It', 'product-countdown-for-woocommerce'); ?></a>
							<a class="prowc_product_countdown_button button action" href="?prowc-wc-pc-plugin-nopemaybelater"><?php echo esc_html__('Nope Maybe later', 'product-countdown-for-woocommerce'); ?></a>
							<a class="prowc_product_countdown_button button action" href="?prowc-wc-pc-plugin-alreadydid"><?php echo esc_html__('I Already Did', 'product-countdown-for-woocommerce'); ?></a>
						</div>
					</div>
				</div>
			</div>
			
		<?php } ?>
	<?php }

	function prowc_product_countdown_plugin_notice_review() {
		$curDate = gmdate('Y-m-d', strtotime(' + 7 days'));
		if ( isset( $_GET['prowc-wc-pc-plugin-nopemaybelater'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'prowc_remind_nopemay_nonce' ) ) {
			update_option('prowc_product_countdown_plugin_notice_nopemaybelater', $curDate);
		}
		if (isset($_GET['prowc-wc-pc-plugin-alreadydid'])) {
			update_option('prowc_product_countdown_plugin_notice_alreadydid', 'true');
		}
	}

	}

endif;

if (!function_exists('prowc_time_countdown_free_activation')) {

	/**
	 * Add action on plugin activation
	 * 
	 * @version 1.5.4
	 * @since   1.5.4
	 */
	function prowc_time_countdown_free_activation() {

		// Deactivate Product Time Countdown pro for WooCommerce
		deactivate_plugins('product-time-countdown-pro-for-woocommerce/product-time-countdown-pro-for-woocommerce.php'); 
		
	}
}
register_activation_hook(__FILE__, 'prowc_time_countdown_free_activation');

if ( ! function_exists( 'prowc_product_countdown' ) ) {
	/**
	 * Returns the main instance of ProWC_Product_Countdown to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  ProWC_Product_Countdown
	 */
	function prowc_product_countdown() {
		return ProWC_Product_Countdown::instance();
	}
}

prowc_product_countdown();

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage (HPOS).
 */
add_action('before_woocommerce_init', 'prowc_product_countdown_hpos_compatibility');

function prowc_product_countdown_hpos_compatibility() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true // true (compatible, default) or false (not compatible)
        );
    }
}
