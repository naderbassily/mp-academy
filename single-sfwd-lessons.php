<?php
/**
 * Template: LearnDash Single Lesson
 * Franklin Design System
 *
 * @package MP_Academy
 */

if (!defined('ABSPATH')) exit;

get_header();

$user_id = get_current_user_id();
$progression_locked = false;
$previous_item      = null;
?>

<?php
			$lesson_id = get_the_ID();
			$course_id = function_exists('learndash_get_course_id')
				? learndash_get_course_id($lesson_id)
				: 0;

			if (
				$user_id
				&& $course_id
				&& function_exists('learndash_lesson_progression_enabled')
				&& learndash_lesson_progression_enabled($course_id)
				&& function_exists('learndash_user_progress_get_previous_incomplete_step')
				&& ! ( function_exists('learndash_can_user_bypass') && learndash_can_user_bypass($user_id, 'learndash_course_progression') )
			) {
				$previous_item_id = (int) learndash_user_progress_get_previous_incomplete_step($user_id, $course_id, $lesson_id);

				if ($previous_item_id && $previous_item_id !== $lesson_id) {
					$progression_locked = true;
					$previous_item = get_post($previous_item_id);
				}
			}
			?>

			<?php
			// Hero Section (Full width, dark background, shorter height)
			get_template_part('template-parts/lesson/single/hero', null, [
				'lesson_id' => $lesson_id,
				'course_id' => $course_id,
			]);
			?>

<main id="primary" class="site-main u-wrap u-margin-top-xl u-margin-bottom-xl">
	<div class="mp-single-lesson">
		<div class="u-wrap--content">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php if ( $progression_locked ) : ?>
				<?php
				learndash_get_template_part(
					'modules/messages/lesson-progression.php',
					array(
						'previous_item' => $previous_item,
						'course_id'     => $course_id,
						'context'       => 'lesson',
						'user_id'       => $user_id,
					),
					true
				);
				?>
			<?php else : ?>
				<?php
				// Progress bar (same component, different context)
				if ($user_id && $course_id) {
					get_template_part('template-parts/components/progress-bar', null, [
						'course_id' => $course_id,
						'user_id'   => $user_id,
						'context'   => 'lesson-page',
					]);
				}
				?>

				<div class="mp-lesson-content u-margin-top-lg">
					<?php the_content(); ?>
				</div>

				<?php if ( function_exists('learndash_get_lesson_topics_list') ) : ?>
					<section class="mp-lesson-topics u-margin-top-xl">
						<h2 class="mp-section-title">Topics</h2>

						<?php
						echo learndash_get_lesson_topics_list(
							$lesson_id,
							$user_id,
							$course_id
						);
						?>
					</section>
				<?php endif; ?>
			<?php endif; ?>
		<?php endwhile; ?>
		</div>
	</div>
</main>

<?php
get_footer();
