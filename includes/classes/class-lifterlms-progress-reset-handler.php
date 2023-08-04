<?php
/**
 * This file is used to handle the progress reset request.
 *
 * @package     LifterLMS-Progress-Reset
 * @since       1.0.0
 *
 * @package    LifterLMS-Progress-Reset
 * @subpackage LifterLMS-Progress-Reset/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'LLMS_Progress_Reset_Handler_Class' ) ) {

    /**
     *  LLMS_Progress_Reset_Handler_Class class
     *
     * @since       1.0.0
     */
    class LLMS_Progress_Reset_Handler_Class {

        /**
         * @var LLMS_Progress_Reset_Request
         */
        protected $process_request;

        public function __construct() {

            add_action( 'wp_ajax_perform_action', array( $this, 'process' ) );
            add_action( 'wp_ajax_nopriv_perform_action', array( $this, 'process' ) );
            //ajax request for user filter
            add_action( 'wp_ajax_get_wp_users', array( $this, 'wp_filter_user_cb' ) );
            add_action( 'wp_ajax_nopriv_get_wp_users', array( $this, 'wp_filter_user_cb' ) );
            //ajax request for course filter
            add_action( 'wp_ajax_get_wp_courses', array( $this, 'wp_filter_course_cb' ) );
            add_action( 'wp_ajax_nopriv_get_wp_courses', array( $this, 'wp_filter_course_cb' ) );
            // add_action( 'http_api_debug', array( $this, 'llmspr_http_api_debug' ), 10, 5 );
            $this->process_request = new LLMS_Progress_Reset_Request();

        }

        public function llmspr_http_api_debug( $response, $type, $class, $args, $url ) {
			// You can change this from error_log() to var_dump() but it can break AJAX requests
			error_log( 'Request URL: ' . var_export( $url, true ) );
			error_log( 'Request Args: ' . var_export( $args, true ) );
			error_log( 'Request Response : ' . var_export( $response, true ) );
		}

        /**
         * Main Method - This method handles the whole business logic for Progress Reset
         * Runs all operations in this method
         *
         */
        public function process() {

            // Checks if logged in user is Admin or not
            if( ! current_user_can('manage_options') || ! is_admin()  ) {
                return;
            }

            // Checks if server request is set or not
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
                unset($_POST['action']);

                $this->process_request->data( $_POST )->dispatch();

            }

        }
        public function wp_filter_user_cb(){
            $search_query = sanitize_text_field($_GET['q']);
            $page = ! empty( absint( $_GET['page'] ) ) ? absint($_GET['page']) : 1;
            $total_results = 10;
            $args = array(
                'number'  => $total_results,
                'paged'   => $page,
                'order'   => 'ASC',
                'orderby' => 'display_name',
                'search'  => '*' . esc_attr( $search_query ) . '*',
                'search_columns' => array( 'user_login', 'user_email','user_nicename' ),
            );

            // Create the WP_User_Query object
            $wp_user_query = new WP_User_Query( $args );

            // Get the results
            $users_via_table = $wp_user_query->get_results();

            //search from the usermeta
            $users_query_meta = new WP_User_Query(
                array(
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'first_name',
                        'value' => $search_query,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => 'last_name',
                        'value' => $search_query,
                        'compare' => 'LIKE'
                    )
                )
                )
            );
            $users_via_meta = $users_query_meta->get_results();
            // Merge both result.. 
            $combined_users  = array_merge( $users_via_table, $users_via_meta );
            // Get unique user
            $results = array_unique( $combined_users, SORT_REGULAR );

            if ( ! empty( $results ) ) {

                $users = [];

                // loop through each user
                foreach ( $results as $user ) {
                    $user_info = get_userdata( $user->ID );
                    $users[] = array(
                        'id'   => $user_info->ID,
                        'text' => $user_info->display_name." (".$user_info->user_email.")",
                    );

                }
            }

            $response = array(
                'results'     => $users,
                'count_total' => $wp_user_query->get_total(),
            );
            echo json_encode( $response );

            die();

        }
        public function wp_filter_course_cb(){
            $search = sanitize_text_field($_GET['q']);
            $page = ! empty( absint( $_GET['page'] ) ) ? absint($_GET['page']) : 1;
            $args = array(
                'post_type' => 'course',
                's' => $search,
                'post_status' => 'publish',
                'posts_per_page' => 10,
                'paged' => $page,
                'orderby' => 'title',
                'order' => 'ASC',
            );
            $query = new WP_Query( $args );
                $courses = [];
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $post_id = get_the_ID();
                    $post_name = get_the_title();
                    $courses[] = array(
                        'id' => $post_id,
                        'text' => $post_name,
                    );
                }
                // reset post data
                wp_reset_postdata();
                $response = array(
                    'results'     => $courses,
                    'count_total' => $query->found_posts,
                );
                echo json_encode( $response );
                die();

        }

    }

    new LLMS_Progress_Reset_Handler_Class();
}