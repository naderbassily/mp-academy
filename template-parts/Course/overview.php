<?php
/**
 * Course Overview block
 * Shows the main course content (visible to everyone).
 */
if (!defined('ABSPATH')) exit;

$course_id = (int) ($args['course_id'] ?? get_the_ID());

// Get the course content
$course_post = get_post($course_id);
$content     = $course_post ? apply_filters('the_content', $course_post->post_content) : '';

// Optional: If you later add ACF fields (e.g., duration/level/prerequisites), we’ll slot them in here.
?>
<section class="mp-course__overview">

  <?php if (!empty($content)) : ?>
<div class="mp-course__overview-body u-step--0"><?php echo $content; ?></div>
  <?php else : ?>
    <p class="u-text-quiet">Overview coming soon.</p>
  <?php endif; ?>
</section>
