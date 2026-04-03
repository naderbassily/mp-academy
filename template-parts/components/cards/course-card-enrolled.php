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

// Get progress for button logic (we need this for button text)
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

// Continue / resume URL
$continue_url = $permalink;
if ($user_id && function_exists('learndash_user_course_resume_url')) {
  $resume = learndash_user_course_resume_url($user_id, $course_id);
  if (!empty($resume)) $continue_url = $resume;
}

// Determine button text and class based on completion
$is_completed = ($progress_pct >= 100);
$btn_label = $is_completed 
  ? __('Restart course', 'mp-academy') 
  : __('Continue course', 'mp-academy');
$btn_class = $is_completed 
  ? 'mp c-button c-button--inline c-button--outline-green'
  : 'mp c-button c-button--inline c-button--green';
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
          <p class="u-step--1 u-petrol-step-1"  ><?php echo esc_html($cat_label); ?></p>

        <?php endif; ?>

        <!-- Title is NOT a link -->
        <h2 class="c-h c-card__title"><?php echo esc_html($title); ?></h2>
      </header>

      <div class="c-card__content u-flow">
        <?php 
        // Use unified progress component
        get_template_part('template-parts/components/progress-bar', null, [
          'course_id' => $course_id,
          'user_id'   => $user_id,
          'context'   => 'card',
          'show_text' => true,
          'show_date' => true,
          'show_bar'  => true,
        ]); 
        ?>
      </div>

      <footer class="c-card__footer u-flow--2xs">
        <div>
          <a href="<?php echo esc_url($continue_url); ?>" class="<?php echo esc_attr($btn_class); ?>">
            <?php echo esc_html($btn_label); ?>
          </a>
        </div>
      </footer>
    </div>

    <!-- Removed the full-card overlay link -->
  </div>
</article>