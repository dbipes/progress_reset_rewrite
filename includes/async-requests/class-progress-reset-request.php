<?php

class LLMS_Progress_Reset_Request extends LLMS_PR_WP_Async_Request {

	use LLMS_PR_Class_Handlers;

	/**
	 * @var string
	 */
	protected $action = 'llmspr_request';

	/**
	 * Handle
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 */
	protected function handle() {

		$this->really_long_running_task();

		/**
		 * Filter: ldpr_disallow_all_users_reset
		 *
		 * Disallow admin to reset all users at once or leave empty
		 */
		$disallow_all_users_reset = apply_filters( 'ldpr_disallow_all_users_reset', false );
		if( $disallow_all_users_reset ) {
			if ( isset($_POST['llmspr_users']) ) {
				$data_class = new LLMS_Progress_Reset_Data_Class();
				$llmspr_users = $data_class->llms_get_all_users_admin();
				$is_all = count($llmspr_users) == count($_POST['llmspr_users']) ? true : false;
				if( $is_all ) {
					$resp = array('status' => false, 'message' => __('Stopped: You cannot reset all Users at once!', 'llms-progress-reset'), 'data' => []);
					wp_send_json($resp);
					return;
				}
			}
			if ( !isset($_POST['llmspr_users']) ) {
				$resp = array('status' => false, 'message' => __('Stopped: You must have to select specific Users!', 'llms-progress-reset'), 'data' => []);
				wp_send_json($resp);
				return;
			}
		}

		$progress_reset_user_data = array();
		$progress_status = false;
		$progress_reset_message = __('No progress reset was done!', 'llms-progress-reset');

		$option = '';
		if ( isset($_POST['llmspr_email']) ) {
			$option = $_POST['llmspr_email'];
		}

		// Checks if all POST variables are set
		if ( isset($_POST['llmspr_users']) && isset($_POST['llmspr_courses']) && isset($_POST['llmspr_memberships']) ) {

			/**
			 * This section handles the logic to reset the progress for all Users, Courses and Memberships.
			 * Here we handle the reset logic of combination Users, Courses and Memberships.
			 *
			 */

			$llmspr_users = $_POST['llmspr_users'];
			$llmspr_courses = $_POST['llmspr_courses'];
			$llmspr_memberships = $_POST['llmspr_memberships'];

			foreach ($llmspr_users as $user) {
				if( $user == 'all' ) continue;

				foreach ($llmspr_courses as $course) {
					if( $course == 'all' ) continue;

					$LLMS_Student = new LLMS_Student( $user );
					$is_user_enrolled_in_course = $LLMS_Student->is_enrolled( $course );

					if( $is_user_enrolled_in_course ) {
				
						foreach ($llmspr_memberships as $membership) {
							if( $membership == 'all' ) continue;

							$membership_obj = new LLMS_Membership( $membership );
							$membership_courses = $membership_obj->get_associated_posts( 'course' );

							if( ! empty($membership_courses) ) {
								if ( in_array( $course, $membership_courses ) ) {
									$status = self::reset_course_progress($user, $course, $option);
									if( $status ) {
										$user_meta = get_userdata( $user );
										$progress_reset_user_data[] = array(
														'ID' => $user_meta->ID,
														'Email' => $user_meta->user_email,
													);
										$progress_status = true;
										$progress_reset_message = __('Progress reset done successfully!', 'llms-progress-reset');
									}
								}
							}
						}
						
					}

				}

			}
			$progress_reset_user_data = array_values(array_unique( $progress_reset_user_data, SORT_REGULAR ));
			$resp = array('status' => $progress_status, 'message' => $progress_reset_message, 'data' => $progress_reset_user_data);
			wp_send_json($resp);

		} elseif ( isset($_POST['llmspr_users']) && isset($_POST['llmspr_courses']) ) {

			/**
			 * This section handles the logic to reset the progress for all Users and Courses.
			 * Here we handle the reset logic of combination Users and Courses.
			 *
			 */

			$llmspr_users = $_POST['llmspr_users'];
			$llmspr_courses = $_POST['llmspr_courses'];

			foreach ($llmspr_users as $user) {
				if( $user == 'all' ) continue;

				foreach ($llmspr_courses as $course) {
					if( $course == 'all' ) continue;

					$LLMS_Student = new LLMS_Student( $user );
					$is_user_enrolled_in_course = $LLMS_Student->is_enrolled( $course );

					if( $is_user_enrolled_in_course ) {
						$status = self::reset_course_progress($user, $course, $option);
						if( $status ) {
							$user_meta = get_userdata( $user );
							$progress_reset_user_data[] = array(
											'ID' => $user_meta->ID,
											'Email' => $user_meta->user_email,
										);
							$progress_status = true;
							$progress_reset_message = __('Progress reset done successfully!', 'llms-progress-reset');
						}
					}

				}

			}
			$progress_reset_user_data = array_values(array_unique( $progress_reset_user_data, SORT_REGULAR ));
			$resp = array('status' => $progress_status, 'message' => $progress_reset_message, 'data' => $progress_reset_user_data);
			wp_send_json($resp);

		} elseif ( isset($_POST['llmspr_users']) && isset($_POST['llmspr_memberships']) ) {

			/**
			 * This section handles the logic to reset the progress for all Users and Memberships.
			 * Here we handle the reset logic of combination Users and Memberships.
			 *
			 */

			$llmspr_users = $_POST['llmspr_users'];
			$llmspr_memberships = $_POST['llmspr_memberships'];

			foreach ($llmspr_users as $user) {
				if( $user == 'all' ) continue;

				$LLMS_Student = new LLMS_Student( $user );
				$user_memberships = $LLMS_Student->get_memberships();

				if( ! empty($user_memberships['results']) ) {

					foreach ($llmspr_memberships as $membership) {
						if( $membership == 'all' ) continue;

						if ( in_array( $membership, $user_memberships['results'] ) ) {
						
						$membership_obj = new LLMS_Membership( $membership );
						$membership_courses = $membership_obj->get_associated_posts( 'course' );

							foreach ($membership_courses as $course) {
								if( isset($course) ) {

									$LLMS_Student = new LLMS_Student( $user );
									$is_user_enrolled_in_course = $LLMS_Student->is_enrolled( $course );

									if( $is_user_enrolled_in_course ) {
										$status = self::reset_course_progress($user, $course, $option);
										if( $status ) {
											$user_meta = get_userdata( $user );
											$progress_reset_user_data[] = array(
															'ID' => $user_meta->ID,
															'Email' => $user_meta->user_email,
														);
											$progress_status = true;
											$progress_reset_message = __('Progress reset done successfully!', 'llms-progress-reset');
										}
									}
									
								}
							}
						}
					}
				}

			}
			$progress_reset_user_data = array_values(array_unique( $progress_reset_user_data, SORT_REGULAR ));
			$resp = array('status' => $progress_status, 'message' => $progress_reset_message, 'data' => $progress_reset_user_data);
			wp_send_json($resp);

		} elseif ( isset($_POST['llmspr_courses']) && isset($_POST['llmspr_memberships']) ) {

			/**
			 * This section handles the logic to reset the progress for all Courses and Memberships.
			 * Here we handle the reset logic of combination Courses and Memberships.
			 *
			 */

			$llmspr_courses = $_POST['llmspr_courses'];
			$llmspr_memberships = $_POST['llmspr_memberships'];

			foreach ($llmspr_courses as $course) {
				if( $course == 'all' ) continue;
				
				foreach ($llmspr_memberships as $membership) {
					if( $membership == 'all' ) continue;

					$membership_obj = new LLMS_Membership( $membership );
					$membership_courses = $membership_obj->get_associated_posts( 'course' );

					if( ! empty($membership_courses) ) {
						if ( in_array( $course, $membership_courses ) ) {

							$course_users = llms_get_enrolled_students( $course, 'enrolled', 1000 );

							foreach ($course_users as $user) {
								if( isset($user) ) {
									$status = self::reset_course_progress($user, $course, $option);
									if( $status ) {
										$user_meta = get_userdata( $user );
										$progress_reset_user_data[] = array(
														'ID' => $user_meta->ID,
														'Email' => $user_meta->user_email,
													);
										$progress_status = true;
										$progress_reset_message = __('Progress reset done successfully!', 'llms-progress-reset');
									}
								}
							}
						}
					}
				}

			}
			$progress_reset_user_data = array_values(array_unique( $progress_reset_user_data, SORT_REGULAR ));
			$resp = array('status' => $progress_status, 'message' => $progress_reset_message, 'data' => $progress_reset_user_data);
			wp_send_json($resp);

		} elseif ( isset($_POST['llmspr_users']) ) {

			/**
			 * This section handles the logic to reset the progress for all Users.
			 * Here we handle the reset logic of Users only.
			 *
			 */

			$llmspr_users = $_POST['llmspr_users'];			

			foreach ($llmspr_users as $user) {
				if( $user == 'all' ) continue;

				$LLMS_Student = new LLMS_Student( $user );
				$user_courses = $LLMS_Student->get_courses();

				foreach ($user_courses['results'] as $course) {
					if( isset($course) ) {
						$status = self::reset_course_progress($user, $course, $option);
						if( $status ) {
							$user_meta = get_userdata( $user );
							$progress_reset_user_data[] = array(
											'ID' => $user_meta->ID,
											'Email' => $user_meta->user_email,
										);
							$progress_status = true;
							$progress_reset_message = __('Progress reset done successfully!', 'llms-progress-reset');
						}
					}
				}
			}
			$progress_reset_user_data = array_values(array_unique( $progress_reset_user_data, SORT_REGULAR ));
			$resp = array('status' => $progress_status, 'message' => $progress_reset_message, 'data' => $progress_reset_user_data);
			wp_send_json($resp);

		} elseif ( isset($_POST['llmspr_courses']) ) {

			/**
			 * This section handles the logic to reset the progress for all Courses.
			 * Here we handle the reset logic of Courses only.
			 *
			 */

			$llmspr_courses = $_POST['llmspr_courses'];

			foreach ($llmspr_courses as $course) {
				if( $course == 'all' ) continue;

				$course_users = llms_get_enrolled_students( $course, 'enrolled', 1000 );

				foreach ($course_users as $user) {
					if( isset($user) ) {
						$status = self::reset_course_progress($user, $course, $option);
						if( $status ) {
							$user_meta = get_userdata( $user );
							$progress_reset_user_data[] = array(
											'ID' => $user_meta->ID,
											'Email' => $user_meta->user_email,
										);
							$progress_status = true;
							$progress_reset_message = __('Progress reset done successfully!', 'llms-progress-reset');
						}
					}
				}
			}
			$progress_reset_user_data = array_values(array_unique( $progress_reset_user_data, SORT_REGULAR ));
			$resp = array('status' => $progress_status, 'message' => $progress_reset_message, 'data' => $progress_reset_user_data);
			wp_send_json($resp);

		} elseif ( isset($_POST['llmspr_memberships']) ) {

			/**
			 * This section handles the logic to reset the progress for all Memberships.
			 * Here we handle the reset logic of Memberships only.
			 *
			 */

			$llmspr_memberships = $_POST['llmspr_memberships'];

			foreach ($llmspr_memberships as $membership) {
				if( $membership == 'all' ) continue;

				$membership_obj = new LLMS_Membership( $membership );
				$membership_courses = $membership_obj->get_associated_posts( 'course' );

				foreach ($membership_courses as $course) {
					if( isset($course) ) {
						$course_users = llms_get_enrolled_students( $course, 'enrolled', 1000 );
						foreach ($course_users as $user) {
							if( isset($user) ) {
								$status = self::reset_course_progress($user, $course, $option);
								if( $status ) {
									$user_meta = get_userdata( $user );
									$progress_reset_user_data[] = array(
													'ID' => $user_meta->ID,
													'Email' => $user_meta->user_email,
												);
									$progress_status = true;
									$progress_reset_message = __('Progress reset done successfully!', 'llms-progress-reset');
								}
							}
						}
					}
				}
			}
			$progress_reset_user_data = array_values(array_unique( $progress_reset_user_data, SORT_REGULAR ));
			$resp = array('status' => $progress_status, 'message' => $progress_reset_message, 'data' => $progress_reset_user_data);
			wp_send_json($resp);

		}

	}

}