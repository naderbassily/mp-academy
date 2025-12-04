<?php
/**
 * Template part: MP Academy — My Courses Grid (clean + LD-safe)
 * Location: template-parts/home/my-course-grid.php
 */
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();
?>
<section class="mp mp-my-courses mp-section" aria-labelledby="mp-my-courses-title">
  <div class="u-wrap">
    <header class="mp-my-courses__hd u-mb-12">
      <h2 id="mp-my-courses-title" class="c-heading c-heading--h3">
        <?php esc_html_e('My courses', 'mp-academy'); ?>
      </h2>
    </header>

    <?php if (!$user_id): ?>
      <p class="mp-my-courses__note">
        <?php
          printf(
            wp_kses_post(__('Please <a href="%s">log in</a> to see your courses.', 'mp-academy')),
            esc_url(wp_login_url(get_permalink()))
          );
        ?>
      </p>
      <?php return; ?>
    <?php endif; ?>

    <?php
    // Gather enrolled course IDs (direct + via Groups)
    $course_ids = [];
    if (function_exists('learndash_user_get_enrolled_courses')) {
      $course_ids = learndash_user_get_enrolled_courses($user_id, [
        'num'                   => 999,
        'post_status'           => ['publish', 'private'],
        'include_group_courses' => true,
        'orderby'               => 'title',
        'order'                 => 'ASC',
      ]);
    }
    if (!is_array($course_ids)) $course_ids = (array) $course_ids;

    // Legacy fallback
    if (empty($course_ids) && function_exists('ld_get_mycourses')) {
      $legacy = ld_get_mycourses($user_id, ['posts_per_page' => 999]);
      if ($legacy instanceof WP_Query && $legacy->have_posts()) {
        $course_ids = wp_list_pluck($legacy->posts, 'ID');
      } elseif (is_array($legacy) && !empty($legacy)) {
        $course_ids = array_map(static function($p){ return is_object($p) ? (int)$p->ID : (int)$p; }, $legacy);
      }
    }

    // Clean + optional cap
    $course_ids = array_values(array_unique(array_map('intval', $course_ids)));
    $max_cards  = 8; // adjust or set to 0 to show all
    if ($max_cards > 0 && count($course_ids) > $max_cards) {
      $course_ids = array_slice($course_ids, 0, $max_cards);
    }
    ?>

    <?php if (empty($course_ids)) : ?>
      <p class="mp-my-courses__note"><?php esc_html_e('No enrolled courses found.', 'mp-academy'); ?></p>
    <?php else : ?>
      <div class="o-grid o-grid--gap-24 o-grid--cols-3 mp-my-courses__grid">
        <?php foreach ($course_ids as $cid): ?>
          <?php get_template_part('template-parts/components/my-course-card', null, ['course_id' => (int)$cid]); ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
