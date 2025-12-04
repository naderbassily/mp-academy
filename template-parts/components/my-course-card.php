<?php
/**
 * Component: MP Academy — Course Card (Franklin layout, button-only clickable)
 * Expects: $args['course_id'] (int)
 */
if (!defined('ABSPATH')) exit;

$course_id = isset($args['course_id']) ? (int)$args['course_id'] : 0;
if (!$course_id) return;

$user_id   = get_current_user_id();
$permalink = get_permalink($course_id);
$title     = get_the_title($course_id);

// Featured image (fallback)
$thumb = get_the_post_thumbnail($course_id, 'large', [
  'class'   => '',
  'loading' => 'lazy',
  'alt'     => esc_attr($title),
]);
if (!$thumb) {
  $thumb = '<div class="mp-course-card__img mp-course-card__img--ph" aria-hidden="true"></div>';
}

// Category (LearnDash course category)
$cat_label = '';
$terms = get_the_terms($course_id, 'ld_course_category');
if ($terms && !is_wp_error($terms) && !empty($terms)) {
  $cat_label = esc_html($terms[0]->name);
}

// Progress %
$progress_pct = 0;
if ($user_id && function_exists('learndash_course_progress')) {
  $prog = learndash_course_progress([
    'user_id'   => $user_id,
    'course_id' => $course_id,
    'array'     => true,
  ]);
  if (is_array($prog) && isset($prog['percentage'])) {
    $progress_pct = $prog['percentage'];
    if ($progress_pct > 0 && $progress_pct <= 1) $progress_pct = round($progress_pct * 100);
    $progress_pct = (int) max(0, min(100, $progress_pct));
  }
}

// Last activity (robust)
$last_activity_str = '';
if ($user_id && function_exists('learndash_get_user_activity')) {
  $act = learndash_get_user_activity([
    'user_id'        => $user_id,
    'course_id'      => $course_id,
    'post_id'        => $course_id,
    'activity_type'  => 'course',
    'per_page'       => 1,
    'orderby'        => 'activity_updated',
    'order'          => 'DESC',
  ]);

  $updated_raw = '';
  if (is_array($act) && !empty($act))  $updated_raw = $act[0]->activity_updated ?? '';
  elseif (is_object($act))             $updated_raw = $act->activity_updated ?? '';

  if ($updated_raw) {
    if (is_numeric($updated_raw)) {
      $ts = (int) $updated_raw;
      if ($ts > 1000000000000) $ts = (int) round($ts / 1000); // ms → s
    } else {
      $ts = strtotime($updated_raw);
    }
    if (!empty($ts) && $ts > 0) $last_activity_str = date_i18n(get_option('date_format'), $ts);
  }
}

// Continue / resume URL
$continue_url = $permalink;
if ($user_id && function_exists('learndash_user_course_resume_url')) {
  $resume = learndash_user_course_resume_url($user_id, $course_id);
  if (!empty($resume)) $continue_url = $resume;
}

$btn_label = __('Continue course', 'mp-academy');
?>

<article class="mp c-card c-card--layout-single c-card--size-small c-card--alt c-card--has-image">
  <div class="c-card__wrapper">
    <figure class="c-card__image">
      <!-- Image is NOT a link -->
      <?php echo $thumb; ?>
    </figure>

    <div class="c-card__primary">
      <header class="c-card__header u-flow--2xs">
        <?php if ($cat_label): ?>
          <p class="mp-course-card__kicker"><?php echo esc_html($cat_label); ?></p>
        <?php endif; ?>

        <!-- Title is NOT a link -->
        <h2 class="c-h c-card__title"><?php echo esc_html($title); ?></h2>
      </header>

      <div class="c-card__content u-flow">
        <div class="c-progress" role="progressbar"
             aria-valuenow="<?php echo (int)$progress_pct; ?>" aria-valuemin="0" aria-valuemax="100"
             title="<?php echo esc_attr($progress_pct); ?>%">
          <div class="c-progress__bar" style="width: <?php echo (int)$progress_pct; ?>%;"></div>
        </div>

        <div class="mp-course-card__stats">
          <span class="mp-course-card__stat-left">
            <strong><?php echo (int)$progress_pct; ?>%</strong>&nbsp;<?php esc_html_e('complete', 'mp-academy'); ?>
          </span>
          <?php if ($last_activity_str): ?>
            <span class="mp-course-card__stat-right">
              <?php esc_html_e('Last activity:', 'mp-academy'); ?>&nbsp;<?php echo esc_html($last_activity_str); ?>
            </span>
          <?php endif; ?>
        </div>
      </div>

      <footer class="c-card__footer u-flow--2xs">
        <div>
          <!-- Only this button is clickable -->
          <a href="<?php echo esc_url($continue_url); ?>" class="mp c-button c-button--inline c-button--green">
            <?php echo esc_html($btn_label); ?>
          </a>
        </div>
      </footer>
    </div>

    <!-- Removed the full-card overlay link -->
  </div>
</article>
