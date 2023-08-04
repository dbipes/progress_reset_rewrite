<?php
/**
 * Scripts & Styles
 *
 * @package     LifterLMS-Progress-Reset\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function llms_progress_reset_admin_scripts() {

    wp_enqueue_script( 'llms_pr_admin_js', LLMS_PROGRESS_RESET_ASSETS_URL . 'js/admin.js', array( 'jquery' ), false, true );
    wp_enqueue_style( 'llms_pr_admin_css', LLMS_PROGRESS_RESET_ASSETS_URL . 'css/admin.css' );
    
    // Localize the script with new data
    $llms_pr_local_vars = array(
        'session_id' => session_id(),
    );
    
    wp_localize_script( 'llms_pr_admin_js', 'llms_vars', $llms_pr_local_vars );

    wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css' );
	wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js', array('jquery') );
}
add_action( 'admin_enqueue_scripts', 'llms_progress_reset_admin_scripts', 100 );