<?php
/**
 * Template: LearnDash Single Course
 * Franklin Design System
 *
 * @package MP_Academy
 */

if ( ! defined('ABSPATH') ) exit;

get_header();

$course_id  = get_the_ID();
$user_id    = get_current_user_id();
$has_access = false;

if ( $user_id && function_exists('sfwd_lms_has_access') ) {
  $has_access = (bool) sfwd_lms_has_access( $course_id, $user_id );
}
?>

<?php
get_template_part('template-parts/course/single/hero', null, [
  'course_id' => $course_id,
]);
?>

<main id="primary" class="site-main u-wrap u-margin-bottom-xl">
  <div class="mp-single-course">
    <div class="u-wrap--content">

      <?php if ( ! $user_id ) : ?>

        <!-- Not logged in -->
        <div class="u-margin-top-m u-margin-bottom-m">
          <p><?php esc_html_e( 'Please log in to enroll in this course.', 'mp-academy' ); ?></p>
        </div>

      <?php elseif ( ! $has_access ) : ?>

        <!-- Logged in but NOT enrolled -->
        <div class="u-margin-top-m u-margin-bottom-m">
          <p><?php esc_html_e( 'Please enroll to access this course.', 'mp-academy' ); ?></p>

          <?php
          if ( shortcode_exists('learndash_payment_buttons') ) {
            echo do_shortcode('[learndash_payment_buttons course_id="' . (int) $course_id . '"]');
          } elseif ( shortcode_exists('learndash_course_info') ) {
            echo do_shortcode('[learndash_course_info course_id="' . (int) $course_id . '"]');
          }
          ?>
        </div>

      <?php else : ?>

        <!-- Enrolled -->
        <?php
        get_template_part('template-parts/components/progress-bar', null, [
          'course_id' => $course_id,
          'user_id'   => $user_id,
          'context'   => 'course-page',
        ]);
        ?>

        <?php
        get_template_part('template-parts/components/course-complete-banner', null, [
          'course_id' => $course_id,
          'user_id'   => $user_id,
        ]);
        ?>

      <?php endif; ?>

      <?php
      // Lock content if not enrolled
      $locked_class = ( $user_id && ! $has_access ) ? 'mp-course-locked' : '';
      ?>

      <div class="<?php echo esc_attr( $locked_class ); ?>">

        <?php
        // Module list (always visible)
        get_template_part('template-parts/course/single/module-list', null, [
          'course_id' => $course_id,
          'user_id'   => $user_id,
        ]);
        ?>

        <?php
        // Course overview (always visible)
        get_template_part('template-parts/course/single/overview', null, [
          'course_id' => $course_id,
        ]);
        ?>



      </div>

    </div>
  </div>
</main>

<?php get_footer(); ?>
