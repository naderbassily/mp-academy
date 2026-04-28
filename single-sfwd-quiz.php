<?php
/**
 * Template: LearnDash Single Quiz
 * Franklin Design System
 *
 * @package MP_Academy
 */

if (!defined('ABSPATH')) exit;

get_header();

$quiz_id   = get_the_ID();
$user_id   = get_current_user_id();
$course_id = function_exists('learndash_get_course_id') ? (int) learndash_get_course_id($quiz_id) : 0;

// Pass score (dynamic from LearnDash settings)
$pass_score = 0;
if (function_exists('learndash_get_setting')) {
  $pass_score = (int) learndash_get_setting($quiz_id, 'passingpercentage');
}

// Last activity
$last_activity = '';

if ($user_id && function_exists('learndash_get_user_activity')) {
  $activity = learndash_get_user_activity([
    'user_id'       => $user_id,
    'course_id'     => $course_id,
    'post_id'       => $quiz_id,
    'activity_type' => 'quiz',
    'per_page'      => 1,
    'orderby'       => 'activity_updated',
    'order'         => 'DESC',
  ]);

  if (!empty($activity)) {
    $last = is_array($activity) ? reset($activity) : $activity;

    $updated_raw = !empty($last->activity_completed) ? $last->activity_completed : ($last->activity_updated ?? '');
    if ($updated_raw) {
      $timestamp = is_numeric($updated_raw) ? (int) $updated_raw : strtotime($updated_raw);
      if ($timestamp > 1000000000000) {
        $timestamp = (int) round($timestamp / 1000);
      }
      if ($timestamp) {
        $last_activity = date_i18n(get_option('date_format'), $timestamp);
      }
    }
  }
}

get_template_part('template-parts/quiz/single/hero', null, [
  'quiz_id'       => $quiz_id,
  'course_id'     => $course_id,
  'pass_score'    => $pass_score,
  'last_activity' => $last_activity,
]);
?>

<main id="primary" class="site-main u-wrap u-margin-top-xl u-margin-bottom-xl">
  <div class="u-wrap--content">

    <?php if ($pass_score > 0): ?>
      <div class="mp-quiz-intro">
        <p class="mp-quiz-intro__text">
          <?php
          printf(
            wp_kses(
              /* translators: %d: passing score percentage */
              __('Stay focused and take your time. You need a score of <strong>%d%%</strong> to pass.', 'mp-academy'),
              ['strong' => []]
            ),
            $pass_score
          );
          ?>
        </p>
      </div>
    <?php endif; ?>

    <?php
    // Render any custom content added in the backend
    $raw_content = get_post_field('post_content', $quiz_id);
    $prose = $raw_content ? wpautop(do_shortcode($raw_content)) : '';
    if ($prose): ?>
      <div class="mp-quiz-content u-margin-bottom-lg">
        <?php echo $prose; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    <?php endif; ?>

    <div class="mp-quiz-body">
      <?php echo do_shortcode('[ld_quiz quiz_id="' . $quiz_id . '"]'); ?>
    </div>

  </div>
</main>

<?php get_footer(); ?>
