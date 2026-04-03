<?php
/**
 * Template Functions
 * 
 * @package MP_Academy
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Normalize LearnDash course status to one of:
 * 'in-progress', 'completed', 'not-started', 'not-logged-in'
 */
function mp_ld_course_status_key($course_id, $user_id) {
  if (!$user_id || !function_exists('learndash_course_status')) {
    return 'not-logged-in';
  }

  $status_html = learndash_course_status($course_id, $user_id);
  $status_text = strtolower(wp_strip_all_tags($status_html));

  if (strpos($status_text, 'in progress') !== false) {
    return 'in-progress';
  }

  if (strpos($status_text, 'completed') !== false) {
    return 'completed';
  }

  return 'not-started';
}

/**
 * Check if a user is enrolled in a LearnDash course.
 */
function mp_ld_is_enrolled($user_id, $course_id) {
  if (!$user_id) {
    return false;
  }

  if (function_exists('ld_course_check_if_enrolled')) {
    return (bool) ld_course_check_if_enrolled($user_id, $course_id);
  }

  if (function_exists('sfwd_lms_has_access')) {
    return (bool) sfwd_lms_has_access($course_id, $user_id);
  }

  return false;
}

/**
 * Get LearnDash lesson or topic status for the current user.
 *
 * @param int    $user_id   Current user ID.
 * @param int    $course_id Course ID.
 * @param int    $step_id   Lesson or topic ID.
 * @param string $step_type LearnDash activity type.
 * @return array{complete:bool,started:bool,can_view:bool}
 */
function mp_get_step_status( $user_id, $course_id, $step_id, $step_type = 'lesson' ) {
  $status = array(
    'complete' => false,
    'started'  => false,
    'can_view' => true,
  );

  if ( 'lesson' === $step_type ) {
    if ( $user_id && function_exists( 'learndash_is_lesson_complete' ) ) {
      $status['complete'] = (bool) learndash_is_lesson_complete( $user_id, $step_id, $course_id );
    }
  } elseif ( $user_id && function_exists( 'learndash_is_topic_complete' ) ) {
    $status['complete'] = (bool) learndash_is_topic_complete( $user_id, $step_id );
  }

  if ( ! $status['complete'] && $user_id && function_exists( 'learndash_get_user_activity' ) ) {
    $activity = learndash_get_user_activity(
      array(
        'user_id'       => $user_id,
        'course_id'     => $course_id,
        'post_id'       => $step_id,
        'activity_type' => $step_type,
        'per_page'      => 1,
      )
    );

    if ( ( is_array( $activity ) && ! empty( $activity ) ) || is_object( $activity ) ) {
      $status['started'] = true;
    }
  }

  return $status;
}
