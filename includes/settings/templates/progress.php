<?php
/**
 * Provide a admin area view for the plugin settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package     LifterLMS-Progress-Reset
 * @since       1.0.0
 *
 * @package    LifterLMS-Progress-Reset
 * @subpackage LifterLMS-Progress-Reset/includes/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$progress_reset_class_instance = new LLMS_Progress_Reset_Data_Class();
$llmspr_users = $progress_reset_class_instance->llms_get_all_users_admin();
$llmspr_courses = $progress_reset_class_instance->llms_get_all_courses_admin();
$llmspr_memberships = $progress_reset_class_instance->llms_get_all_memberships_admin();
$llmspr_select_all_users = true;
$llmspr_select_all_courses = true;
$llmspr_select_all_memberships = true;

?>

<div class="llmspr-progress-reset-wrap">
    <h2><?php _e('Progress Reset Form', 'llms-progress-reset'); ?></h2>
    <div class="llmspr-progress-reset-form">
        <form method="post" id="llmspr-reset-form">
        <table class="form-table">
            <tr>
                <td style="width:40%;">
                    <label class="llmspr-input-labels">
                        <h4 class="llmspr-input-headings"><?php _e('Reset Email:', 'llms-progress-reset'); ?></h4>
                        <span class="llmspr-input-sub-headings"><?php _e('Selected option will reset engagement emails.', 'llms-progress-reset'); ?></span>
                    </label>
                </td>
                <td style="width:40%;">
                    <label for="llmspr_email">
                        <input type="checkbox"  id="llmspr_email" name="llmspr_email"> <?php _e('Yes', 'llms-progress-reset'); ?>&nbsp;&nbsp;
                    </label>
                </td>
            </tr>
            <tr>
                <td style="width:40%;">
                    <label for="llmspr_users" class="llmspr-input-labels">
                        <h4 class="llmspr-input-headings"><?php _e('Select Users:', 'llms-progress-reset'); ?></h4>
                        <span class="llmspr-input-sub-headings"><?php _e('Selected users will have their course(s) progress reset.', 'llms-progress-reset'); ?></span>
                    </label>
                </td>
                <td style="width:40%;">
                    <select class="llmspr-select-multiple_user" id="llmspr_users" name="llmspr_users[]" multiple="multiple">
                    <?php $llmspr_select_all_users = apply_filters('llms_progress_reset_select_all_users', $llmspr_select_all_users); ?>
                    
                    </select>
                </td>
                <td style="width:20%;">
                <input class="button button-primary" id="llmspr_users_clear" type="button" value="<?php esc_attr_e( 'Clear' ); ?>" />
                </td>
            </tr>

            <tr>
                <td style="width:40%;">
                    <label for="llmspr_courses" class="llmspr-input-labels">
                        <h4 class="llmspr-input-headings"><?php _e('Select Courses:', 'llms-progress-reset'); ?></h4>
                        <span class="llmspr-input-sub-headings"><?php _e('Selected courses will have progress reset of its enrolled user(s).', 'llms-progress-reset'); ?></span>
                    </label>
                </td>
                <td style="width:40%;">
                    <select class="llmspr-select-multiple_course" id="llmspr_courses" name="llmspr_courses[]" multiple="multiple">
                    </select>
                </td>
                <td style="width:20%;">
                <input class="button button-primary" id="llmspr_courses_clear" type="button" value="<?php esc_attr_e( 'Clear' ); ?>" />
                </td>
            </tr>

            <tr>
                <td style="width:40%;">
                    <label for="llmspr_memberships" class="llmspr-input-labels">
                        <h4 class="llmspr-input-headings"><?php _e('Select Memberships:', 'llms-progress-reset'); ?></h4>
                        <span class="llmspr-input-sub-headings"><?php _e('Selected memberships will have its course(s) progress reset of all enrolled users.', 'llms-progress-reset'); ?></span>
                    </label>
                </td>
                <td style="width:40%;">
                    <select class="llmspr-select-multiple" id="llmspr_memberships" name="llmspr_memberships[]" multiple="multiple">
                    <?php $llmspr_select_all_memberships = apply_filters('llms_progress_reset_select_all_memberships', $llmspr_select_all_memberships); ?>
                    <?php if( $llmspr_select_all_memberships ) : ?>
                        <option value="all"><?php _e('All', 'llms-progress-reset'); ?></option>
                    <?php endif; ?>
                    </select>
                </td>
                <td style="width:20%;">
                    <input class="button button-primary" id="llmspr_memberships_clear" type="button" value="<?php esc_attr_e( 'Clear' ); ?>" />
                </td>
            </tr>
        </table>
        
        <table style="width:100%;">
            <tr>
                <td style="width:15%; padding: 8px 10px;">
                    <input type="hidden" name="action" value="perform_action">
                    <input name="submit" class="button button-primary" id="llmspr-submit-button" type="submit" value="<?php esc_attr_e( 'Reset Progress' ); ?>" />
                </td>
                <td style="width:85%; padding: 8px 10px;">
                    <div id="llmspr_progress_container" style="width:74%;">
                    <div id="llmspr_progress_wrap">
                        <div id="llmspr_progress_bar"></div>
                    </div>
                    <span id="llmspr_info"></span>
                </div>
                </td>
            </tr>
        </table>
        <hr>

        </form>
    </div>
</div>