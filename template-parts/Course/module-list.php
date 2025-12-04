<?php
/**
 * Course Module List (Figma-style)
 * Expects: $args['course_id'], $args['user_id']
 */
if (!defined('ABSPATH')) exit;

$course_id = (int) ($args['course_id'] ?? get_the_ID());
$user_id   = (int) ($args['user_id']   ?? get_current_user_id());

$is_logged_in = is_user_logged_in();
$is_enrolled  = ($is_logged_in && function_exists('learndash_is_user_enrolled'))
  ? learndash_is_user_enrolled($user_id, $course_id)
  : false;

/**
 * Get lessons (top-level modules)
 */
$lessons = [];
if ( function_exists('learndash_get_course_lessons_list') ) {
  $raw = learndash_get_course_lessons_list($course_id, $user_id);
  if (is_array($raw)) {
    foreach ($raw as $row) {
      $lessons[] = (is_object($row) ? $row : ($row['post'] ?? null));
    }
    $lessons = array_filter($lessons);
  }
}
if (empty($lessons)) {
  $lessons = get_posts([
    'post_type'   => 'sfwd-lessons',
    'numberposts' => -1,
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
    'meta_query'  => [
      [ 'key' => 'course_id', 'value' => $course_id ],
    ],
  ]);
}

/**
 * Helper: status per lesson (module)
 */
function mp_course_lesson_status($user_id, $course_id, $lesson_id) {
  $out = ['complete'=>false,'started'=>false,'can_view'=>false];

  if (function_exists('learndash_can_user_view_course_step')) {
    $out['can_view'] = (bool) learndash_can_user_view_course_step($user_id, $course_id, $lesson_id);
  } else {
    $out['can_view'] = true; // fallback
  }

  if ($user_id && function_exists('learndash_is_lesson_complete')) {
    $out['complete'] = (bool) learndash_is_lesson_complete($user_id, $lesson_id, $course_id);
  }

  if (!$out['complete'] && $user_id && function_exists('learndash_get_user_activity')) {
    $act = learndash_get_user_activity([
      'user_id'       => $user_id,
      'course_id'     => $course_id,
      'post_id'       => $lesson_id,
      'activity_type' => 'lesson',
      'per_page'      => 1,
    ]);
    if ( (is_array($act) && !empty($act)) || is_object($act) ) {
      $out['started'] = true;
    }
  }
  return $out;
}

?>
<section class="mp-course__content u-margin-top-m">
  <?php if (!$is_logged_in) : ?>
    <p class="u-text-quiet u-margin-bottom-xs">Log in to access this course</p>
  <?php endif; ?>

  <?php if (empty($lessons)) : ?>
    <p>No modules found.</p>
    <?php return; ?>
  <?php endif; ?>

  <div class="c-module-list">
    <?php
    $i = 0;
    foreach ($lessons as $lesson) :
      $i++;
      $lesson_id   = is_object($lesson) ? $lesson->ID : (int) $lesson;
      $lesson_link = get_permalink($lesson_id);

      $st = mp_course_lesson_status($user_id, $course_id, $lesson_id);
      $classes = ['c-module-item', 'u-bg-petrol-step-3'];
      if ($st['complete'])        $classes[] = 'c-module-item--complete';
      elseif ($st['started'])     $classes[] = 'c-module-item--inprogress';

      // CTA: first module, enrolled, not started
      $show_start_cta = ($is_enrolled && !$st['complete'] && !$st['started'] && $i === 1);
      ?>
      <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
        <span class="c-module-item__dot" aria-hidden="true"></span>

        <div class="c-module-item__title">
          <?php
          // Link when user can view step OR when course is open
          if ($st['can_view']) :
          ?>
            <a class="u-link-reset" href="<?php echo esc_url($lesson_link); ?>">
              <?php echo esc_html(get_the_title($lesson_id)); ?>
            </a>
          <?php else : ?>
            <?php echo esc_html(get_the_title($lesson_id)); ?>
          <?php endif; ?>
        </div>

        <div class="c-module-item__meta">
          <?php if ($st['complete']) : ?>
            <span class="c-badge c-badge--complete">Complete</span>
          <?php elseif ($st['started']) : ?>
            <span class="c-badge c-badge--progress">In progress</span>
          <?php elseif ($show_start_cta) : ?>
            <a class="c-btn-start" href="<?php echo esc_url($lesson_link); ?>">Start module</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
