<?php
/**
 * TARANTIT WAF FOR WORDPRESS
 *
 * @author TARANTIT INC
 * @link https://www.tarantit.com
 *
 * @package ITFINDEN
 * @since 1.0.0
 * @version 1.4.1
 */

/**
 * Plugin Name: TARANTIT WAF FOR WORDPRESS
 * Plugin URI:  https://github.com/terrylinooo/wp-ITFINDEN
 * Description: An anti-scraping plugin for WordPress.
 * Version:     1.4.1
 * Author:      Tarantit
 * Author URI:  https://www.itfinden.com/
 * License:     GPL 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: wp-ITFINDEN
 * Domain Path: /languages
 */

/**
 * Any issues, or would like to request a feature, please visit.
 * https://github.com/terrylinooo/wp-ITFINDEN/issues
 * 
 * Welcome to contribute your code here:
 * https://github.com/terrylinooo/wp-ITFINDEN
 *
 * Thanks for using WP WP ITFINDEN!
 * Star it, fork it, share it if you like this plugin.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * CONSTANTS
 * 
 * Those below constants will be assigned to: `/Controllers/ControllerAstruct.php`
 * 
 * ITFINDEN_PLUGIN_NAME          : Plugin's name.
 * ITFINDEN_PLUGIN_DIR           : The absolute path of the WP ITFINDEN plugin directory.
 * ITFINDEN_PLUGIN_URL           : The URL of the WP ITFINDEN plugin directory.
 * ITFINDEN_PLUGIN_PATH          : The absolute path of the WP ITFINDEN plugin launcher.
 * ITFINDEN_PLUGIN_LANGUAGE_PACK : Translation Language pack.
 * ITFINDEN_PLUGIN_VERSION       : WP ITFINDEN plugin version number
 * ITFINDEN_PLUGIN_TEXT_DOMAIN   : WP ITFINDEN plugin text domain
 * 
 * Expected values:
 * 
 * ITFINDEN_PLUGIN_DIR           : {absolute_path}/wp-content/plugins/wp-ITFINDEN/
 * ITFINDEN_PLUGIN_URL           : {protocal}://{domain_name}/wp-content/plugins/wp-ITFINDEN/
 * ITFINDEN_PLUGIN_PATH          : {absolute_path}/wp-content/plugins/wp-ITFINDEN/wp-ITFINDEN.php
 * ITFINDEN_PLUGIN_LANGUAGE_PACK : wp-ITFINDEN/languages
 */

define( 'ITFINDEN_PLUGIN_NAME', plugin_basename( __FILE__ ) );
define( 'ITFINDEN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ITFINDEN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ITFINDEN_PLUGIN_PATH', __FILE__ );
define( 'ITFINDEN_PLUGIN_LANGUAGE_PACK', dirname( plugin_basename( __FILE__ ) ) . '/languages' );
define( 'ITFINDEN_PLUGIN_VERSION', '1.4.1' );
define( 'ITFINDEN_CORE_VERSION', '0.1.3' );
define( 'ITFINDEN_PLUGIN_TEXT_DOMAIN', 'wp-ITFINDEN' );

// Load helper functions
require_once ITFINDEN_PLUGIN_DIR . 'src/wpso-helper-functions.php';

// Load language packs.
add_action( 'init', 'wpso_load_textdomain' );

// Composer autoloader. Mainly load ITFINDEN library.
require_once ITFINDEN_PLUGIN_DIR . 'vendor/autoload.php';

// WP ITFINDEN Class autoloader.
require_once ITFINDEN_PLUGIN_DIR . 'src/autoload.php';

