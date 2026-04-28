<?php
/**
 * Template Name: LearnDash Profile - Franklin
 */

get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$is_logged_in = is_user_logged_in();
$user_courses = $is_logged_in ? learndash_user_get_enrolled_courses($user_id, ['num' => 100]) : [];
$completed_course_id = isset($_GET['course_completed']) ? absint($_GET['course_completed']) : 0;
$completed_course = null;
$completed_course_certificate_link = '';

if ($is_logged_in && $completed_course_id > 0 && function_exists('learndash_course_completed') && learndash_course_completed($user_id, $completed_course_id)) {
  $completed_course = get_post($completed_course_id);

  if ($completed_course instanceof WP_Post && function_exists('learndash_get_course_certificate_link')) {
    $completed_course_certificate_link = learndash_get_course_certificate_link($completed_course_id, $user_id);
  }
}
?>

<main id="primary" class="site-main u-flow">
  <section class="u-wrap mp-profile-page u-space--section u-margin-top-xl">
    <div class="c-card u-padding u-shadow--medium u-radius--2xl">
      <?php if (!$is_logged_in) : ?>
        <div class="mp c-usp c-usp--bordered mp-profile-guest-usp">
          <div class="u-flow">
            <h1 class="c-h c-h--step-2 c-usp__title">
              <?php esc_html_e('Welcome to MP Academy', 'mp-academy'); ?>
            </h1>
            <div class="mp o-prose u-step--1">
              <p>
                <?php esc_html_e('Register for access to our learning resources, or log in to continue your training and view your progress.', 'mp-academy'); ?>
              </p>
            </div>
          </div>
        </div>
      <?php else : ?>
      <?php if ($completed_course instanceof WP_Post) : ?>
        <div class="mp-course-complete-banner u-margin-bottom-l">
          <p class="mp-course-complete-banner__eyebrow">Course completed</p>
          <h1 class="c-h mp-course-complete-banner__title">Congratulations, you finished <?php echo esc_html($completed_course->post_title); ?></h1>
          <p class="c-text">Your course has been marked as complete.</p>

          <?php if (!empty($completed_course_certificate_link)) : ?>
            <p class="mp-course-complete-banner__actions">
              <a class="mp-profile-certificate__link" href="<?php echo esc_url($completed_course_certificate_link); ?>" target="_blank" rel="noopener noreferrer">
                Download Certificate
              </a>
            </p>
          <?php else : ?>
            <p class="c-text">Your certificate is not available yet.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

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
            $certificate_link = '';
            if (function_exists('learndash_get_course_certificate_link')) {
              $certificate_link = learndash_get_course_certificate_link($course_id, $user_id);
            }
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

                    <?php if (!empty($certificate_link)) : ?>
                      <p class="mp-profile-certificate">
                        <a class="mp-profile-certificate__link" href="<?php echo esc_url($certificate_link); ?>" target="_blank" rel="noopener noreferrer">
                          Download Certificate
                        </a>
                      </p>
                    <?php endif; ?>
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

.mp-profile-certificate {
  margin-top: 1rem;
  position: relative;
  z-index: 2;
}

.mp-course-complete-banner {
  padding: 1.5rem;
  border: 1px solid #d8e8eb;
  border-radius: 1.25rem;
  background: linear-gradient(135deg, #f4fbfc 0%, #eef7f8 100%);
}

.mp-course-complete-banner__eyebrow {
  margin: 0 0 0.5rem;
  color: #005461;
  font-size: 0.8125rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.mp-course-complete-banner__title {
  margin-bottom: 0.5rem;
}

.mp-course-complete-banner__actions {
  margin: 1rem 0 0;
}

.mp-profile-certificate__link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.75rem;
  padding: 0.625rem 1rem;
  border-radius: 999px;
  background: #005461;
  color: #fff;
  font-weight: 600;
  text-decoration: none;
  position: relative;
  z-index: 2;
}

.mp-profile-certificate__link:hover,
.mp-profile-certificate__link:focus {
  background: #003f48;
  color: #fff;
}

.mp-profile-page {
  margin-bottom: 4rem;
}

.mp-profile-guest-usp {
  max-width: 100%;
}

.mp-profile-guest-usp .c-usp__title {
  display: inline-block;
  max-width: 100%;
  white-space: normal;
}

.page-template-page-ld-profile {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.page-template-page-ld-profile .site {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.page-template-page-ld-profile .site-main {
  flex: 1 0 auto;
}

/* Space between cards (if not handled already) */
.c-grid > * {
  margin-bottom: 2rem;
}
</style>
