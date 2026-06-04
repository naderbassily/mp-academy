<?php
if (!function_exists('format_duration_hhmm')) {
  function format_duration_hhmm($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return sprintf('%02d:%02d', $hours, $minutes);
  }
}

$args = array(
  'post_type'      => 'sfwd-courses',
  'posts_per_page' => 3,
  'post_status'    => 'publish'
);

$query = new WP_Query($args);

if ($query->have_posts()) :
?>
<section class="u-wrap u-margin-top-l-xl u-margin-bottom-l-xl">
  <h2 class="c-h u-margin-bottom-s">Featured Courses</h2>

  <div class="o-grid o-grid--of-three o-grid--swipeable">
    <?php while ($query->have_posts()) : $query->the_post();

      $course_id   = get_the_ID();
      $title       = get_the_title();
      $permalink   = get_permalink();
      $image_url   = get_the_post_thumbnail_url($course_id, 'large') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
      $release     = get_the_date('M Y');
      $language    = 'English';

      $raw_duration = get_post_meta($course_id, '_learndash_course_grid_duration', true);
      $duration     = $raw_duration ? format_duration_hhmm(intval($raw_duration)) : '00:00';

      $short_desc = get_post_meta($course_id, '_learndash_course_grid_short_description', true);
      if (empty($short_desc)) {
        $short_desc = wp_trim_words(get_the_excerpt(), 20);
      }

      $terms    = get_the_terms($course_id, 'ld_course_category');
      $category = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'General';

      $user_id     = get_current_user_id();
      $button_text = sfwd_lms_has_access($course_id, $user_id) ? 'Continue' : 'Enroll Now';
    ?>

    <article class="mp c-card c-card--layout-single c-card--size-large c-card--featured c-card--inline-specs c-card--has-tag c-card--has-image">
      <span class="c-card__tag"><?php echo esc_html($category); ?></span>
      <div class="c-card__wrapper">
        <figure class="c-card__image">
          <a href="<?php echo esc_url($permalink); ?>">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
          </a>
        </figure>
        <div class="c-card__primary">
          <header class="c-card__header u-flow--2xs">
            <p class="c-card__meta">
              <span class="mp c-twi c-twi--left">
                <span><?php echo esc_html($duration); ?></span>
                <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--play">
                  <use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#play"></use>
                </svg>
              </span>
              <span><?php echo esc_html($release); ?></span>
              <span><?php echo esc_html($language); ?></span>
            </p>
            <h2 class="c-h c-card__title">
              <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
            </h2>
          </header>
          <div class="c-card__content u-flow">
            <div class="c-card__specs">
              <dl>
                <dt>Category:</dt>
                <dd><?php echo esc_html($category); ?></dd>
              </dl>
              <dl>
                <dt>Description:</dt>
                <dd><?php echo esc_html($short_desc); ?></dd>
              </dl>
            </div>
          </div>
          <footer class="c-card__footer u-flow--2xs">
            <a href="<?php echo esc_url($permalink); ?>" class="mp c-button c-button--inline c-button--outline-white">
              <?php echo esc_html($button_text); ?>
            </a>
          </footer>
        </div>
        <a class="u-fill u-fill--link" href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
      </div>
    </article>

    <?php endwhile; ?>
  </div>
</section>
<?php
  wp_reset_postdata();
endif;
?>