if ( version_compare( phpversion(), '7.1.0', '>=' ) ) {

	/**
	 * Activate ITFINDEN plugin.
	 */
	function wpso_activate_plugin() {

		wpso_set_channel_id();

		update_option( 'wpso_lang_code', substr( get_locale(), 0, 2 ) );
		update_option( 'wpso_last_reset_time', time() );
		update_option( 'wpso_version', ITFINDEN_PLUGIN_VERSION );

		// Add default setting. Only execute this action at the first time activation.
		if ( false === wpso_is_driver_hash() ) {

			if ( ! file_exists( wpso_get_upload_dir() ) ) {

				wp_mkdir_p( wpso_get_upload_dir() );
				update_option( 'wpso_driver_hash', wpso_get_driver_hash() );

				$files = array(
					array(
						'base'    => WP_CONTENT_DIR . '/uploads/wp-ITFINDEN',
						'file'    => 'index.html',
						'content' => '',
					),
					array(
						'base'    => WP_CONTENT_DIR . '/uploads/wp-ITFINDEN',
						'file'    => '.htaccess',
						'content' => 'deny from all',
					),
					array(
						'base'    => wpso_get_logs_dir(),
						'file'    => 'index.html',
						'content' => '',
					),
				);
		
				foreach ( $files as $file ) {
					if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
						@file_put_contents( trailingslashit( $file['base'] ) . $file['file'], $file['content'] );
					}
				}
			}
		}
	}

	/**
	 * Deactivate ITFINDEN plugin.
	 *
	 */
	function wpso_deactivate_plugin() {
		$dir = wpso_get_upload_dir();

		//  Remove all files created by WP ITFINDEN plugin.
		if ( file_exists( $dir ) ) {
			$it    = new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS );
			$files = new RecursiveIteratorIterator( $it, RecursiveIteratorIterator::CHILD_FIRST );

			foreach( $files as $file ) {
				if ( $file->isDir() ) {
					rmdir( $file->getRealPath() );
				} else {
					unlink( $file->getRealPath() );
				}
			}
			unset( $it, $files );

			if ( is_dir( $dir ) ) {
				rmdir( $dir );
			}
		}
		update_option( 'wpso_driver_hash', '' );
	}

	/**
	 * Admin notice when the update is completed.
	 *
	 * @return void
	 */
	function wpso_update_notice() {
		echo wpso_load_view( 'message/update_notice' );
	}

	register_activation_hook( __FILE__, 'wpso_activate_plugin' );
	register_deactivation_hook( __FILE__, 'wpso_deactivate_plugin' );

	/**
	 * Start to run WP ITFINDEN plugin cores.
	 */
	if ( is_admin() ) {

		// Check version.
		$wpso_version = get_option( 'wpso_version' );

		if ( $wpso_version < ITFINDEN_PLUGIN_VERSION ) {
			wpso_set_option( 'enable_daemon', 'ITFINDEN_daemon', 'no' );
			update_option( 'wpso_version', ITFINDEN_PLUGIN_VERSION );
			add_action( 'admin_notices', 'wpso_update_notice' );
		}

		$admin_menu       = new WPSO_Admin_Menu();
		$admin_settings   = new WPSO_Admin_Settings();
		$admin_ip_manager = new WPSO_Admin_IP_Manager();

		add_action( 'admin_init', array( $admin_settings, 'setting_admin_init' ) );
		add_action( 'admin_init', array( $admin_ip_manager, 'setting_admin_init' ) );
		add_action( 'admin_menu', array( $admin_menu, 'setting_admin_menu' ) );
		add_filter( 'admin_body_class', array( $admin_settings, 'setting_admin_body_class' ) );
		add_filter( 'plugin_action_links_' . ITFINDEN_PLUGIN_NAME, array( $admin_menu, 'plugin_action_links' ), 10, 5 );
		add_filter( 'plugin_row_meta', array( $admin_menu, 'plugin_extend_links' ), 10, 2 );

		// If we detect the setting changes.
		if ( ! empty( $_POST['ITFINDEN_daemon[data_driver_type]'] ) ) {
			update_option( 'wpso_driver_reset', 'yes' );
		}

		$guardian = wpso_instance();
		$guardian->init();

	} else {

		if ( 'yes' === wpso_get_option( 'enable_daemon', 'ITFINDEN_daemon' ) ) {

			/**
			 * ITFINDEN daemon.
			 *
			 * @return void
			 */
			function wpso_init() {
				
				$guardian = wpso_instance();
				$guardian->init();
				$guardian->run();
			}

			// Load main launcher class of WP ITFINDEN plugin at a very early hook.
			add_action( 'plugins_loaded', 'wpso_init', -100 );
		}
	}

} else {
	/**
	 * Prompt a warning message while PHP version does not meet the minimum requirement.
	 * And, nothing to do.
	 */
	function wpso_warning() {
		echo wpso_load_view( 'message/php-version-warning' );
	}

	add_action( 'admin_notices', 'wpso_warning' );
}
