<?php
/**
 * Template part: MP Academy — My Courses Grid (Homepage)
 * Handles 3 states:
 * 1) Logged in + has courses  → show grid
 * 2) Logged in + no courses   → show empty state
 * 3) Logged out               → show “Welcome to the MP Academy”
 */

if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

/*
|--------------------------------------------------------------------------
| STATE 3 — NOT LOGGED IN
|--------------------------------------------------------------------------
*/
if (!$user_id): ?>
    <section class="mp mp-my-courses mp-section">
        <div class="u-wrap">
          			<p class="c-h c-h--step-4">
Welcome to the MP Academy</p>
        </div>
    </section>
    <?php return; ?>
<?php endif; ?>


<?php
/*
|--------------------------------------------------------------------------
| USER IS LOGGED IN — FIND ENROLLED COURSES
|--------------------------------------------------------------------------
*/

$course_ids = [];

// Primary LearnDash method
if (function_exists('learndash_user_get_enrolled_courses')) {
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

// Legacy fallback
if (empty($course_ids) && function_exists('ld_get_mycourses')) {
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
?>


<section class="mp mp-my-courses mp-section" aria-labelledby="mp-my-courses-title">
    <div class="u-wrap">

        <header class="mp-my-courses__hd u-mb-12">
           			<p class="c-h c-h--step-4">

                My courses
</p>
        </header>

        <?php
        /*
        |--------------------------------------------------------------------------
        | STATE 2 — LOGGED IN BUT NO COURSES
        |--------------------------------------------------------------------------
        */
        if (empty($course_ids)): ?>

            <div class="mp-my-courses__empty u-flex u-flex--column u-flex--align-center u-flex--justify-center u-text-center" style="margin: 40px 0;">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/no-courses.svg'); ?>"
                     alt=""
                     class="mp-my-courses__empty-icon"
                     style="width:180px; height: auto; margin-bottom: 16px;" />

                <p class="mp-my-courses__empty-text u-charcoal u-step--1">
                    You don’t have any courses in progress.
                </p>
            </div>

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
