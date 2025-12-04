<?php
if (!defined('ABSPATH')) exit;

$course_id = $args['course_id'] ?? get_the_ID();

// Try common LearnDash Course Grid short description keys.
// You can change $keys order or add your exact key later.
$keys = [
  '_learndash_course_grid_short_description',
  'learndash_course_grid_short_description',
  'course_grid_short_description',
  'short_description',
];

$short_desc = '';
foreach ($keys as $k) {
  $val = get_post_meta($course_id, $k, true);
  if (!empty($val)) { $short_desc = $val; break; }
}

// Fallbacks: excerpt, or empty.
if (!$short_desc) {
  if (has_excerpt($course_id)) {
    $short_desc = get_the_excerpt($course_id);
  }
}

// Render
?>
<header class="mp-course__header u-margin-bottom-m">
  <h1 class="c-h"><?php echo esc_html(get_the_title($course_id)); ?></h1>

  <?php if (!empty($short_desc)) : ?>
    <p class="u-step--1 u-margin-top-2xs course-short-desc" >
        
      <?php echo wp_kses_post($short_desc); ?>
    </p>
  <?php endif; ?>
</header>
