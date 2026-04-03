<?php
/**
 * Component: Breadcrumbs
 * Franklin Design System
 *
 * Location: template-parts/components/breadcrumbs.php
 */

if (!defined('ABSPATH')) exit;

if ( ! function_exists( 'mp_bc_chevron' ) ) {
	function mp_bc_chevron( $sprite_path ) {
		?>
		<svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--chevron-down">
			<use xlink:href="<?php echo esc_attr( $sprite_path ); ?>#chevron-down"></use>
		</svg>
		<?php
	}
}

$sprite_path = '/static/svg/sprite.svg';
$extra_class = !empty($args['extra_class']) ? ' ' . trim((string) $args['extra_class']) : '';

// Base links
$home_url = home_url('/');
$courses_url = get_post_type_archive_link('sfwd-courses') ?: home_url('/courses/');

// Context
$is_courses_archive = is_post_type_archive('sfwd-courses');
$is_single_course   = is_singular('sfwd-courses');
$is_single_lesson   = is_singular('sfwd-lessons');
$is_single_topic    = is_singular('sfwd-topic');

$is_learndash_context = ($is_courses_archive || $is_single_course || $is_single_lesson || $is_single_topic);

$post_id = get_the_ID();

// Detect Video Library page (works whether it's a Page Template or a normal page)
$is_video_library = false;
if (is_page()) {
  $tpl = get_page_template_slug($post_id);
  if ($tpl === 'page-videos-library.php' || (function_exists('is_page') && is_page('videos-library'))) {
    $is_video_library = true;
  }
}

// Resolve LearnDash IDs (only if needed)
$course_id = 0;
$lesson_id = 0;
$topic_id  = 0;

if ($is_single_course) {
  $course_id = $post_id;
} elseif ($is_single_lesson) {
  $lesson_id = $post_id;
} elseif ($is_single_topic) {
  $topic_id = $post_id;
}

if (!$course_id && ($is_single_lesson || $is_single_topic) && function_exists('learndash_get_course_id')) {
  $course_id = (int) learndash_get_course_id($post_id);
}

if ($is_single_topic && function_exists('learndash_get_setting')) {
  $lesson_id = (int) learndash_get_setting($topic_id, 'lesson');
}

$course_title = $course_id ? get_the_title($course_id) : '';
$course_link  = $course_id ? get_permalink($course_id) : '';

$lesson_title = $lesson_id ? get_the_title($lesson_id) : '';
$lesson_link  = $lesson_id ? get_permalink($lesson_id) : '';

$topic_title  = $topic_id ? get_the_title($topic_id) : '';
?>

<nav class="c-breadcrumb<?php echo esc_attr($extra_class); ?>" aria-label="Breadcrumb">
  <ol class="c-breadcrumb__list" role="list">
    <!-- Home -->
    <li class="c-breadcrumb__item" role="listitem">
      <a href="<?php echo esc_url($home_url); ?>" class="c-breadcrumb__link" title="<?php esc_attr_e('Home', 'mp-academy'); ?>">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px; transform: none !important; vertical-align: middle; display: inline-block;">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
      </a>

      <?php
      // Chevron shows if there is anything after Home
      if ($is_learndash_context || $is_video_library || (is_singular() || is_page())) {
        mp_bc_chevron($sprite_path);
      }
      ?>
    </li>

    <?php if ($is_learndash_context): ?>
      <!-- Courses (ONLY for LearnDash context) -->
      <li class="c-breadcrumb__item" role="listitem">
        <?php if ($is_courses_archive) : ?>
          <span class="c-breadcrumb__current" aria-current="page">
            <?php esc_html_e('Courses', 'mp-academy'); ?>
          </span>
        <?php else : ?>
          <a href="<?php echo esc_url($courses_url); ?>" class="c-breadcrumb__link">
            <?php esc_html_e('Courses', 'mp-academy'); ?>
          </a>

          <?php
          // Show chevron if more crumbs follow
          if ($is_single_course || $is_single_lesson || $is_single_topic) {
            mp_bc_chevron($sprite_path);
          }
          ?>
        <?php endif; ?>
      </li>

      <!-- Course / Lesson / Topic chain -->
      <?php if ($is_single_course && $course_id): ?>
        <li class="c-breadcrumb__item" role="listitem">
          <span class="c-breadcrumb__current" aria-current="page">
            <?php echo esc_html($course_title); ?>
          </span>
        </li>
      <?php endif; ?>

      <?php if ($is_single_lesson && $course_id): ?>
        <li class="c-breadcrumb__item" role="listitem">
          <a href="<?php echo esc_url($course_link); ?>" class="c-breadcrumb__link">
            <?php echo esc_html($course_title); ?>
          </a>
          <?php mp_bc_chevron($sprite_path); ?>
        </li>

        <li class="c-breadcrumb__item" role="listitem">
          <span class="c-breadcrumb__current" aria-current="page">
            <?php echo esc_html($lesson_title ?: get_the_title($post_id)); ?>
          </span>
        </li>
      <?php endif; ?>

      <?php if ($is_single_topic && $course_id): ?>
        <li class="c-breadcrumb__item" role="listitem">
          <a href="<?php echo esc_url($course_link); ?>" class="c-breadcrumb__link">
            <?php echo esc_html($course_title); ?>
          </a>
          <?php mp_bc_chevron($sprite_path); ?>
        </li>

        <?php if ($lesson_id): ?>
          <li class="c-breadcrumb__item" role="listitem">
            <a href="<?php echo esc_url($lesson_link); ?>" class="c-breadcrumb__link">
              <?php echo esc_html($lesson_title); ?>
            </a>
            <?php mp_bc_chevron($sprite_path); ?>
          </li>
        <?php endif; ?>

        <li class="c-breadcrumb__item" role="listitem">
          <span class="c-breadcrumb__current" aria-current="page">
            <?php echo esc_html($topic_title ?: get_the_title($post_id)); ?>
          </span>
        </li>
      <?php endif; ?>

    <?php elseif ($is_video_library): ?>

      <!-- Video Library (NON-courses page) -->
      <li class="c-breadcrumb__item" role="listitem">
        <span class="c-breadcrumb__current" aria-current="page">
          <?php esc_html_e('Video Library', 'mp-academy'); ?>
        </span>
      </li>

    <?php else: ?>

      <!-- Default: Home > Current page/post -->
      <li class="c-breadcrumb__item" role="listitem">
        <span class="c-breadcrumb__current" aria-current="page">
          <?php echo esc_html(get_the_title($post_id)); ?>
        </span>
      </li>

    <?php endif; ?>

  </ol>
</nav>
