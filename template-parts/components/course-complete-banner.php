<?php
/**
 * Component: Course Complete Banner
 * Displays congratulations and a certificate download link if the user has completed the course.
 *
 * Args:
 * - course_id (int) Required
 * - user_id   (int) Required
 */

if (!defined('ABSPATH')) exit;

$course_id = isset($args['course_id']) ? (int) $args['course_id'] : get_the_ID();
$user_id   = isset($args['user_id']) ? (int) $args['user_id'] : get_current_user_id();

if (!$course_id || !$user_id) return;

$progress_pct = 0;
$is_completed = false;

if (function_exists('learndash_course_progress')) {
  $prog = learndash_course_progress([
    'user_id'   => $user_id,
    'course_id' => $course_id,
    'array'     => true,
  ]);
  if (is_array($prog) && isset($prog['percentage'])) {
    $progress_pct = $prog['percentage'];
    if ($progress_pct > 0 && $progress_pct <= 1) {
      $progress_pct = $progress_pct * 100;
    }
  }
}

if ($progress_pct >= 100 || (function_exists('learndash_course_completed') && learndash_course_completed($user_id, $course_id))) {
  $is_completed = true;
}

if ($is_completed) :
  $certificate_link = '';
  if (function_exists('learndash_get_course_certificate_link')) {
    $certificate_link = learndash_get_course_certificate_link($course_id, $user_id);
  }
?>
  <div class="mp-course-complete-banner u-margin-top-l u-margin-bottom-l">
    <p class="mp-course-complete-banner__eyebrow">Course completed</p>
    <h2 class="c-h mp-course-complete-banner__title">Congratulations, you finished <?php echo esc_html(get_the_title($course_id)); ?>!</h2>
    <p class="c-text">Your course has been marked as complete.</p>

    <?php if (!empty($certificate_link)) : ?>
      <p class="mp-course-complete-banner__actions">
        <a class="mp-profile-certificate__link" href="<?php echo esc_url($certificate_link); ?>" target="_blank" rel="noopener noreferrer">
          Download Certificate
        </a>
      </p>
    <?php endif; ?>
  </div>

  <style>
  .mp-course-complete-banner {
    padding: 1.5rem;
    border: 1px solid #d8e8eb;
    border-radius: 1.25rem;
    background: linear-gradient(135deg, #f4fbfc 0%, #eef7f8 100%);
  }
  .mp-course-complete-banner__eyebrow {
    margin: 0 0 0.5rem;
    color: #005461;
    font-size: 0.8125rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }
  .mp-course-complete-banner__title {
    margin-bottom: 0.5rem;
  }
  .mp-course-complete-banner__actions {
    margin: 1rem 0 0;
  }
  .mp-profile-certificate__link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2.75rem;
    padding: 0.625rem 1rem;
    border-radius: 999px;
    background: #005461;
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    position: relative;
    z-index: 2;
  }
  .mp-profile-certificate__link:hover,
  .mp-profile-certificate__link:focus {
    background: #003f48;
    color: #fff;
  }
  </style>
<?php endif; ?>
