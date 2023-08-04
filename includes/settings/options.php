<?php
/**
 * LifterLMS Progress Reset
 *
 * Displays the LifterLMS Progress Reset Options.
 *
 * @author   WooNinjas
 * @category Admin
 * @package  LifterLMS Progress Reset/Plugin Options
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the License Class
if ( file_exists( LLMS_PROGRESS_RESET_INCLUDES_DIR . 'LLMS_PR_License.php' ) ) {
    require_once LLMS_PROGRESS_RESET_INCLUDES_DIR . 'LLMS_PR_License.php';
}

/**
 * Class LLMS_Progress_Reset_Options
 */
class LLMS_Progress_Reset_Options {

	private $license_class;

	public $page_tab;

    /**
	 * LLMS_Progress_Reset_Options constructor.
	 */
	public function __construct() {
		$this->page_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'license';
		add_action( 'admin_menu', array( $this, 'llms_pr_menu' ) );
		add_filter ( 'admin_footer_text', [ $this, 'remove_footer_admin' ] );
		$this->license_class = new LLMS_PR_License();
    }

	public function get_license_class() {
        return $this->license_class;
    }

    /**
	 * Add plugin's menu
	 */
	public function llms_pr_menu() {

		add_submenu_page(
			'lifterlms',
			__( 'Progress Reset', LLMS_PROGRESS_RESET_TEXT_DOMAIN ),
			__( 'Progress Reset', LLMS_PROGRESS_RESET_TEXT_DOMAIN ),
			'manage_options',
			'lifterlms-pr',
			array( $this, 'LLMS_Progress_Reset_Data' )
		);
    }
    
    /**
	 * Setting page data
	 */
	public function LLMS_Progress_Reset_Data() {

		?>
		<div id="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1><?php echo __( 'LifterLMS Progress Reset', LLMS_PROGRESS_RESET_TEXT_DOMAIN ); ?></h1>

			<div class="nav-tab-wrapper">
				<?php
				$llms_pr_setting_sections = $this->llms_pr_get_setting_sections();
				foreach ( $llms_pr_setting_sections as $key => $llms_pr_settings_section ) {
					?>
					<a href="?page=lifterlms-pr&tab=<?php echo $key; ?>"
					class="nav-tab <?php echo $this->page_tab == $key ? 'nav-tab-active' : ''; ?>">
						<i class="dashicons dashicons-<?php echo $llms_pr_settings_section['icon']; ?>" aria-hidden="true"></i>
						<?php _e( $llms_pr_settings_section['title'], LLMS_PROGRESS_RESET_TEXT_DOMAIN ); ?>
					</a>
					<?php
				}
				?>
			</div>

			<?php
                foreach ( $llms_pr_setting_sections as $key => $llms_pr_setting_section ) {
					if ( $this->page_tab == $key ) {
						include 'templates/' . $key . '.php';
					}
				}
			?>
		</div>
		<?php
	}

	/**
	 * LLMS_Progress_Reset Settings Sections
	 *
	 * @return mixed|void
	 */
	public function llms_pr_get_setting_sections() {

			$llms_pr_settings_sections = array(
				'license' => array(
                    'title' => __( 'License Option', LLMS_PROGRESS_RESET_TEXT_DOMAIN ),
                    'icon'  => 'update',
                ),
				'progress'         => array(
					'title' => __( 'Progress Reset Settings', LLMS_PROGRESS_RESET_TEXT_DOMAIN ),
					'icon'  => 'admin-settings',
				),
			);


		return apply_filters( 'llms_pr_settings_section', $llms_pr_settings_sections );

	}
	
	/**
     * Add footer branding
     *
     * @param $footer_text
     * @return mixed
     */
    function remove_footer_admin ( $footer_text ) {
        if( isset( $_GET['page'] ) && ( $_GET['page'] == 'lifterlms-pr' ) ) {
            _e('Fueled by <a href="http://www.wordpress.org" target="_blank">WordPress</a> | developed and designed by <a href="https://wooninjas.com" target="_blank">The WooNinjas</a></p>');
        } else {
            return $footer_text;
        }
    }
}

$GLOBALS['LLMS_Progress_Reset_Options'] = new LLMS_Progress_Reset_Options();