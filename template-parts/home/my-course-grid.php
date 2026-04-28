<?php
/**
 * Template part: MP Academy — My Courses Grid (Homepage)
 * Handles 3 states:
 * 1) Logged in + has courses  → show grid
 * 2) Logged in + no courses   → show empty state
 * 3) Logged out               → show latest featured courses
 */

if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

$course_ids = [];
$latest_courses_query = null;

// Primary LearnDash method.
if ($user_id && function_exists('learndash_user_get_enrolled_courses')) {
    $course_ids = learndash_user_get_enrolled_courses($user_id, [
        'num'                   => 999,
        'post_status'           => ['publish', 'private'],
        'include_group_courses' => true,
        'orderby'               => 'title',
        'order'                 => 'ASC',
    ]);
}

if (!is_array($course_ids)) {
    $course_ids = (array) $course_ids;
}

// Legacy fallback.
if ($user_id && empty($course_ids) && function_exists('ld_get_mycourses')) {
    $legacy = ld_get_mycourses($user_id, ['posts_per_page' => 999]);
    if ($legacy instanceof WP_Query && $legacy->have_posts()) {
        $course_ids = wp_list_pluck($legacy->posts, 'ID');
    } elseif (is_array($legacy) && !empty($legacy)) {
        $course_ids = array_map(
            static fn($p) => is_object($p) ? (int)$p->ID : (int)$p,
            $legacy
        );
    }
}

// Clean IDs + limit
$course_ids = array_values(array_unique(array_map('intval', $course_ids)));
$max_cards  = 8;
if ($max_cards > 0 && count($course_ids) > $max_cards) {
    $course_ids = array_slice($course_ids, 0, $max_cards);
}

if (!$user_id || empty($course_ids)) {
    $latest_courses_query = new WP_Query([
        'post_type'      => 'sfwd-courses',
        'post_status'    => ['publish', 'private'],
        'posts_per_page' => 3,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}
?>


<section class="mp mp-my-courses mp-section" aria-labelledby="mp-my-courses-title">
    <div class="u-wrap">

        <header class="mp-my-courses__hd u-mb-12">
            <p class="c-h c-h--step-4">
                <?php echo esc_html(($user_id && !empty($course_ids)) ? __('My courses', 'mp-academy') : __('Featured courses', 'mp-academy')); ?>
            </p>
            <?php if ($user_id && empty($course_ids)): ?>
                <p class="mp-my-courses__empty-text u-charcoal u-step--1">
                    <?php esc_html_e('You don’t have any courses in progress.', 'mp-academy'); ?>
                </p>
            <?php endif; ?>
        </header>

        <?php if (!$user_id): ?>
            <?php if ($latest_courses_query && $latest_courses_query->have_posts()): ?>
                <div class="o-grid o-grid--gap-24 o-grid--cols-3 mp-my-courses__grid">
                    <?php
                    while ($latest_courses_query->have_posts()):
                        $latest_courses_query->the_post();

                        get_template_part(
                            'template-parts/components/cards/course-card-featured',
                            null,
                            [
                                'course_id' => get_the_ID(),
                            ]
                        );
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            <?php else: ?>
                <p class="mp-my-courses__empty-text u-charcoal u-step--1">
                    <?php esc_html_e('No courses are available yet.', 'mp-academy'); ?>
                </p>
            <?php endif; ?>

        <?php
        /*
        |--------------------------------------------------------------------------
        | STATE 2 — LOGGED IN BUT NO COURSES
        |--------------------------------------------------------------------------
        */
        elseif (empty($course_ids)): ?>
            <?php if ($latest_courses_query && $latest_courses_query->have_posts()): ?>
                <div class="o-grid o-grid--gap-24 o-grid--cols-3 mp-my-courses__grid">
                    <?php
                    while ($latest_courses_query->have_posts()):
                        $latest_courses_query->the_post();

                        get_template_part(
                            'template-parts/components/cards/course-card-featured',
                            null,
                            [
                                'course_id'         => get_the_ID(),
                                'show_enroll_cta'   => true,
                                'enroll_cta_label'  => __('Enroll now', 'mp-academy'),
                            ]
                        );
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            <?php else: ?>
                <p class="mp-my-courses__empty-text u-charcoal u-step--1">
                    <?php esc_html_e('No courses are available yet.', 'mp-academy'); ?>
                </p>
            <?php endif; ?>

        <?php
        /*
        |--------------------------------------------------------------------------
        | STATE 1 — LOGGED IN WITH COURSES
        |--------------------------------------------------------------------------
        */
        else: ?>

            <div class="o-grid o-grid--gap-24 o-grid--cols-3 mp-my-courses__grid">
                <?php foreach ($course_ids as $cid): ?>
                    <?php
                    get_template_part(
                        'template-parts/components/cards/course-card-enrolled',
                        null,
                        ['course_id' => (int)$cid]
                    );
                    ?>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</section>
