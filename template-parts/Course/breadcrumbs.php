<?php
if (!defined('ABSPATH')) exit;

$course_id   = $args['course_id'] ?? get_the_ID();
$courses_url = get_post_type_archive_link('sfwd-courses');
// Fallback if the archive isn't registered to a public URL.
if (!$courses_url) $courses_url = home_url('/courses/');

// Use Franklin sprite path you provided. Change if your theme hosts it elsewhere.
$sprite_path = '/static/svg/sprite.svg';
?>
<nav class="c-breadcrumb" aria-label="Breadcrumb">
  <ol class="c-breadcrumb__list" role="list">
    <li class="c-breadcrumb__item" role="listitem">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="c-breadcrumb__link">Academy home</a>
      <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--chevron-down">
        <use xlink:href="<?php echo esc_attr($sprite_path); ?>#chevron-down"></use>
      </svg>
    </li>
    <li class="c-breadcrumb__item" role="listitem">
      <a href="<?php echo esc_url($courses_url); ?>" class="c-breadcrumb__link">Courses</a>
      <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--chevron-down">
        <use xlink:href="<?php echo esc_attr($sprite_path); ?>#chevron-down"></use>
      </svg>
    </li>
    <li class="c-breadcrumb__item" role="listitem">
      <span class="c-breadcrumb__current" aria-current="page">
        <?php echo esc_html(get_the_title($course_id)); ?>
      </span>
    </li>
  </ol>
</nav>
