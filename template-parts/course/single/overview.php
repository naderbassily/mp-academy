<?php
/**
 * Template Part: Course Overview
 * Displays the main course content
 * 
 * Location: template-parts/course/single/overview.php
 */

if (!defined('ABSPATH')) exit;

$course_id = (int) ($args['course_id'] ?? get_the_ID());

// Get course content
$course_post = get_post($course_id);
$content = $course_post ? apply_filters('the_content', $course_post->post_content) : '';

if (empty($content)) {
	return;
}
?>

<section class="mp-course-overview">
	<div class="mp-course-overview__content o-prose u-flow--prose">
		<?php echo $content; ?>
	</div>
</section>
