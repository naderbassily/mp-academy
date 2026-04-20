<?php
/**
 * Template part: Single Lesson / Topic Hero Section
 */

if (!defined('ABSPATH'))
  exit;

$current_id = (int)($args['post_id'] ?? get_the_ID());
$lesson_id  = (int)($args['lesson_id'] ?? 0);
$course_id  = (int)($args['course_id'] ?? 0);

$show_eyebrow   = !empty($args['show_eyebrow']);
$is_completed   = !empty($args['is_completed']);
$course_url     = isset($args['course_url']) ? (string) $args['course_url'] : '';
$step_type      = isset($args['step_type']) ? (string) $args['step_type'] : 'topic';
$step_status    = isset($args['step_status']) ? (string) $args['step_status'] : ($is_completed ? 'complete' : 'in-progress');
$topic_progress = is_array($args['topic_progress'] ?? null) ? $args['topic_progress'] : [];

$progress_percent = isset($topic_progress['percent']) ? (int) $topic_progress['percent'] : 0;
$progress_percent = max(0, min(100, $progress_percent));
$steps_done       = isset($topic_progress['steps_done']) ? (int) $topic_progress['steps_done'] : 0;
$steps_total      = isset($topic_progress['steps_total']) ? (int) $topic_progress['steps_total'] : 0;
$last_activity    = isset($topic_progress['last_activity']) ? (string) $topic_progress['last_activity'] : '';

$title   = get_the_title($current_id);
$excerpt = has_excerpt($current_id) ? get_the_excerpt($current_id) : '';

$hero_svg = 'https://dam.malvernpanalytical.com/fae4c741-f556-475a-b286-b36e0098eefe/website%20hero%20placeholder_Original%20file.svg';

// Status badge config
$status_labels = [
  'lesson' => [
    'complete'    => __('Lesson completed', 'mp-academy'),
    'in-progress' => __('Lesson in progress', 'mp-academy'),
    'not-started' => __('Not started', 'mp-academy'),
  ],
  'topic' => [
    'complete'    => __('Topic completed', 'mp-academy'),
    'in-progress' => __('Topic in progress', 'mp-academy'),
    'not-started' => __('Not started', 'mp-academy'),
  ],
];
$status_styles = [
  'complete'    => 'background-color:#d1fae5;color:#065f46;border:none;',
  'in-progress' => 'background-color:#bfdbfe;color:#1e3a8a;border:none;',
  'not-started' => 'background-color:#e5e7eb;color:#374151;border:none;',
];
$type_key    = isset($status_labels[$step_type]) ? $step_type : 'topic';
$badge_label = $status_labels[$type_key][$step_status] ?? $status_labels[$type_key]['in-progress'];
$badge_style = $status_styles[$step_status] ?? $status_styles['in-progress'];
?>

<section id="mp-custom-small-hero" class="c-hero c-hero--dark mp-small-hero"
  style="--placeholder-image: url('<?php echo esc_url($hero_svg); ?>')">
  <div class="c-hero__wrap">
    <div class="c-hero__main">
      <?php
      get_template_part('template-parts/components/breadcrumbs', null, [
        'course_id'   => $course_id,
        'lesson_id'   => $lesson_id,
        'extra_class' => 'c-breadcrumb--dark',
      ]);
      ?>

      <h1 class="c-hero__heading">
        <?php echo esc_html($title); ?>
      </h1>

      <?php if (!empty($excerpt)): ?>
        <p class="c-hero__lede u-margin-top-s">
          <?php echo esc_html($excerpt); ?>
        </p>
      <?php endif; ?>

      <?php if (!empty($topic_progress)): ?>
        <div class="mp-topic-hero-progress" aria-label="<?php esc_attr_e('Progress', 'mp-academy'); ?>">
          <div class="mp-topic-hero-progress__row">
            <span class="mp-topic-hero-progress__label"><?php esc_html_e('Progress', 'mp-academy'); ?></span>
            <strong class="mp-topic-hero-progress__percent"><?php echo esc_html($progress_percent); ?>%</strong>
            <div
              class="mp-topic-hero-progress__bar"
              role="progressbar"
              aria-valuenow="<?php echo esc_attr($progress_percent); ?>"
              aria-valuemin="0"
              aria-valuemax="100"
            >
              <span style="width: <?php echo esc_attr($progress_percent); ?>%;"></span>
            </div>
            <?php if ($steps_total > 0): ?>
              <span class="mp-topic-hero-progress__steps">
                <?php printf(esc_html__('%1$d/%2$d steps', 'mp-academy'), $steps_done, $steps_total); ?>
              </span>
            <?php endif; ?>
          </div>

          <?php if ($last_activity): ?>
            <p class="mp-topic-hero-progress__activity">
              <?php esc_html_e('Last activity:', 'mp-academy'); ?>
              <?php echo esc_html($last_activity); ?>
            </p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($show_eyebrow): ?>
        <div class="mp-topic-hero-status u-margin-top-m u-margin-bottom-xs">
          <span class="mp c-eyebrow" style="<?php echo esc_attr($badge_style); ?>">
            <?php echo esc_html($badge_label); ?>
          </span>
        </div>

        <?php if (!empty($course_url)): ?>
          <div class="mp-topic-hero-back-link-wrap">
            <a href="<?php echo esc_url($course_url); ?>" class="mp-topic-back-link">
              <?php esc_html_e('← Back to course overview', 'mp-academy'); ?>
            </a>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <div class="c-hero__media-wrap"></div>
  </div>
</section>
