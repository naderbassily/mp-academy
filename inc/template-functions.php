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
 * Whether the course enforces linear lesson progression.
 *
 * @param int $course_id Course ID.
 * @return bool
 */
function mp_ld_progression_enabled( $course_id ) {
  if ( ! $course_id || ! function_exists( 'learndash_lesson_progression_enabled' ) ) {
    return true;
  }

  return (bool) learndash_lesson_progression_enabled( $course_id );
}

/**
 * Whether the current user can bypass course progression rules.
 *
 * @param int $user_id User ID.
 * @return bool
 */
function mp_ld_user_can_bypass_progression( $user_id ) {
  if ( ! $user_id || ! function_exists( 'learndash_can_user_bypass' ) ) {
    return false;
  }

  return (bool) learndash_can_user_bypass( $user_id, 'learndash_course_progression' );
}

/**
 * Get ordered lesson IDs for a course.
 *
 * @param int $course_id Course ID.
 * @param int $user_id   User ID.
 * @return int[]
 */
function mp_ld_get_course_lesson_ids( $course_id, $user_id = 0 ) {
  if ( ! $course_id || ! function_exists( 'learndash_get_course_lessons_list' ) ) {
    return array();
  }

  $lessons = array();
  $raw     = learndash_get_course_lessons_list( $course_id, $user_id );

  if ( ! is_array( $raw ) ) {
    return $lessons;
  }

  foreach ( $raw as $row ) {
    $lesson = is_object( $row ) ? $row : ( $row['post'] ?? null );

    if ( $lesson && ! empty( $lesson->ID ) ) {
      $lessons[] = (int) $lesson->ID;
    }
  }

  return array_values( array_unique( array_filter( $lessons ) ) );
}

/**
 * Get ordered topic IDs for a lesson.
 *
 * @param int $lesson_id Lesson ID.
 * @param int $course_id Course ID.
 * @return int[]
 */
function mp_ld_get_lesson_topic_ids( $lesson_id, $course_id ) {
  if ( ! $lesson_id || ! $course_id || ! function_exists( 'learndash_get_topic_list' ) ) {
    return array();
  }

  $topic_ids = array();
  $topics    = learndash_get_topic_list( $lesson_id, $course_id );

  if ( ! is_array( $topics ) ) {
    return $topic_ids;
  }

  foreach ( $topics as $topic ) {
    $topic_id = is_object( $topic ) ? (int) $topic->ID : (int) $topic;

    if ( $topic_id > 0 ) {
      $topic_ids[] = $topic_id;
    }
  }

  return array_values( array_unique( $topic_ids ) );
}

/**
 * Get LearnDash video settings for a lesson or topic.
 *
 * @param int $step_id LearnDash step ID.
 * @return array<string,string>
 */
function mp_ld_get_step_video_settings( $step_id ) {
  $keys = array(
    'lesson_video_enabled'       => 'enabled',
    'lesson_video_url'           => 'url',
    'lesson_video_auto_start'    => 'autostart',
    'lesson_video_focus_pause'   => 'focus_pause',
    'lesson_video_track_video'   => 'resume',
    'lesson_video_show_controls' => 'controls',
    'lesson_video_auto_complete' => 'auto_complete',
  );

  $settings = array_fill_keys( array_values( $keys ), '' );

  if ( ! $step_id || ! function_exists( 'learndash_get_setting' ) ) {
    return $settings;
  }

  foreach ( $keys as $setting_key => $mapped_key ) {
    $settings[ $mapped_key ] = (string) learndash_get_setting( $step_id, $setting_key );
  }

  return $settings;
}

/**
 * Whether a LearnDash step has a configured video.
 *
 * @param int $step_id LearnDash step ID.
 * @return bool
 */
function mp_ld_step_has_video( $step_id ) {
  $settings = mp_ld_get_step_video_settings( $step_id );

  return 'on' === $settings['enabled'] && '' !== trim( $settings['url'] );
}

/**
 * Whether a lesson should behave like a standalone content step.
 *
 * A standalone lesson has no child topics and contains body content or a
 * LearnDash lesson video.
 *
 * @param int $lesson_id Lesson ID.
 * @param int $course_id Course ID.
 * @return bool
 */
function mp_ld_is_standalone_lesson( $lesson_id, $course_id = 0 ) {
  $lesson_id = (int) $lesson_id;
  $course_id = (int) $course_id;

  if ( ! $lesson_id ) {
    return false;
  }

  if ( ! $course_id && function_exists( 'learndash_get_course_id' ) ) {
    $course_id = (int) learndash_get_course_id( $lesson_id );
  }

  if ( ! empty( mp_ld_get_lesson_topic_ids( $lesson_id, $course_id ) ) ) {
    return false;
  }

  $raw_content = (string) get_post_field( 'post_content', $lesson_id );
  $has_content = '' !== trim( wp_strip_all_tags( strip_shortcodes( $raw_content ) ) );

  return $has_content || mp_ld_step_has_video( $lesson_id );
}

