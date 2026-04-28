<?php
/**
 * Template part: Single Quiz Hero Section
 */

if (!defined('ABSPATH')) exit;

$quiz_id    = (int)($args['quiz_id']    ?? get_the_ID());
$course_id  = (int)($args['course_id'] ?? 0);
$quiz_title = get_the_title($quiz_id);
$excerpt    = has_excerpt($quiz_id) ? get_the_excerpt($quiz_id) : '';

$pass_score    = isset($args['pass_score'])    ? (int)    $args['pass_score']     : 0;
$last_activity = isset($args['last_activity']) ? (string) $args['last_activity'] : '';

$hero_svg = 'https://dam.malvernpanalytical.com/fae4c741-f556-475a-b286-b36e0098eefe/website%20hero%20placeholder_Original%20file.svg';
?>

<section id="mp-custom-small-hero" class="c-hero c-hero--dark mp-small-hero"
  style="--placeholder-image: url('<?php echo esc_url($hero_svg); ?>')">
  <div class="c-hero__wrap">
    <div class="c-hero__main">

      <?php
      get_template_part('template-parts/components/breadcrumbs', null, [
        'course_id'   => $course_id,
        'quiz_id'     => $quiz_id,
        'extra_class' => 'c-breadcrumb--dark',
      ]);
      ?>

      <h1 class="c-hero__heading">
        <?php echo esc_html($quiz_title); ?>
      </h1>

      <?php if ($excerpt): ?>
        <p class="c-hero__lede u-margin-top-s">
          <?php echo esc_html($excerpt); ?>
        </p>
      <?php endif; ?>

      <div class="mp-quiz-hero-meta">
        <?php if ($pass_score > 0): ?>
          <span class="mp-quiz-hero-pass-score">
            <?php
            printf(
              /* translators: %d: passing percentage */
              esc_html__('Passing score: %d%%', 'mp-academy'),
              $pass_score
            );
            ?>
          </span>
        <?php endif; ?>

        <?php if ($last_activity): ?>
          <span class="mp-quiz-hero-activity">
            <?php esc_html_e('Last activity:', 'mp-academy'); ?>
            <?php echo esc_html($last_activity); ?>
          </span>
        <?php endif; ?>
      </div>

    </div>
    <div class="c-hero__media-wrap"></div>
  </div>
</section>
