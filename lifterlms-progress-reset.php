<?php

/**
 * Plugin Name: LifterLMS Progress Reset
 * Plugin URI: https://wooninjas.com/wn-products/lifterlms-progress-reset
 * Description: Allows LifterLMS admin to reset engagement email sequence and the progress of users, courses and memberships.
 * Version: 1.0.4
 * Requires at least: 5.1
 * Requires PHP: 7.2
 * Author: WooNinjas
 * Author URI: https://wooninjas.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: llms-progress-reset
 * Doman Path: /languages/
 */


// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Plugin version
define('LLMS_PROGRESS_RESET_VER', '1.0.4');

if (!class_exists('LLMS_Progress_Reset')) {

    /**
     * Main LLMS_Progress_Reset class
     *
     * @since       1.0.0
     */
    class LLMS_Progress_Reset
    {

        /**
         * @var         LLMS_Progress_Reset $instance The one true LLMS_Progress_Reset
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true LLMS_Progress_Reset
         */
        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new LLMS_Progress_Reset();
                self::$instance->setup_constants();
                self::$instance->includes();
                // self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants()
        {

            /**
             * Plugin Text Domain
             */
            define('LLMS_PROGRESS_RESET_TEXT_DOMAIN', 'llms-progress-reset');

            // Plugin Path
            define('LLMS_PROGRESS_RESET_DIR', plugin_dir_path(__FILE__));

            define('LLMS_PROGRESS_RESET_DIR_FILE', LLMS_PROGRESS_RESET_DIR . basename(__FILE__));

            // Plugin Includes Folder Path
            define('LLMS_PROGRESS_RESET_INCLUDES_DIR', trailingslashit(LLMS_PROGRESS_RESET_DIR . 'includes'));

            // Plugin URL
            define('LLMS_PROGRESS_RESET_URL', trailingslashit(plugins_url('', __FILE__)));

            // Plugin Assets URL
            define('LLMS_PROGRESS_RESET_ASSETS_URL', trailingslashit(LLMS_PROGRESS_RESET_URL . 'assets'));
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes()
        {

            /**
             * LifterLMS Progress Reset Data Class
             */
            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'classes/class-lifterlms-progress-reset-data.php';

            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'classes/class-async-request.php';

            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'class-handlers.php';

            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'async-requests/class-progress-reset-request.php';

            /**
             * LifterLMS Progress Reset Handler Class
             */
            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'classes/class-lifterlms-progress-reset-handler.php';

            /**
             * LifterLMS Progress Reset Settings Menu/Page
             */
            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'settings/options.php';

            /**
             * LifterLMS Progress Reset License Handler Class
             */
            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'LLMS_PR_License_Handler.php';

            /**
             * LifterLMS Progress Reset Assets Loader
             */
            require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'llms-pr-scripts-loader.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         *
         */
        private function hooks()
        {
            // Register settings
        }
    }
} // End if class_exists check


/**
 * Display admin notifications if dependency not found.
 */
function llms_pr_dependency_check()
{
    if (!current_user_can('manage_options') || !is_admin()) {
        return;
    }

    if (!class_exists('LifterLMS')) {
        $class   = 'notice is-dismissible error';
        $message = __('LifterLMS Progress Reset add-on requires <a href="https://www.lifterlms.com" target="_BLANK">LifterLMS</a> plugin to be activated.', 'llms-progress-reset');
        printf('<div id="message" class="%s"> <p>%s</p></div>', $class, $message);
        deactivate_plugins(plugin_basename(__FILE__));
    }
    return true;
}

/**
 * @return bool
 */
function LLMS_Progress_Reset()
{
    if (!class_exists('LifterLMS')) {
        add_action('admin_notices', 'llms_pr_dependency_check');
        return false;
    }

    $GLOBALS['LLMS_Progress_Reset'] = LLMS_Progress_Reset::instance();
}
add_action('plugins_loaded', 'LLMS_Progress_Reset');


/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function LLMS_Progress_Reset_activation()
{
    if (!current_user_can('activate_plugins')) {
        return;
    }

    update_option('llms_progress_reset_version', LLMS_PROGRESS_RESET_VER);
}
register_activation_hook(__FILE__, 'LLMS_Progress_Reset_activation');

/**
 * Deactivation function hook
 *
 * @since       1.0.0
 * @return void
 */
function LLMS_Progress_Reset_deactivation()
{
    delete_option('llms_progress_reset_version');
}
register_deactivation_hook(__FILE__, 'LLMS_Progress_Reset_deactivation');