/**
 * Check whether a user has completed a LearnDash step.
 *
 * @param int    $user_id    User ID.
 * @param int    $course_id  Course ID.
 * @param int    $step_id    Step ID.
 * @param string $step_type  Step type.
 * @return bool
 */
function mp_ld_is_step_complete( $user_id, $course_id, $step_id, $step_type = 'lesson' ) {
  if ( ! $user_id || ! $course_id || ! $step_id ) {
    return false;
  }

  if ( function_exists( 'learndash_user_progress_is_step_complete' ) ) {
    return (bool) learndash_user_progress_is_step_complete( $user_id, $course_id, $step_id );
  }

  if ( 'topic' === $step_type && function_exists( 'learndash_is_topic_complete' ) ) {
    return (bool) learndash_is_topic_complete( $user_id, $step_id, $course_id );
  }

  if ( 'lesson' === $step_type && function_exists( 'learndash_is_lesson_complete' ) ) {
    return (bool) learndash_is_lesson_complete( $user_id, $step_id, $course_id );
  }

  return false;
}

/**
 * Whether a lesson is accessible in the course sequence.
 *
 * @param int        $user_id    User ID.
 * @param int        $course_id  Course ID.
 * @param int        $lesson_id  Lesson ID.
 * @param int[]|null $lesson_ids Ordered lesson IDs.
 * @return bool
 */
function mp_ld_can_access_lesson( $user_id, $course_id, $lesson_id, $lesson_ids = null ) {
  if ( ! $lesson_id || ! $course_id ) {
    return false;
  }

  if ( empty( $lesson_ids ) || ! is_array( $lesson_ids ) ) {
    $lesson_ids = mp_ld_get_course_lesson_ids( $course_id, $user_id );
  }

  $position = array_search( (int) $lesson_id, $lesson_ids, true );

  if ( false === $position ) {
    return true;
  }

  if ( 0 === $position ) {
    return true;
  }

  $previous_lesson_id = (int) $lesson_ids[ $position - 1 ];

  return mp_ld_is_step_complete( $user_id, $course_id, $previous_lesson_id, 'lesson' );
}

/**
 * Whether a topic is accessible in the lesson sequence.
 *
 * @param int        $user_id    User ID.
 * @param int        $course_id  Course ID.
 * @param int        $lesson_id  Lesson ID.
 * @param int        $topic_id   Topic ID.
 * @param int[]|null $topic_ids  Ordered topic IDs.
 * @return bool
 */
function mp_ld_can_access_topic( $user_id, $course_id, $lesson_id, $topic_id, $topic_ids = null ) {
  if ( ! $topic_id || ! $lesson_id || ! $course_id ) {
    return false;
  }

  if ( ! mp_ld_can_access_lesson( $user_id, $course_id, $lesson_id ) ) {
    return false;
  }

  if ( empty( $topic_ids ) || ! is_array( $topic_ids ) ) {
    $topic_ids = mp_ld_get_lesson_topic_ids( $lesson_id, $course_id );
  }

  $position = array_search( (int) $topic_id, $topic_ids, true );

  if ( false === $position ) {
    return true;
  }

  if ( 0 === $position ) {
    return true;
  }

  $previous_topic_id = (int) $topic_ids[ $position - 1 ];

  return mp_ld_is_step_complete( $user_id, $course_id, $previous_topic_id, 'topic' );
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

/**
 * Get the formatted last activity date for a LearnDash step.
 *
 * @param int    $user_id   Current user ID.
 * @param int    $course_id Course ID.
 * @param int    $step_id   Step ID.
 * @param string $step_type LearnDash activity type.
 * @return string
 */
function mp_academy_get_activity_date( $user_id, $course_id, $step_id, $step_type ) {
  if ( ! $user_id || ! function_exists( 'learndash_get_user_activity' ) ) {
    return '';
  }

  $activity = learndash_get_user_activity(
    array(
      'user_id'       => $user_id,
      'course_id'     => $course_id,
      'post_id'       => $step_id,
      'activity_type' => $step_type,
      'per_page'      => 1,
    )
  );

  if ( is_array( $activity ) ) {
    $activity = reset( $activity );
  }

  if ( ! is_object( $activity ) ) {
    return '';
  }

  $timestamp = 0;

  if ( ! empty( $activity->activity_updated ) ) {
    $timestamp = strtotime( (string) $activity->activity_updated );
  } elseif ( ! empty( $activity->activity_started ) ) {
    $timestamp = strtotime( (string) $activity->activity_started );
  }

  if ( ! $timestamp ) {
    return '';
  }

  return wp_date( get_option( 'date_format' ), $timestamp );
}
