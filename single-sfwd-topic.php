<?php
get_header();

$topic_id = get_the_ID();
$user_id = get_current_user_id();

$lesson_id = function_exists('learndash_get_setting') ? (int)learndash_get_setting($topic_id, 'lesson') : 0;
$course_id = function_exists('learndash_get_course_id') ? (int)learndash_get_course_id($topic_id) : 0;

$course_url = $course_id ? get_permalink($course_id) : '';

/**
 * Status pill
 */
$is_completed = false;
if (function_exists('learndash_is_topic_complete')) {
  $is_completed = $course_id ? (bool)learndash_is_topic_complete($user_id, $topic_id, $course_id) : (bool)learndash_is_topic_complete($user_id, $topic_id);
}
elseif (function_exists('learndash_is_item_complete')) {
  $is_completed = (bool)learndash_is_item_complete($user_id, $topic_id, $course_id);
}

/**
 * Prev/Next step
 */
$prev_id = function_exists('learndash_get_previous_step_id') ? (int)learndash_get_previous_step_id($course_id, $topic_id) : 0;
$next_id = function_exists('learndash_get_next_step_id') ? (int)learndash_get_next_step_id($course_id, $topic_id) : 0;

$prev_url = $prev_id ? get_permalink($prev_id) : '';
$next_url = $next_id ? get_permalink($next_id) : '';

/**
 * Strip breadcrumbs and progress bars (navigation kept for Mark Complete & CSS styling)
 */
if (!function_exists('mp_strip_learndash_ui_blocks')) {
  function mp_strip_learndash_ui_blocks($html)
  {
    if (!$html)
      return $html;

    // Strip breadcrumbs and progress using basic regex 
    // Note: Regex on HTML can be fragile, CSS display:none is safer long term.
    $html = preg_replace('/<([a-z0-9]+)\b[^>]*class="[^"]*\bld-breadcrumbs\b[^"]*"[^>]*>.*?<\/\1>/is', '', $html);
    $html = preg_replace('/<([a-z0-9]+)\b[^>]*class="[^"]*\bld-progress\b[^"]*"[^>]*>.*?<\/\1>/is', '', $html);

    return $html;
  }
}

/**
 * Extract complete LearnDash video wrapper
 */
if (!function_exists('mp_extract_first_video')) {
  function mp_extract_first_video($content)
  {
    // Extracts the first div with class ld-video assuming a single inner wrapper
    $pattern = '/<div\s+class="ld-video"[^>]*>.*?<\/div>\s*<\/div>/is';

    if (preg_match($pattern, $content, $m)) {
      $video = $m[0];
      $rest = str_replace($video, '', $content);
      return [$video, $rest];
    }

    return ['', $content];
  }
}
?>

<?php
// Generic Small Hero Section
get_template_part('template-parts/lesson/single/hero', null, [
  'post_id' => $topic_id,
  'lesson_id' => $lesson_id,
  'course_id' => $course_id,
  'show_eyebrow' => true,
  'is_completed' => $is_completed,
]);
?>
<main id="primary" class="site-main u-wrap u-space--section u-flow u-margin-top-xl ">

  <header class="u-flow--s">

    <div class="u-display-flex u-flex-wrap u-gap-s u-align-items-center u-margin-top-xl u-justify-content-between">


      <span class="u-margin-left-auto"></span>

      <?php if ($prev_url): ?>
      <a href="<?php echo esc_url($prev_url); ?>" class="mp c-button c-button--outline-green">← Previous Topic</a>
      <?php
endif; ?>

      <?php if ($next_url): ?>
      <a href="<?php echo esc_url($next_url); ?>" class="mp c-button c-button--green">Next Topic →</a>
      <?php
endif; ?>
    </div>
  </header>

  <?php
if (have_posts()):
  while (have_posts()):
    the_post();

    $raw = apply_filters('the_content', get_the_content());
    $clean = mp_strip_learndash_ui_blocks($raw);

    $clean = preg_replace('/<div\b[^>]*id="ld-tab-panel-content"[^>]*>\s*<\/div>/is', '', $clean);

    list($video_block, $body_block) = mp_extract_first_video($clean);
?>

  <?php if (!empty($video_block)): ?>
  <section class="mp-topic-video-wrapper" data-ld-topic-id="<?php echo esc_attr($topic_id); ?>"
    data-ld-progression="<?php echo esc_attr(learndash_get_setting($topic_id, 'lesson_video_enabled')); ?>"
    data-ld-autostart="<?php echo esc_attr(learndash_get_setting($topic_id, 'lesson_video_auto_start')); ?>"
    data-ld-focus-pause="<?php echo esc_attr(learndash_get_setting($topic_id, 'lesson_video_focus_pause')); ?>"
    data-ld-resume="<?php echo esc_attr(learndash_get_setting($topic_id, 'lesson_video_track_video')); ?>"
    data-ld-controls="<?php echo esc_attr(learndash_get_setting($topic_id, 'lesson_video_show_controls')); ?>"
    data-ld-auto-complete="<?php echo esc_attr(learndash_get_setting($topic_id, 'lesson_video_auto_complete')); ?>">
    <?php echo $video_block; ?>
  </section>
  <?php
    endif; ?>

  <div class="u-wrap">
    <?php echo $body_block; ?>
  </div>

  <?php if ($course_url): ?>
  <div class="u-wrap u-space--section u-flow">
    <p><a href="<?php echo esc_url($course_url); ?>" class="u-blue u-text-weight-bold">← Back to course overview</a></p>
  </div>
  <?php
    endif; ?>

  <?php
  endwhile;
endif;
?>


</main>

<?php get_footer(); ?>