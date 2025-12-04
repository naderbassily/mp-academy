<?php
/**
 * Template Part: Single Course Card (Franklin style)
 * Used in: Course archive, featured section, etc.
 */

$course_id = get_the_ID();
$title     = get_the_title();
$permalink = get_permalink();
$image_url = get_the_post_thumbnail_url($course_id, 'large') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';

$short_desc = get_post_meta($course_id, '_learndash_course_grid_short_description', true);
if (empty($short_desc)) {
  $short_desc = wp_trim_words(get_post_field('post_content', $course_id), 25);
}

$terms    = get_the_terms($course_id, 'ld_course_category');
$category = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'General';
?>

<article class="mp c-card c-card--layout-single c-card--size-medium c-card--alt c-card--has-tag c-card--has-image">
  <span class="c-card__tag"><?php echo esc_html($category); ?></span>
  <div class="c-card__wrapper">
    <figure class="c-card__image">
      <a href="<?php echo esc_url($permalink); ?>">
        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
      </a>
    </figure>
    <div class="c-card__primary">
      <header class="c-card__header u-flow--2xs">
        <h2 class="c-h c-card__title">
          <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
        </h2>
      </header>
      <div class="c-card__content u-flow">
        <div class="mp o-prose u-flow--prose u-step--1">
          <p><?php echo esc_html($short_desc); ?></p>
        </div>
      </div>
    </div>
    <a class="u-fill u-fill--link" href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
  </div>
</article>
