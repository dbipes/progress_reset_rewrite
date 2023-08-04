<?php

/**
 * License Class.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LLMS_PR_License {
    private $license_key_field = null;

    /**
     * @var LLMS_PR_License_Handler
     */
    private $license_handler = null;

    public function __construct() {

        $this->license_key_field = 'wn_llms_pr_license_key';

        add_action( 'init', [ $this, 'plugin_init' ] );
        add_action( 'admin_notices', [ $this, 'show_license_expire_or_invalid' ], 20 );

        /**
         * Enable these for local testing
         */
         # add_filter( 'llms_pr_sl_api_request_verify_ssl', '__return_false', 10, 2 );
         # add_filter( 'https_ssl_verify', '__return_false' );
         # add_filter( 'http_request_host_is_external', '__return_true', 10, 3 );
    }

    public function plugin_init() {
        if ( ! current_user_can( 'manage_options' ) || ! is_admin() )
            return;

        if( !function_exists('get_plugin_data') ){
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $plugin_data = get_plugin_data( LLMS_PROGRESS_RESET_DIR_FILE );
        $this->license_handler = new LLMS_PR_License_Handler( LLMS_PROGRESS_RESET_DIR_FILE, $plugin_data['Name'], $plugin_data['Version'], $plugin_data['AuthorName'], $this->license_key_field );
    }

    public function show_license_expire_or_invalid() {
        if ( ! isset( $this->license_handler ) )
            return;

        $license_setting_url = add_query_arg( array( 'page' => 'lifterlms-pr' ), admin_url( 'admin.php' ) );
        $error_msg = '';
        $success_msg = '';
        $submission = isset( $_POST['llms_pr_activate_license'] ) || isset( $_POST['llms_pr_deactivate_license'] );
        $invalid_license_err = __( 'Please enter a valid license key for <strong> LifterLMS Progress Reset</strong> to receive latest updates. <a href="' . esc_attr( $license_setting_url ) . '">License Settings</a>', LLMS_PROGRESS_RESET_TEXT_DOMAIN );
        $expired_license_err = __( 'Your license for <strong> LifterLMS Progress Reset</strong> has been expired. You will not receive any future updates for this addon. Please purchase the addon from our site, <a href="https://wooninjas.com/wn-products/lifterlms-progress-reset/">here</a> to recieve a valid license key.', LLMS_PROGRESS_RESET_TEXT_DOMAIN );

        if( $submission ) {
            if( $this->license_handler->is_active() ) {
                $success_msg = __( 'License Activated!', LLMS_PROGRESS_RESET_TEXT_DOMAIN );
            } else if( $this->license_handler->is_expired() ) {
                $error_msg = $expired_license_err;
            } else if( $this->license_handler->last_err() ) {
                $error_msg = $invalid_license_err;
            } else if( !$this->license_handler->is_active() ) {
                $success_msg = __( 'License Deactivated!', LLMS_PROGRESS_RESET_TEXT_DOMAIN );
            }
        } else {
            if ( $this->license_handler->is_expired() ) {
                $error_msg = $expired_license_err;
            } else if( !$this->license_handler->is_active() ) {
                $error_msg = $invalid_license_err;
            }
        }

        if( $success_msg ) { ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo $success_msg; ?></p>
            </div>
            <?php
        } else if( $error_msg ) { ?>
            <div class="error notice">
                <p><?php echo $error_msg; ?></p>
            </div>
            <?php
        }
    }

	/**
	 * @return LLMS_PR_License_Handler
	 */
    public function get_license_handler() {
        return $this->license_handler;
    }

	/**
	 * @return string
	 */
	public function get_license_key_field() {
		return $this->license_key_field;
	}
}
