<?php
/**
 * Template Name: LearnDash Profile - Franklin
 */

get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_courses = learndash_user_get_enrolled_courses($user_id, ['num' => 100]);
?>

<main id="primary" class="site-main u-flow">
  <section class="u-wrap u-space--section u-margin-top-xl">
    <div class="c-card u-padding u-shadow--medium u-radius--2xl">
      <h1 class="c-h">Welcome, <?php echo esc_html($current_user->display_name); ?></h1>
      <p class="c-text u-margin-bottom-l">Here’s your course progress and achievements.</p>

      <?php if (!empty($user_courses)) : ?>
        <div class="c-grid c-grid--2col@md u-gap u-margin-top-m">
          <?php foreach ($user_courses as $course_id) :
            $progress = learndash_course_progress([
              'user_id' => $user_id,
              'course_id' => $course_id,
              'array' => true
            ]);
            $course = get_post($course_id);
            $percent = $progress['percentage'];
            $course_image = get_the_post_thumbnail_url($course_id, 'medium') ?: 'https://via.placeholder.com/300x200?text=Course+Image';
          ?>
            <article class="mp c-card c-card--layout-multi c-card--size-small c-card--event c-card--inline-specs c-card--has-image">
              <span class="c-card__corner">
                <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--arrow-right">
                  <use xlink:href="/static/svg/sprite.svg#arrow-right"></use>
                </svg>
              </span>

              <div class="c-card__wrapper">
                <figure class="c-card__image">
                  <a href="<?php echo get_permalink($course_id); ?>">
                    <img src="<?php echo esc_url($course_image); ?>" alt="<?php echo esc_attr($course->post_title); ?>" />
                  </a>
                </figure>

                <div class="c-card__primary">
                  <header class="c-card__header u-flow--2xs">
                    <p class="c-card__meta">
                      <span><?php echo esc_html($percent); ?>% complete</span>
                    </p>
                    <h2 class="c-h c-card__title">
                      <a href="<?php echo get_permalink($course_id); ?>">
                        <?php echo esc_html($course->post_title); ?>
                      </a>
                    </h2>
                  </header>

                  <div class="c-card__content u-flow">
                    <div class="c-card__specs">
                      <dl>
                        <dt>Progress:</dt>
                        <dd style="margin-top: 10px;">
                          <div class="c-progress">
                            <div class="c-progress__bar" style="width: <?php echo esc_attr($percent); ?>%;"></div>
                          </div>
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>

                <a class="u-fill u-fill--link" href="<?php echo get_permalink($course_id); ?>">
                  <?php echo esc_html($course->post_title); ?>
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <p class="c-text">You are not enrolled in any courses yet.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>

<style>
/* Progress Bar Styles */
.c-progress {
  margin-top: 10px;
  height: 8px;
  background: #ccc; /* darker than #eee */
  border-radius: 4px;
  overflow: hidden;
}
.c-progress__bar {
  height: 100%;
  background-color: #005461;
  transition: width 0.3s ease-in-out;
}

/* Space between cards (if not handled already) */
.c-grid > * {
  margin-bottom: 2rem;
}
</style>
