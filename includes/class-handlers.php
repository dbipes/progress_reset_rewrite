<?php

trait LLMS_PR_Class_Handlers {

	/**
	 * Really long running process
	 *
	 * @return int
	 */
	public function really_long_running_task() {
		return sleep( 1 );
	}

	/**
	 * Log
	 *
	 * @param string $message
	 */
	public function log( $message ) {
		error_log( $message );
	}

	protected static $quiz_list;
	protected static $assignment_list;

	/**
	 *
	 * Reset Course Progress
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	protected static function reset_course_progress( $user_id, $course_id, $option ) {
		$user_id = intval( $user_id );
		$course_id = intval( $course_id );
		if ( '-1' !== $course_id ) {

			$course = new LLMS_Course( $course_id );
			$student_course_progress = $course->get_percent_complete( $user_id );
			$progress_data = self::get_progress_data( $course_id );
			$course_average_progress_current = $progress_data['progress'] / $progress_data['students'];
			$course_average_progress_updated = round( ( (intval(($course_average_progress_current) * ($progress_data['students'])) - intval($student_course_progress)) / intval($progress_data['students']) ), 2 );
			update_post_meta( $course_id, '_llms_average_progress', $course_average_progress_updated );

			$is_course_progress_deleted = self::delete_course_progress( $user_id, $course_id, $option );
			$is_quiz_progress_deleted = self::delete_quiz_progress( $user_id, $course_id );
			$is_user_activity_deleted = self::delete_user_activity( $user_id, $course_id, $option );
			// self::delete_assignments();
			if( $is_course_progress_deleted || $is_user_activity_deleted || $is_quiz_progress_deleted ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 *
	 * Delete course progress from Usermeta Table
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	protected static function delete_course_progress( $user_id, $course_id, $option ) {
		$usermeta = get_user_meta( $user_id, 'llms_course_'.$course_id.'_progress', true );
		if( isset( $usermeta ) && ! empty( $usermeta ) && $usermeta !== '0' && $usermeta != '' ) {
			$usermeta = 0;
			update_user_meta( $user_id, 'llms_course_'.$course_id.'_progress', $usermeta );
			if( isset($option) && !empty($option) ) {
				global $wpdb;
				$res = llms_unenroll_student( $user_id, $course_id, 'cancelled', 'any' );
				// $wpdb->query( "DELETE FROM {$wpdb->prefix}lifterlms_user_postmeta WHERE user_id = {$user_id}" );
				$res = llms_enroll_student( $user_id, $course_id, 'admin_' . get_current_user_id() );
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * Delete course related meta keys from user meta table and delete all activity related to a course.
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	protected static function delete_user_activity( $user_id, $course_id, $option ) {
		global $wpdb;
		$activity_ids = $wpdb->get_results( 'SELECT meta_id FROM ' . $wpdb->prefix . 'lifterlms_user_postmeta WHERE user_id = ' . $user_id . ' AND meta_key IN ("_is_complete", "_completion_trigger")');
		if ( $activity_ids ) {
			update_user_meta( $user_id, 'llms_overall_grade', 0 );
			update_user_meta( $user_id, 'llms_overall_progress', 0 );
			$LLMS_Course = new LLMS_Course( $course_id );
			$course_lessons = $LLMS_Course->get_lessons( 'ids' );
			if( isset( $course_lessons ) && !empty( $course_lessons ) ){
				foreach ( $course_lessons as $lesson ) {
					$wpdb->query( "DELETE FROM {$wpdb->prefix}lifterlms_user_postmeta WHERE meta_key IN ('_is_complete', '_completion_trigger') AND user_id = {$user_id} AND post_id IN ({$lesson}, {$course_id})" );
				}
			}
			$wpdb->query( "DELETE FROM {$wpdb->prefix}lifterlms_notifications WHERE user_id = {$user_id}" );
			if( isset($option) && !empty($option) ) {
				$current_datetime = current_time('mysql');
				$wpdb->query( "DELETE FROM {$wpdb->prefix}lifterlms_user_postmeta WHERE meta_key IN ('_email_sent') AND user_id = {$user_id} AND post_id = {$course_id}" );
				$wpdb->query( "UPDATE {$wpdb->prefix}lifterlms_user_postmeta SET updated_date = '{$current_datetime}' WHERE meta_key IN ('_start_date', '_status') AND user_id = {$user_id}" );
				$res = llms_unenroll_student( $user_id, $course_id, 'cancelled', 'any' );
				// $wpdb->query( "DELETE FROM {$wpdb->prefix}lifterlms_user_postmeta WHERE user_id = {$user_id}" );
				$res = llms_enroll_student( $user_id, $course_id, 'admin_' . get_current_user_id() );
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * Get lesson quiz list
	 * Delete quiz progress, related to course, quiz etc
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	protected static function delete_quiz_progress( $user_id, $course_id ) {
		global $wpdb;
		$LLMS_Course = new LLMS_Course( $course_id );
		$course_lessons = $LLMS_Course->get_lessons( 'ids' );
		$course_sections = $LLMS_Course->get_sections( 'ids' );
		$course_quizzes = $LLMS_Course->get_quizzes();

		foreach( $course_sections as $section_id ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}lifterlms_user_postmeta WHERE meta_key IN ('_is_complete', '_completion_trigger') AND user_id = {$user_id} AND post_id = {$section_id}" );
		}
		foreach( $course_quizzes as $quiz_id ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}lifterlms_user_postmeta WHERE meta_key IN ('_is_complete', '_completion_trigger') AND user_id = {$user_id} AND post_id = {$quiz_id}" );
		}
		$is_deleted = false;
		if( isset( $course_lessons ) && !empty( $course_lessons ) ){
			$quiz_attempts_table = $wpdb->prefix . 'lifterlms_quiz_attempts';

			foreach ( $course_lessons as $lesson ) {

				$quizzes = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM {$quiz_attempts_table} WHERE `student_id` = %d AND `lesson_id` = %d AND `attempt` = (SELECT MAX(`attempt`) FROM {$quiz_attempts_table} WHERE `student_id` = %d AND `lesson_id` = %d )",
					$user_id, $lesson, $user_id, $lesson )
					, 'ARRAY_A'
				);
				if( isset( $quizzes ) && !empty( $quizzes ) ){
					$course_average_grade_current = get_post_meta( $course_id, '_llms_average_grade' );
					$progress_data = self::get_progress_data( $course_id );
					$course_average_grade_updated = ($progress_data['quizzes'] == 1) ? 0 : round( $progress_data['grade'] / $progress_data['quizzes'], 2);
					update_post_meta( $course_id, '_llms_average_grade', $course_average_grade_updated );
				}

				$quiz_attempts = 0;
				$deleted_rows = $wpdb->delete( "{$wpdb->prefix}lifterlms_quiz_attempts", array( 'lesson_id' => $lesson, 'student_id' => $user_id ) );
				if( $deleted_rows > 0 && $deleted_rows != false ) {
					$is_deleted = true;
				}
			}
		}
		return $is_deleted;
	}

	/**
	 * Get all Course progress data for students
	 */
	protected static function get_progress_data( $course_id = 0 ) {
		// merge with the defaults
		$data = wp_parse_args(
			array(),
			array(
				'students' => 0,
				'progress' => 0,
				'quizzes'  => 0,
				'grade'    => 0,
			)
		);

		$query = new LLMS_Student_Query( array() );

		foreach ( $query->get_students() as $student ) {

			// progress, all students counted here
			$data['students']++;
			$data['progress'] = $data['progress'] + $student->get_progress( $course_id );

			// grades only counted when a student has taken a quiz
			// if a student hasn't taken it, we don't count it as a 0 on the quiz
			$grade = $student->get_grade( $course_id );

			// only check actual quiz grades
			if ( is_numeric( $grade ) ) {
				$data['quizzes']++;
				$data['grade'] = $data['grade'] + $grade;
			}
		}

		return $data;
	}

	/**
	 * Delete assignments of course, related to lessons / topics
	 */
	protected static function delete_assignments() {
		global $wpdb;
		$assignments = self::$assignment_list;
		if ( $assignments ) {
			foreach ( $assignments as $assignment ) {
				$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE ID = {$assignment}" );
				$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id = {$assignment}" );
			}
		}
	}

}