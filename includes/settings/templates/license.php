<?php
/**
 * License Options
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$license_class = $GLOBALS['LLMS_Progress_Reset_Options']->get_license_class();
?>
<div id="llms_pr_license_options">
    <form method="post">
        <h2><?php _e( 'License Configuration', LLMS_PROGRESS_RESET_TEXT_DOMAIN ); ?></h2>
        <h3><?php _e( 'Please enter the license key for this product to get automatic updates. You were emailed the license key when you purchased this item', LLMS_PROGRESS_RESET_TEXT_DOMAIN ); ?></h3>
        <table class="form-table">
            <tr>
                <th style="width:100px;"><label
                        for="<?php echo $license_class->get_license_key_field(); ?>"><?php _e( 'License Key', LLMS_PROGRESS_RESET_TEXT_DOMAIN ); ?></label>
                </th>
                <td>
                    <input class="regular-text" type="text" id="<?php echo $license_class->get_license_key_field(); ?>"
                           placeholder="Enter license key provided with plugin"
                           name="<?php echo $license_class->get_license_key_field(); ?>"
                           value="<?php echo get_option( 'wn_llms_pr_license_key' ); ?>"
                        <?php echo ( $license_class->get_license_handler()->is_active() ) ? 'readonly' : ''; ?>>
                </td>
            </tr>
        </table>
        <p class="submit">
            <?php if( ! $license_class->get_license_handler()->is_active() ) : ?>
                <input type="submit" name="llms_pr_activate_license" value="<?php _e( 'Activate', LLMS_PROGRESS_RESET_TEXT_DOMAIN ); ?>"
                       class="button-primary"/>
            <?php endif; ?>

            <?php if( $license_class->get_license_handler()->is_active() ) : ?>
                <input type="submit" name="llms_pr_deactivate_license" value="<?php _e( 'Deactivate', LLMS_PROGRESS_RESET_TEXT_DOMAIN ); ?>"
                       class="button-primary"/>
            <?php endif; ?>
        </p>
    </form>
</div>