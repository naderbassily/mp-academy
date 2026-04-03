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
 * Enqueue universal progress bar CSS globally
 * Used in: My Courses (home), Single Course, etc.
 * 
 * ⚠️ IMPORTANT: This MUST come BEFORE mp_enqueue_single_course_assets
 * because single-course.css depends on it!
 */
function mp_enqueue_progress_bar_styles() {
  wp_enqueue_style(
    'mp-progress-bar',
    get_template_directory_uri() . '/assets/css/progress-bar.css',
    [],
    '1.0.0'
  );
}
add_action('wp_enqueue_scripts', 'mp_enqueue_progress_bar_styles');

/**
 * Enqueue assets on Single Course (sfwd-courses) page.
 */
function mp_enqueue_single_course_assets() {
  if (!is_singular('sfwd-courses')) {
    return;
  }

  // CSS (depends on progress-bar.css which loads globally)
  wp_enqueue_style(
    'mp-single-course',
    get_template_directory_uri() . '/assets/css/single-course.css',
    ['mp-progress-bar'],  // Depends on progress bar styles
    '1.0.0'
  );

  // JS (Accordion functionality)
  wp_enqueue_script(
    'mp-single-course-js',
    get_template_directory_uri() . '/assets/js/single-course.js',
    [],
    '1.0.0',
    true
  );
}
add_action('wp_enqueue_scripts', 'mp_enqueue_single_course_assets');