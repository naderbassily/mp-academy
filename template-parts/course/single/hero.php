<?php
/**
 * Template part: Single Course Hero Section
 */

if (!defined('ABSPATH')) exit;

$course_id = (int) ($args['course_id'] ?? get_the_ID());
$title = get_the_title($course_id);
$short_desc = get_post_meta($course_id, '_learndash_course_grid_short_description', true);

if (empty($short_desc)) {
  $short_desc = has_excerpt($course_id) ? get_the_excerpt($course_id) : '';
}

$hero_image = get_the_post_thumbnail_url($course_id, 'full');
$hero_fallback = 'https://dam.malvernpanalytical.com/fae4c741-f556-475a-b286-b36e0098eefe/website%20hero%20placeholder_Original%20file.svg';
$hero_media = $hero_image ?: $hero_fallback;
?>

<section class="c-hero c-hero--dark mp-course-hero" style="--placeholder-image: url('<?php echo esc_url($hero_media); ?>')">
  <div class="c-hero__wrap">
    <div class="c-hero__main">
      <?php
      get_template_part('template-parts/components/breadcrumbs', null, [
        'course_id'    => $course_id,
        'extra_class'  => 'c-breadcrumb--dark',
      ]);
      ?>

      <h1 class="c-hero__heading"><?php echo esc_html($title); ?></h1>

      <?php if (!empty($short_desc)) : ?>
        <p class="c-hero__lede"><?php echo esc_html($short_desc); ?></p>
      <?php endif; ?>
    </div>

    <div class="c-hero__media-wrap"></div>
  </div>
</section>
