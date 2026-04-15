<?php
get_header();

$quiz_id = get_the_ID();
$quiz_title = get_the_title();
$course_id = function_exists( 'learndash_get_course_id' ) ? learndash_get_course_id( $quiz_id ) : 0;
$course_url = $course_id ? get_permalink( $course_id ) : '';
$course_title = $course_id ? get_the_title( $course_id ) : '';
?>

<main id="primary" class="site-main mp-quiz-page">
  <section class="mp-quiz-shell u-wrap">
    <div class="mp-quiz-card c-card u-shadow--medium u-radius--2xl">
      <div class="mp-quiz-card__header">
        <p class="mp-quiz-card__eyebrow">Final Quiz</p>
        <h1 class="c-h mp-quiz-card__title"><?php echo esc_html( $quiz_title ); ?></h1>
        <p class="c-text mp-quiz-card__lede">
          Stay focused and take your time. You need a score of <strong>80%</strong> to pass.
        </p>

        <div class="mp-quiz-card__meta">
          <?php if ( $course_title ) : ?>
            <span class="mp-quiz-chip"><?php echo esc_html( $course_title ); ?></span>
          <?php endif; ?>
          <span class="mp-quiz-chip mp-quiz-chip--accent">Passing score: 80%</span>
        </div>

        <?php if ( $course_url ) : ?>
          <p class="mp-quiz-card__back">
            <a href="<?php echo esc_url( $course_url ); ?>">Back to course</a>
          </p>
        <?php endif; ?>
      </div>

      <div class="mp-quiz-card__body">
        <?php echo do_shortcode( '[ld_quiz quiz_id="' . $quiz_id . '"]' ); ?>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
