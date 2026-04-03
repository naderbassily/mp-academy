<?php
get_header();

$quiz_id = get_the_ID();
$quiz_title = get_the_title();
?>

<main id="primary" class="site-main u-wrap u-space--section u-flow u-margin-top-xl u-margin-bottom-xl">
    
  <section class="u-wrap u-space--section u-flow">
    <div class="c-card u-padding u-shadow--small u-radius--2xl u-bg--light u-text-center">
      <h2 class="c-h u-margin-bottom-xs"><?php echo esc_html($quiz_title); ?></h2>
      <p class="c-text">
        Please focus and answer carefully.<br>
        You need to score at least <strong>80%</strong> to pass and access the next lesson.
      </p>

    <?php echo do_shortcode('[ld_quiz quiz_id="' . $quiz_id . '"]'); ?>

    </div>
  </section>
</main>

<?php get_footer(); ?>
