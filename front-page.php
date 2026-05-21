<?php
/**
 * Template: MP Academy Homepage
 * 
 * @package MP_Academy
 */

get_header(); 
?>

<main id="primary" class="site-main u-flow">
  <?php 
  // Hero Section
  get_template_part('template-parts/home/home-hero'); 
  ?>

  <div class="mp-home-update-test u-wrap" role="status">
    <?php esc_html_e('Testing version', 'mp-academy'); ?>
  </div>
  
  <?php 
  // My Courses Section (Shows enrolled courses OR empty state)
  get_template_part('template-parts/home/my-course-grid'); 
  ?>

  <?php 
  // Latest Videos Section
  get_template_part('template-parts/home/featured-video-grid'); 
  ?>
  
</main>

<?php get_footer(); ?>
