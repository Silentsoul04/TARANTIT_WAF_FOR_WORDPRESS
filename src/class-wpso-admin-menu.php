<?php

/**
 * WP Shieldon Settings Admin.
 *
 * @author Terry Lin
 * @package Shieldon
 * @since 1.0.0
 * @version 1.1.0
 * @license GPLv3
 *
 */

class WPSO_Admin_Menu {

	/**
	 * Constructer.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
	}

	/**
	 * Load specfic CSS file for the Shieldon setting page.
	 */
	public function admin_enqueue_styles( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'shieldon' ) ) {
			return;
		}
		wp_enqueue_style( 'custom_wp_admin_css', SHIELDON_PLUGIN_URL . 'src/assets/css/admin-style.css', array(), SHIELDON_PLUGIN_VERSION, 'all' );
		wp_enqueue_style (  'wp-jquery-ui-dialog' );
	}

	/**
	 * Register JS files.
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'shieldon' ) ) {
			return;
		}
		wp_enqueue_script( 'fontawesome-5-js', SHIELDON_PLUGIN_URL . 'src/assets/js/fontawesome-all.min.js', array( 'jquery' ), SHIELDON_PLUGIN_VERSION, true );
		wp_enqueue_script( 'jquery-ui-dialog' );
	}

	/**
	 * Register the plugin page.
	 */
	public function setting_admin_menu() {
		global $admin_settings, $admin_ip_manager;

		$separate = '<div style="margin: 0px -10px 10px -10px; background-color: #555566; height: 1px; overflow: hidden;"></div>';

		add_menu_page(
			__( 'WP Shieldon', 'wp-shieldon' ),
			__( 'WP Shieldon', 'wp-shieldon' ),
			'manage_options',
			'shieldon-settings',
			'__return_false',
			'dashicons-shield'
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Settings', 'wp-shieldon' ),
			__( 'Settings', 'wp-shieldon' ),
			'manage_options',
			'shieldon-settings',
			array( $admin_settings, 'setting_plugin_page' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Action Logs', 'wp-shieldon' ),
			__( 'Action Logs', 'wp-shieldon' ),
			'manage_options',
			'shieldon-action-logs',
			array( $this, 'action_logs' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Overview', 'wp-shieldon' ),
			__( 'Overview', 'wp-shieldon' ),
			'manage_options',
			'shieldon-overview',
			array( $this, 'overview' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Rule Table', 'wp-shieldon' ),
			$separate . __( 'Rule Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-rule-table',
			array( $this, 'rule_table' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Filter Log Table', 'wp-shieldon' ),
			__( 'Filter Log Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-filter-log-table',
			array( $this, 'filter_log_table' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Session Table', 'wp-shieldon' ),
			__( 'Session Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-session-table',
			array( $this, 'session_table' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'IP Manager', 'wp-shieldon' ),
			$separate . __( 'IP Manager', 'wp-shieldon' ),
			'manage_options',
			'shieldon-ip-manager',
			array( $admin_ip_manager, 'setting_plugin_page' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'XSS Protection', 'wp-shieldon' ),
			__( 'XSS Protection', 'wp-shieldon' ),
			'manage_options',
			'shieldon-xss-protection',
			array( $this, 'xss_protection' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Authentication', 'wp-shieldon' ),
			__( 'Authentication', 'wp-shieldon' ),
			'manage_options',
			'shieldon-authentication',
			array( $this, 'authentication' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'About', 'wp-shieldon' ),
			$separate . __( 'About', 'wp-shieldon' ),
			'manage_options',
			'shieldon-about',
			array( $this, 'about' )
		);
	}

	/**
	 * Filters the action links displayed for each plugin in the Network Admin Plugins list table.
	 *
	 * @param  array  $links Original links.
	 * @param  string $file  File position.
	 *
	 * @return array Combined links.
	 */
	public function plugin_action_links( $links, $file ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $links;
		}

		if ( $file == SHIELDON_PLUGIN_NAME ) {
			$links[] = '<a href="' . admin_url( "admin.php?page=shieldon-settings" ) . '">' . __( 'Settings', 'wp-shieldon' ) . '</a>';
			return $links;
		}
	}

	/**
	 * Add links to plugin meta information on plugin list page.
	 *
	 * @param  array  $links Original links.
	 * @param  string $file  File position.
	 *
	 * @return array Combined links.
	 */
	public function plugin_extend_links( $links, $file ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return $links;
		}

		if ( $file == SHIELDON_PLUGIN_NAME ) {
			$links[] = '<a href="https://github.com/terrylinooo/shieldon" target="_blank">' . __( 'View GitHub project', 'wp-shieldon' ) . '</a>';
			$links[] = '<a href="https://github.com/terrylinooo/shieldon/issues" target="_blank">' . __( 'Report issues', 'wp-shieldon' ) . '</a>';
		}
		return $links;
	}

	/**
	 * About me.
	 *
	 * @return void
	 */
	public function about() {
		wpso_show_settings_header();
		echo wpso_load_view( 'setting/about' );
		wpso_show_settings_footer();
	}

	/**
	 * Dashboard
	 *
	 * @param string $page Page tab.
	 *
	 * @return void
	 */
	public function action_logs() {

		$parser = new \Shieldon\Log\ActionLogParser( wpso_get_logs_dir() );

		$tab = 'today';

		if ( ! empty( $_GET['tab'] ) ) {
			$tab = esc_html( $_GET['tab'] );
		}

		switch ( $tab ) {
			case 'yesterday':
			case 'this_month':
			case 'last_month':
			case 'past_seven_days':
			case 'today':
				$type = $tab;
				break;

			default:
				$type = 'today';
		}

		$parser->prepare( $type );

		$data['ip_details']  = $parser->getIpData();
		$data['period_data'] = $parser->getParsedPeriodData();

		if ( 'today' === $type ) {
			$parser->prepare( 'past_seven_hours' );
			$data['past_seven_hour'] = $parser->getParsedPeriodData();
		}

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/dashboard_' . $type, $data );
		wpso_show_settings_footer();
	}

	/**
	 * Rule table for current cycle.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function rule_table() {

		$wpso = wpso_instance();
		$wpso->set_driver();

		if ( isset( $_POST['ip'] ) && check_admin_referer( 'check_form_for_ip_rule', 'wpso-rule-form' ) ) {

			$ip     = sanitize_text_field( $_POST['ip'] );
			$action = sanitize_text_field( $_POST['action'] );

			$action_code['temporarily_ban'] = $wpso->shieldon::ACTION_TEMPORARILY_DENY;
			$action_code['permanently_ban'] = $wpso->shieldon::ACTION_DENY;
			$action_code['allow']           = $wpso->shieldon::ACTION_ALLOW;

			switch ( $action ) {
				case 'temporarily_ban':
				case 'permanently_ban':
				case 'allow':
					$logData['log_ip']     = $ip;
					$logData['ip_resolve'] = gethostbyaddr( $ip );
					$logData['time']       = time();
					$logData['type']       = $action_code[ $action ];
					$logData['reason']     = $wpso->shieldon::REASON_MANUAL_BAN;

					$wpso->shieldon->driver->save( $ip, $logData, 'rule' );
					break;

				case 'remove':
					$wpso->shieldon->driver->delete( $ip, 'rule' );
					break;
			}
		}

		$reason_translation_mapping[99]  = __( 'Added manually by administrator', 'wp-shieldon' );
		$reason_translation_mapping[100] = __( 'Search engine bot', 'wp-shieldon' );
		$reason_translation_mapping[101] = __( 'Google bot', 'wp-shieldon' );
		$reason_translation_mapping[102] = __( 'Bing bot', 'wp-shieldon' );
		$reason_translation_mapping[103] = __( 'Yahoo bot', 'wp-shieldon' );
		$reason_translation_mapping[1]   = __( 'Too many sessions', 'wp-shieldon' );
		$reason_translation_mapping[2]   = __( 'Too many accesses', 'wp-shieldon' );
		$reason_translation_mapping[3]   = __( 'Cannot create JS cookies', 'wp-shieldon' );
		$reason_translation_mapping[4]   = __( 'Empty referrer', 'wp-shieldon' );
		$reason_translation_mapping[11]  = __( 'Daily limit reached', 'wp-shieldon' );
		$reason_translation_mapping[12]  = __( 'Hourly limit reached', 'wp-shieldon' );
		$reason_translation_mapping[13]  = __( 'Minutely limit reached', 'wp-shieldon' );
		$reason_translation_mapping[14]  = __( 'Secondly limit reached', 'wp-shieldon' );

		$type_translation_mapping[0] = __( 'DENY', 'wp-shieldon' );
		$type_translation_mapping[1] = __( 'ALLOW', 'wp-shieldon' );
		$type_translation_mapping[2] = __( 'CAPTCHA', 'wp-shieldon' );

		$data['rule_list']       = $wpso->shieldon->driver->getAll( 'rule' );
		$data['reason_mapping']  = $reason_translation_mapping;
		$data['type_mapping']    = $type_translation_mapping;
		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/rule_table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * IP log table for current cycle.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function filter_log_table() {

		$wpso = wpso_instance();
		$wpso->set_driver();

		$data['ip_log_list']     = $wpso->shieldon->driver->getAll( 'log' );
		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/filter_log_table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * Session table for current cycle.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function session_table() {

		$wpso = wpso_instance();
		$wpso->set_driver();

		$data['session_list'] = $wpso->shieldon->driver->getAll( 'session' );

		$data['is_session_limit']     = false;
		$data['session_limit_count']  = 0;
		$data['session_limit_period'] = 0;
		$data['online_count']         = 0;
		$data['expires']              = 0;

		if ( 'yes' === wpso_get_option( 'enable_online_session_limit', 'shieldon_daemon' ) ) {
			$data['is_session_limit']     = true;
			$data['session_limit_count']  = wpso_get_option( 'session_limit_count', 'shieldon_daemon' );
			$data['session_limit_period'] = wpso_get_option( 'session_limit_period', 'shieldon_daemon' );
			$data['online_count']         = count( $data['session_list'] );
			$data['expires']              = (int) $data['session_limit_period'] * 60;
		}

		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/session_table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * WWW-Authenticate.
	 *
	 * @return void
	 */
	public function authentication() {

		if ( isset( $_POST['action'] ) && check_admin_referer( 'check_form_authentication', 'wpso_authentication_form' ) ) {

			$authenticated_list = get_option( 'shieldon_authetication' );

			$action = sanitize_text_field( $_POST['action'] );
			$order  = sanitize_text_field( $_POST['order'] );
			$url    = sanitize_text_field( $_POST['url'] );
			$user   = sanitize_text_field( $_POST['user'] );
			$pass   = sanitize_text_field( $_POST['pass'] );

			if ( empty( $authenticated_list ) ) {
				$authenticated_list = array();
				update_option( 'shieldon_authetication', $authenticated_list );
			}

			if ( 'add' === $action ) {
                array_push( $authenticated_list, array(
                    'url'  => $url,
                    'user' => $user,
                    'pass' => password_hash( $pass, PASSWORD_BCRYPT ),
                ) );

            } elseif ( 'remove' === $action ) {
                unset( $authenticated_list[ $order ] );
                $authenticated_list = array_values( $authenticated_list );
            }
 
			update_option( 'shieldon_authetication', $authenticated_list );
		}

		// Load the latest authenticated list.
		$authenticated_list = get_option( 'shieldon_authetication' );

		$data = array();

		$data['authenticated_list'] = $authenticated_list;

		wpso_show_settings_header();
		echo wpso_load_view( 'security/authentication', $data );
		wpso_show_settings_footer();
	}

	/**
	 * XSS Protection.
	 *
	 * @return void
	 */
	public function xss_protection() {

		$xss_type = array(
			'get'    => 'no',
			'post'   => 'no',
			'cookie' => 'no',
		);

		$xss_protected_list = array();

		if ( isset( $_POST['xss_post'] ) && check_admin_referer( 'check_form_xss_type', 'wpso_xss_form' ) ) {

			$xss_type = get_option( 'shieldon_xss_protected_type' );

			$xss_type['get']    = sanitize_text_field( $_POST['xss_get'] );
			$xss_type['post']   = sanitize_text_field( $_POST['xss_post'] );
			$xss_type['cookie'] = sanitize_text_field( $_POST['xss_cookie'] );

			update_option( 'shieldon_xss_protected_type', $xss_type );
		}

		if ( isset( $_POST['variable'] ) && check_admin_referer( 'check_form_xss_single', 'wpso_xss_form' ) ) {

			$xss_protected_list = get_option( 'shieldon_xss_protected_list' );

			$action   = sanitize_text_field( $_POST['action'] );
			$order    = sanitize_text_field( $_POST['order'] );
			$type     = sanitize_text_field( $_POST['type'] );
			$variable = sanitize_text_field( $_POST['variable'] );

			if ( empty( $xss_protected_list ) ) {
				$xss_protected_list = array();
				update_option( 'shieldon_xss_protected_list', $xss_protected_list );
			}

			if ( 'add' === $action ) {
                array_push( $xss_protected_list, array(
                    'type'     => $type,
                    'variable' => $variable,
                ) );

            } elseif ( 'remove' === $action ) {
                unset( $xss_protected_list[ $order ] );
                $xss_protected_list = array_values( $xss_protected_list );
            }
 
			update_option( 'shieldon_xss_protected_list', $xss_protected_list );
		}

		$xss_protected_list = get_option( 'shieldon_xss_protected_list' );
		$xss_type           = get_option( 'shieldon_xss_protected_type' );

		$data = [];

		$data['xss_protected_list'] = $xss_protected_list;
		$data['xss_type'] = $xss_type;

		wpso_show_settings_header();
		echo wpso_load_view( 'security/xss_protection', $data );
		wpso_show_settings_footer();
	}

	/**
	 * Overview
	 *
	 * @return void
	 */
	public function overview() {

		$shieldon = \Shieldon\Container::get( 'shieldon' );

		if ( isset( $_POST['action_type'] ) && 'reset_action_logs' === $_POST['action_type'] ) {
			if ( check_admin_referer( 'check_form_reset_action_logger', 'wpso_reset_action_logger_form' ) ) {
				// Remove all action logs.
				$shieldon->logger->purgeLogs();
			}
		}

		if ( isset( $_POST['action_type'] ) && 'reset_data_circle' === $_POST['action_type'] ) {
			if ( check_admin_referer( 'check_form_reset_data_circle', 'wpso_reset_data_circle_form' ) ) {
				$last_reset_time = strtotime( date( 'Y-m-d 00:00:00') );
				// Record new reset time.
				update_option( 'wpso_last_reset_time', $last_reset_time );
				// Remove all data and rebuild data circle tables.
				$shieldon->driver->rebuild();
			}
		}

        /*
        |--------------------------------------------------------------------------
        | Logger
        |--------------------------------------------------------------------------
        |
        | All logs were recorded by ActionLogger.
        | Get the summary information from those logs.
        |
        */

        $data['action_logger'] = false;

        if ( ! empty( $shieldon->logger ) ) {
			$loggerInfo = $shieldon->logger->getCurrentLoggerInfo();

            $data['action_logger'] = true;
        }

        $data['logger_started_working_date'] = 'No record';
        $data['logger_work_days']            = '0 day';
        $data['logger_total_size']           = '0 MB';

        if ( ! empty( $loggerInfo)) {

            $i = 0;
            ksort( $loggerInfo);

            foreach ( $loggerInfo as $date => $size ) {
                if ( 0 === $i ) {
                    $data['logger_started_working_date'] = date( 'Y-m-d', strtotime( (string) $date ) );
                }
                $i += (int) $size;
            }

            $data['logger_work_days']  = count( $loggerInfo );
            $data['logger_total_size'] = round( $i / ( 1024 * 1024), 5 ) . ' MB';
        }

        /*
        |--------------------------------------------------------------------------
        | Data circle
        |--------------------------------------------------------------------------
        |
        | A data circle includes the primary data tables of Shieldon.
        | They are ip_log_table, ip_rule_table and session_table.
        |
        */

        // Data circle.
        $data['rule_list']    = $shieldon->driver->getAll( 'rule' );
        $data['ip_log_list']  = $shieldon->driver->getAll( 'filter_log' );
        $data['session_list'] = $shieldon->driver->getAll( 'session' );

        /*
        |--------------------------------------------------------------------------
        | Shieldon status
        |--------------------------------------------------------------------------
        |
        | 1. Components.
        | 2. Filters.
        | 3. Configuration.
        | 4. Captcha modules.
        | 5. Messenger modules.
        |
        */

        $data['components'] = array(
            'Ip'         => ( ! empty( $shieldon->component['Ip'] ) )         ? true : false,
            'TrustedBot' => ( ! empty( $shieldon->component['TrustedBot'] ) ) ? true : false,
            'Header'     => ( ! empty( $shieldon->component['Header'] ) )     ? true : false,
            'Rdns'       => ( ! empty( $shieldon->component['Rdns'] ) )       ? true : false,
            'UserAgent'  => ( ! empty( $shieldon->component['UserAgent'] ) )  ? true : false,
        );

		$reflection = new ReflectionObject( $shieldon );
		
		$t1 = $reflection->getProperty( 'enableCookieCheck' );
		$t2 = $reflection->getProperty( 'enableSessionCheck' );
		$t3 = $reflection->getProperty( 'enableFrequencyCheck' );
		$t4 = $reflection->getProperty( 'enableRefererCheck' );
		$t5 = $reflection->getProperty( 'properties' );
		$t6 = $reflection->getProperty( 'captcha' );
		$t7 = $reflection->getProperty( 'messengers' );

		$t1->setAccessible( true );
		$t2->setAccessible( true );
		$t3->setAccessible( true );
		$t4->setAccessible( true );
		$t5->setAccessible( true );
		$t6->setAccessible( true );
		$t7->setAccessible( true );

        $enableCookieCheck    = $t1->getValue( $shieldon );
		$enableSessionCheck   = $t2->getValue( $shieldon );
		$enableFrequencyCheck = $t3->getValue( $shieldon );
		$enableRefererCheck   = $t4->getValue( $shieldon );
		$properties           = $t5->getValue( $shieldon );
		$captcha              = $t6->getValue( $shieldon );
		$messengers           = $t7->getValue( $shieldon );
		
        $data['filters'] = array(
            'cookie'    => $enableCookieCheck,
            'session'   => $enableSessionCheck,
            'frequency' => $enableFrequencyCheck,
            'referer'   => $enableRefererCheck,
        );

        $data['configuration'] = $properties;

        $data['driver'] = array(
            'mysql'  => ( $shieldon->driver instanceof \Shieldon\Driver\MysqlDriver),
            'redis'  => ( $shieldon->driver instanceof \Shieldon\Driver\RedisDriver),
            'file'   => ( $shieldon->driver instanceof \Shieldon\Driver\FileDriver),
            'sqlite' => ( $shieldon->driver instanceof \Shieldon\Driver\SqliteDriver),
        );

        $data['captcha'] = array(
            'recaptcha'    => ( isset( $captcha['Recaptcha'] ) ? true : false ),
            'imagecaptcha' => ( isset( $captcha['ImageCaptcha'] ) ? true : false ),
        );

        $operatingMessengers = array(
            'telegram'   => false,
            'linenotify' => false,
            'sendgrid'   => false,
        );

        foreach ( $messengers as $messenger ) {
            $class = get_class( $messenger );
            $class = strtolower( substr( $class, strrpos( $class, '\\' ) + 1 ) );

            if ( isset( $operatingMessengers[ $class ] ) ) {
                $operatingMessengers[ $class ] = true;
            }
        }

        $data['messengers'] = $operatingMessengers;

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/overview', $data );
		wpso_show_settings_footer();
	}
}

