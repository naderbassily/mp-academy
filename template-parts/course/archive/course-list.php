<?php
/**
 * Template Part: Course List (Organized by Categories)
 * Franklin Grid System: o-grid o-grid--4col
 * 
 * Location: template-parts/course/archive/course-list.php
 */
if (!defined('ABSPATH')) exit;

// Get all courses
$courses_query = new WP_Query([
	'post_type'      => 'sfwd-courses',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
]);

if (!$courses_query->have_posts()) {
	echo '<p class="mp-archive__empty u-text-center u-padding-top-xl u-padding-bottom-xl">' . esc_html__('No courses available.', 'mp-academy') . '</p>';
	wp_reset_postdata();
	return;
}

$user_id = get_current_user_id();

// Get user's enrolled courses
$enrolled_course_ids = [];
if ($user_id && function_exists('learndash_user_get_enrolled_courses')) {
	$enrolled_course_ids = learndash_user_get_enrolled_courses($user_id, [
		'num' => 999,
		'include_group_courses' => true,
	]);
	if (!is_array($enrolled_course_ids)) {
		$enrolled_course_ids = [];
	}
}

// Define categories (matching screenshot)
$categories = [
	'theory' => [
		'label' => __('Theory 101', 'mp-academy'),
		'courses' => [],
	],
	'instrument' => [
		'label' => __('Instrument videos', 'mp-academy'),
		'courses' => [],
	],
	'training' => [
		'label' => __('User training', 'mp-academy'),
		'courses' => [],
	],
];

// Categorize courses
while ($courses_query->have_posts()) {
	$courses_query->the_post();
	$course_id = get_the_ID();
	
	// Get course category
	$course_categories = wp_get_post_terms($course_id, 'ld_course_category', ['fields' => 'slugs']);
	
	// Add course to appropriate category
	$added = false;
	if (!empty($course_categories)) {
		foreach ($course_categories as $cat_slug) {
			if (isset($categories[$cat_slug])) {
				$categories[$cat_slug]['courses'][] = $course_id;
				$added = true;
				break;
			}
		}
	}
	
	// Default to theory if no category
	if (!$added) {
		$categories['theory']['courses'][] = $course_id;
	}
}

wp_reset_postdata();

// Display courses by category
foreach ($categories as $cat_key => $category) :
	if (empty($category['courses'])) continue;
	?>
	
	<section class="mp-archive-section u-margin-bottom-xxl" data-category="<?php echo esc_attr($cat_key); ?>">
		
		<!-- Category Title -->
		<h2 class="mp-archive-section__title c-heading c-heading--h2 u-margin-bottom-l">
			<?php echo esc_html($category['label']); ?>
		</h2>
		
		<!-- Franklin Grid: 4 columns -->
		<div class="o-grid o-grid--of-four">
			<?php 
			foreach ($category['courses'] as $course_id) :
				
				// Get enrollment status
				$is_enrolled = in_array($course_id, $enrolled_course_ids, true);
				$status = 'not-started';
				
				if ($is_enrolled && $user_id) {
					$progress = learndash_course_progress([
						'user_id'   => $user_id,
						'course_id' => $course_id,
						'array'     => true,
					]);
					
					$percentage = 0;
					if (!empty($progress['total']) && $progress['total'] > 0) {
						$percentage = round(($progress['completed'] / $progress['total']) * 100);
					}
					
					// Check completion status
					if ($percentage >= 100) {
						$status = 'completed';
					} elseif ($percentage > 0) {
						$status = 'in-progress';
					} else {
						$status = 'not-started';
					}
				}
				
				// Setup post data
				$post = get_post($course_id);
				setup_postdata($GLOBALS['post'] = $post);
				?>
				
				<div 
					class="o-grid__item" 
					data-course-status="<?php echo esc_attr($status); ?>"
					data-course-enrolled="<?php echo $is_enrolled ? 'true' : 'false'; ?>"
				>
					<?php 
					// Use featured card with progress bar enabled
					get_template_part('template-parts/components/cards/course-card-featured', null, [
						'course_id' => $course_id,
						'show_progress' => true  // Enable progress bar on archive page
					]); 
					?>
				</div>
				
			<?php endforeach; ?>
		</div>
		
	</section>
	
<?php endforeach; 

wp_reset_postdata();
?>