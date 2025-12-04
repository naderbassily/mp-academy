<?php
get_header();
?>

<main id="primary" class="site-main u-flow u-margin-top-xl u-margin-bottom-xl">
  <section class="u-wrap u-space--section">
    <h1 class="c-h u-margin-bottom-l">Available Courses</h1>

    <?php if (have_posts()) : ?>
<div class="o-grid mp-courses-grid o-grid--swipeable">
        <?php while (have_posts()) : the_post(); ?>
          <?php get_template_part('template-parts/course-archive-card'); ?>
        <?php endwhile; ?>
      </div>

      <!-- Pagination -->
      <div class="u-wrap u-space--section">
        <?php the_posts_pagination(); ?>
      </div>

    <?php else : ?>
      <p>No courses found.</p>
    <?php endif; ?>
    
  </section>
</main>

<?php get_footer(); ?>
