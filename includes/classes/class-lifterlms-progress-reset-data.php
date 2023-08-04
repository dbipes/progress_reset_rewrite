<?php
/**
 * LLMS_Progress_Reset_Data_Class
 *
 * @package     LifterLMS-Progress-Reset
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'LLMS_Progress_Reset_Data_Class' ) ) {

    /**
     *  LLMS_Progress_Reset_Data_Class class
     *
     * @since       1.0.0
     */
    class LLMS_Progress_Reset_Data_Class {

        /**
         * Get all users
         *
         * @access      public
         * @since       1.0.0
         * @return      array
         *
         */
        public function llms_get_all_users_admin() {

            if( ! current_user_can('manage_options') || ! is_admin()  ) {
                return;
            }

            $users = array();
            // $all_users = get_users( [ 'role__in' => [ 'group_leader', 'customer' ] ] );
            $all_users = get_users();
            foreach( $all_users as $user ) {
                $users[] = json_decode(json_encode($user->data), true);
            }

            return $users;
        }

        /**
         * Get all courses
         *
         * @access      public
         * @since       1.0.0
         * @return      array
         *
         */
        public function llms_get_all_courses_admin() {

            if( ! current_user_can('manage_options') || ! is_admin()  ) {
                return;
            }

            $courses_query_args = array(
                'post_type'   => 'course',
                'nopaging'    => true,
                'post_status' => array( 'publish'),
            );
            
            $courses_query = new WP_Query( $courses_query_args );
            $courses = json_decode(json_encode($courses_query->posts), true);

            return $courses;
        }

        /**
         * Get all memberships
         *
         * @access      public
         * @since       1.0.0
         * @return      array
         *
         */
        public function llms_get_all_memberships_admin() {

            if( ! current_user_can('manage_options') || ! is_admin()  ) {
                return;
            }

            $memberships_query_args = array(
                'post_type'   => 'llms_membership',
                'nopaging'    => true,
                'post_status' => array( 'publish'),
            );
            
            $memberships_query = new WP_Query( $memberships_query_args );
            $memberships = json_decode(json_encode($memberships_query->posts), true);

            return $memberships;
        }


    }

}