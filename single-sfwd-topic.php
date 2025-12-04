<?php
get_header();

$topic_id  = get_the_ID();
$lesson_id = function_exists('learndash_get_setting') ? learndash_get_setting($topic_id, 'lesson') : 0;
$course_id = function_exists('learndash_get_course_id') ? learndash_get_course_id($topic_id) : 0;

$course_url = $course_id ? get_permalink($course_id) : '';
$lesson_url = $lesson_id ? get_permalink($lesson_id) : '';
?>

<main id="primary" class="site-main u-wrap u-space--section u-flow u-margin-top-xl u-margin-bottom-xl">

  <!-- Franklin Breadcrumbs -->
  <nav class="c-breadcrumb" aria-label="Breadcrumb">
    <ol class="c-breadcrumb__list" role="list">
      <?php if ($course_id): ?>
        <li class="c-breadcrumb__item" role="listitem">
          <a href="<?php echo esc_url($course_url); ?>" class="c-breadcrumb__link">
            <?php echo esc_html(get_the_title($course_id)); ?>
          </a>
          <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--chevron-down">
            <use xlink:href="/static/svg/sprite.svg#chevron-down"></use>
          </svg>
        </li>
      <?php endif; ?>

      <?php if ($lesson_id): ?>
        <li class="c-breadcrumb__item" role="listitem">
          <a href="<?php echo esc_url($lesson_url); ?>" class="c-breadcrumb__link">
            <?php echo esc_html(get_the_title($lesson_id)); ?>
          </a>
          <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--chevron-down">
            <use xlink:href="/static/svg/sprite.svg#chevron-down"></use>
          </svg>
        </li>
      <?php endif; ?>

      <li class="c-breadcrumb__item" role="listitem">
        <span class="c-breadcrumb__current"><?php echo esc_html(get_the_title($topic_id)); ?></span>
      </li>
    </ol>
  </nav>

  <header class="u-flow--m">
    <h1 class="c-h c-h--page-title"><?php the_title(); ?></h1>

    <p class="u-text-xs u-color-text-alt">
      <?php if ($lesson_id): ?>
        <a href="<?php echo esc_url($lesson_url); ?>" class="c-link u-color-text-alt">← Back to Lesson</a>
      <?php elseif ($course_id): ?>
        <a href="<?php echo esc_url($course_url); ?>" class="c-link u-color-text-alt">← Back to Course</a>
      <?php endif; ?>
    </p>
  </header>

  <section class="c-content o-prose u-flow--prose">
    <?php
    if (have_posts()) :
      while (have_posts()) : the_post();
        the_content(); // LearnDash renders its notices/buttons here – we’ll restyle them below via JS.
      endwhile;
    endif;
    ?>
  </section>

  <!-- Optional Franklin Back button (visible regardless of LD controls) -->
  <div class="u-margin-top-m">
    <?php if ($lesson_id): ?>
      <a href="<?php echo esc_url($lesson_url); ?>" class="mp c-button">&larr; Back</a>
    <?php elseif ($course_id): ?>
      <a href="<?php echo esc_url($course_url); ?>" class="mp c-button">&larr; Back</a>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
<style>
.learndash-wrapper .ld-breadcrumbs {
  display: none !important;
}

.learndash-wrapper .ld-progress .ld-progress-heading {
  display: none;
}
.learndash-wrapper .ld-progress {
  background: none;
  padding: 0;
  border: none;
}

.learndash-wrapper .ld-progress .ld-progress-bar {
  margin-top: 10px;
  height: 8px;
  background: #ccc;
  border-radius: 4px;
  overflow: hidden;
}
.learndash-wrapper .ld-progress .ld-progress-bar div {
  height: 100%;
  background-color: #005461;
  transition: width 0.3s ease-in-out;
  border-radius: 4px;
}

.learndash-wrapper a.ld-button {
  all: unset;
  display: inline-block;
  padding: 0.5em 1.25em;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.875rem;
  line-height: 1.4;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
}
.learndash-wrapper a.ld-button[href*="mark_complete"] {
  background-color: #0072ce;
  color: white;
}

.learndash-wrapper a.ld-button[href*="lesson"] {
  border: 2px solid #28a745;
  color: #28a745;
  background: none;
}

.learndash-wrapper a.ld-button[href*="next"],
.learndash-wrapper a.ld-button[href*="prev"] {
  background: #eaeaea;
  color: black;
  font-size: 0.75rem;
  padding: 0.35em 0.75em;
}

.learndash-wrapper .ld-content-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.ld-button  {background:none !important; color:#006daf !important;  outline: none !important;
  box-shadow: none !important;
 }
 .ld-course-step-back {display: none !important;}
 .c-button{
  display: inline-block !important;
  width: auto !important;
}
.learndash_mark_complete_button { background:#00a2c2 !important;}
/* Make LearnDash progress bar look like .c-progress */
.learndash-wrapper .ld-progress .ld-progress-bar {
  margin-top: 10px;
  height: 8px;
  background: #ccc !important; /* match your .c-progress */
  border-radius: 4px;
  overflow: hidden;
  padding: 0 !important;
}

/* Style the filled progress area like .c-progress__bar */
.learndash-wrapper .ld-progress .ld-progress-bar > div {
  height: 100%;
  background-color: #005461 !important; /* match your .c-progress__bar */
  transition: width 0.3s ease-in-out;
  border-radius: 4px;
}

</style>