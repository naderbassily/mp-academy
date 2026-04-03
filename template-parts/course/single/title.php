<?php
/**
 * Template Part: Course Title + Description
 * Location: template-parts/course/single/title.php
 */

if (!defined('ABSPATH')) exit;

$course_id = $args['course_id'] ?? get_the_ID();

// Get short description
$short_desc = get_post_meta($course_id, '_learndash_course_grid_short_description', true);
if (empty($short_desc)) {
	$short_desc = has_excerpt($course_id) ? get_the_excerpt($course_id) : '';
}
?>

<header class="mp-course-header u-margin-bottom-l">
	<h1 class="c-h c-h--step-6  u-margin-bottom-s ">
		<?php echo esc_html(get_the_title($course_id)); ?>
	</h1>
	
	<?php if (!empty($short_desc)) : ?>
		<p class="mp-course-header__desc u-step-0">
			<?php echo esc_html($short_desc); ?>
		</p>
	<?php endif; ?>
</header>