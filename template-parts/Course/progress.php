<?php
if (!defined('ABSPATH')) exit;

$course_id = (int) ($args['course_id'] ?? get_the_ID());
$user_id   = (int) ($args['user_id']   ?? get_current_user_id());

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
?>
<div class="mp-course__progress u-margin-bottom-s">
  <div class="mp-course__progress-meta">
    <span class="mp-course__progress-left"><?php echo esc_html($progress_pct); ?>% complete</span>
    <?php if ($last_activity_str): ?>
      <span class="mp-course__progress-right">Last activity: <?php echo esc_html($last_activity_str); ?></span>
    <?php endif; ?>
  </div>
  <div class="c-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr($progress_pct); ?>">
    <span class="c-progress__bar" style="width: <?php echo esc_attr($progress_pct); ?>%"></span>
  </div>
</div>
