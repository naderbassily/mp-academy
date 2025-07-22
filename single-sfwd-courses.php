<?php get_header(); ?>

<main id="primary" class="site-main u-flow">

  <?php get_template_part('template-parts/single-course-hero'); ?>

<section class="u-wrap u-space--section u-margin-top-l u-margin-bottom-s">
      <h2 class="c-h u-margin-bottom-s">Course Overview</h2>

    <div class="c-content">
      <?php
      if (have_posts()) :
        the_post();
        // Get raw content (no filters, no LearnDash additions)
        echo wpautop(get_post_field('post_content', get_the_ID()));
      endif;
      ?>
    </div>
  </section>

    <section class="u-wrap u-space--section u-border-top u-margin-top-l">
    <div class="c-content u-flow">
<?php
$course_id = get_the_ID();
$lessons   = learndash_get_lesson_list($course_id);

if (!empty($lessons)) : ?>
<section class="u-wrap u-space--section">
  <h2 class="c-h u-margin-bottom-s">Course Content</h2>
  <dl class="c-accordion">
    <?php foreach ($lessons as $lesson) : ?>
      <div class="c-accordion__item">
        <dt class="c-accordion__title">
          <?php echo esc_html(get_the_title($lesson->ID)); ?>
        </dt>
        <dd class="c-accordion__content o-prose u-flow--prose">
          <ul class="u-flow--xs">
            <?php
            $topics = learndash_get_topic_list($lesson->ID, $course_id);
            if (!empty($topics)) :
              foreach ($topics as $topic) : ?>
                <li>
                  <a class="c-link" href="<?php echo esc_url(get_permalink($topic->ID)); ?>">
                    <?php echo esc_html(get_the_title($topic->ID)); ?>
                  </a>
                </li>
              <?php endforeach;
            else : ?>
              <li><em>No topics for this lesson.</em></li>
            <?php endif; ?>
          </ul>
        </dd>
      </div>
    <?php endforeach; ?>
  </dl>
</section>
<?php endif; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
