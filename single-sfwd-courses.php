<?php
/**
 * Template: LearnDash Single Course
 */
get_header();

$course_id = get_the_ID();
$current_user_id = get_current_user_id();
?>

<main id="primary" class="site-main">
  <section class="u-wrap u-space--section mp-course">

    <?php
      // 1) Breadcrumbs (we'll wire it up next step)
      get_template_part('template-parts/course/breadcrumbs', null, [
        'course_id' => $course_id,
      ]);

      // 2) Title + short description
      get_template_part('template-parts/course/title', null, [
        'course_id' => $course_id,
      ]);

      // 3) Progress bar (placeholder for now)
      get_template_part('template-parts/course/progress', null, [
        'course_id' => $course_id,
        'user_id'   => $current_user_id,
      ]);
    ?>

 <div class="u-margin-top-l" id="mp-course-modules">
  <?php
    get_template_part('template-parts/course/module-list', null, [
      'course_id' => $course_id,
      'user_id'   => $current_user_id,
    ]);
  ?>
</div>


    <div class="u-margin-top-xl" id="mp-course-overview">
      <?php
         get_template_part('template-parts/course/overview', null, [
           'course_id' => $course_id,
         ]);
      ?>
    </div>

  </section>
</main>

<?php get_footer(); ?>
