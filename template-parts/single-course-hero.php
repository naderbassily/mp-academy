<?php
/**
 * Template part: Single Course Hero Section
 */

$course_id = get_the_ID();
$title = get_the_title();
$short_desc = get_post_meta($course_id, '_learndash_course_grid_short_description', true);

if (empty($short_desc)) {
  $short_desc = wp_trim_words(get_the_excerpt(), 20);
}
?>

<figure class="mp c-hero">
  <div class="u-wrap">
    <div class="c-hero__content">
      <header class="u-flow--m">
        <h1 class="c-h c-h--page-title"><?php echo esc_html($title); ?></h1>
        <p><?php echo esc_html($short_desc); ?></p>
      </header>
    </div>
  </div>
</figure>
