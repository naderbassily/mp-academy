<?php
/**
 * Template: LearnDash Single Lesson
 * Franklin Design System
 *
 * @package MP_Academy
 */

if (!defined('ABSPATH')) exit;

if ( ! function_exists( 'mp_academy_get_activity_date' ) ) {
	function mp_academy_get_activity_date( $user_id, $course_id, $post_id, $activity_type ) {
		if ( ! $user_id || ! $post_id || ! function_exists( 'learndash_get_user_activity' ) ) {
			return '';
		}

		$activity = learndash_get_user_activity(
			array(
				'user_id'       => $user_id,
				'course_id'     => $course_id,
				'post_id'       => $post_id,
				'activity_type' => $activity_type,
				'per_page'      => 1,
				'orderby'       => 'activity_updated',
				'order'         => 'DESC',
			)
		);

		$updated_raw = '';
		if ( is_array( $activity ) && ! empty( $activity ) ) {
			$first       = reset( $activity );
			$updated_raw = isset( $first->activity_updated ) ? $first->activity_updated : '';
		} elseif ( is_object( $activity ) ) {
			$updated_raw = isset( $activity->activity_updated ) ? $activity->activity_updated : '';
		}

		if ( ! $updated_raw ) {
			return '';
		}

		$timestamp = is_numeric( $updated_raw ) ? (int) $updated_raw : strtotime( $updated_raw );
		if ( $timestamp > 1000000000000 ) {
			$timestamp = (int) round( $timestamp / 1000 );
		}

		return $timestamp ? date_i18n( get_option( 'date_format' ), $timestamp ) : '';
	}
}

if ( ! function_exists( 'mp_academy_extract_lesson_prose' ) ) {
	/**
	 * Strip all LearnDash UI nodes from lesson content, returning only prose.
	 */
	function mp_academy_extract_lesson_prose( $html ) {
		if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
			return '';
		}

		$internal_errors = libxml_use_internal_errors( true );
		$doc             = new DOMDocument();
		$loaded          = $doc->loadHTML(
			'<?xml encoding="utf-8" ?><div id="mp-lesson-root">' . $html . '</div>',
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);

		if ( ! $loaded ) {
			libxml_clear_errors();
			libxml_use_internal_errors( $internal_errors );
			return '';
		}

		$xpath = new DOMXPath( $doc );

		// Strip all LearnDash UI chrome
		$strip = array(
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-breadcrumbs ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-progress ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-navigation ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-lesson-topic-list ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-item-list ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-course-step-back ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-layout__header ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-alert ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-status ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-topic-status ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' learndash-course-infobar ')]",
			"//*[@id='learndash_mark_complete_button']",
			"//form[@name='sfwd-mark-complete']",
		);

		foreach ( $strip as $selector ) {
			foreach ( $xpath->query( $selector ) as $node ) {
				if ( $node->parentNode ) {
					$node->parentNode->removeChild( $node );
				}
			}
		}

		$root      = $doc->getElementById( 'mp-lesson-root' );
		$prose     = '';

		if ( $root instanceof DOMElement ) {
			// Walk into .learndash-wrapper > .ld-tab-content if present
			$wrapper = $xpath->query(
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' learndash-wrapper ')]"
			)->item( 0 );

			$source = $wrapper instanceof DOMElement ? $wrapper : $root;

			foreach ( $source->childNodes as $child ) {
				$fragment = $doc->saveHTML( $child );
				if ( '' !== trim( strip_tags( $fragment ) ) ) {
					$prose .= $fragment;
				}
			}
		}

		libxml_clear_errors();
		libxml_use_internal_errors( $internal_errors );

		return trim( $prose );
	}
}

$lesson_id = get_the_ID();
$user_id   = get_current_user_id();
$course_id = function_exists('learndash_get_course_id') ? (int) learndash_get_course_id($lesson_id) : 0;
$course_url = $course_id ? get_permalink( $course_id ) : '';
$is_enrolled = $user_id && $course_id ? mp_ld_is_enrolled( $user_id, $course_id ) : false;
$lesson_ids = $course_id ? mp_ld_get_course_lesson_ids( $course_id, $user_id ) : array();
$can_access_lesson = ! $is_enrolled || ! $course_id ? true : mp_ld_can_access_lesson( $user_id, $course_id, $lesson_id, $lesson_ids );

if ( $is_enrolled && ! $can_access_lesson ) {
	wp_safe_redirect( $course_url ?: home_url( '/' ) );
	exit;
}

get_header();

// Topics for this lesson
$lesson_topics = [];
if ( $course_id && function_exists('learndash_get_topic_list') ) {
	$lesson_topics = learndash_get_topic_list($lesson_id, $course_id);
}
$steps_total = is_array($lesson_topics) ? count($lesson_topics) : 0;
$steps_done  = 0;

if ( $steps_total && $user_id && function_exists('learndash_is_topic_complete') ) {
	foreach ( $lesson_topics as $t ) {
		$tid = is_object($t) ? (int) $t->ID : (int) $t;
		if ( $course_id ? learndash_is_topic_complete($user_id, $tid, $course_id) : learndash_is_topic_complete($user_id, $tid) ) {
			$steps_done++;
		}
	}
}

$is_completed = false;
if ( $user_id && function_exists('learndash_is_lesson_complete') ) {
	$is_completed = (bool) learndash_is_lesson_complete($user_id, $lesson_id, $course_id);
}

if ( $steps_total > 0 ) {
	$is_completed = ( $steps_done >= $steps_total );
}

$progress_percent = $steps_total
	? (int) round(($steps_done / $steps_total) * 100)
	: ($is_completed ? 100 : 0);

$last_activity = mp_academy_get_activity_date($user_id, $course_id, $lesson_id, 'lesson');

$lesson_step_status = function_exists('mp_get_step_status')
	? mp_get_step_status($user_id, $course_id, $lesson_id, 'lesson')
	: [];

$step_status = 'not-started';
if ( $is_completed ) {
	$step_status = 'complete';
} elseif ( $steps_done > 0 || $progress_percent > 0 || ! empty($lesson_step_status['started']) ) {
	$step_status = 'in-progress';
}

get_template_part('template-parts/lesson/single/hero', null, [
	'post_id'        => $lesson_id,
	'lesson_id'      => $lesson_id,
	'course_id'      => $course_id,
	'course_url'     => $course_url,
	'show_eyebrow'   => true,
	'step_type'      => 'lesson',
	'step_status'    => $step_status,
	'is_completed'   => $is_completed,
	'topic_progress' => [
		'percent'       => $progress_percent,
		'steps_done'    => $steps_done,
		'steps_total'   => $steps_total,
		'last_activity' => $last_activity,
	],
]);
?>

<main id="primary" class="site-main u-wrap u-margin-top-xl u-margin-bottom-xl">
	<div class="mp-single-lesson">
		<div class="u-wrap--content">
			<?php while ( have_posts() ) : the_post();

				// Use raw post content to skip LearnDash's content filter injection
				$raw_content = get_post_field('post_content', $lesson_id);
				$prose       = $raw_content ? wpautop(do_shortcode($raw_content)) : '';
			?>

			<?php if ($prose): ?>
				<div class="mp-lesson-content o-prose u-flow--prose u-margin-top-lg">
					<?php echo $prose; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($lesson_topics)): ?>
				<section class="mp-lesson-topics u-margin-top-xl">
					<h2 class="mp-lesson-topics__title"><?php esc_html_e('Topics', 'mp-academy'); ?></h2>

					<ul class="mp-topic-list mp-topic-list--lesson">
						<?php foreach ($lesson_topics as $topic):
							$topic_id      = is_object($topic) ? (int) $topic->ID : (int) $topic;
							$topic_title   = get_the_title($topic_id);
							$topic_link    = get_permalink($topic_id);
							$topic_excerpt = trim(get_post_field('post_excerpt', $topic_id));
							$topic_can_access = $is_enrolled ? mp_ld_can_access_topic( $user_id, $course_id, $lesson_id, $topic_id ) : false;

							$topic_complete = false;
							if ($user_id && function_exists('learndash_is_topic_complete')) {
								$topic_complete = $course_id
									? (bool) learndash_is_topic_complete($user_id, $topic_id, $course_id)
									: (bool) learndash_is_topic_complete($user_id, $topic_id);
							}

						?>
							<li class="mp-topic-item">
								<?php if ($is_enrolled && $topic_link && $topic_can_access): ?>
									<a href="<?php echo esc_url($topic_link); ?>" class="mp-topic-link">
								<?php else: ?>
									<span class="mp-topic-link mp-topic-link--disabled">
								<?php endif; ?>

									<?php if ($topic_complete): ?>
										<span class="mp-topic-icon mp-topic-icon--complete" aria-label="<?php esc_attr_e('Complete', 'mp-academy'); ?>">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
												<path d="M3.5 8L6.5 11L12.5 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</span>
									<?php else: ?>
										<span class="mp-topic-icon" aria-hidden="true"></span>
									<?php endif; ?>

									<span class="mp-topic-text">
										<span class="mp-topic-title"><?php echo esc_html($topic_title); ?></span>
										<?php if ($topic_excerpt !== ''): ?>
											<span class="mp-topic-excerpt u-step--1 u-blue"><?php echo esc_html($topic_excerpt); ?></span>
										<?php endif; ?>
									</span>

								<?php if ($is_enrolled && $topic_link): ?>
									</a>
								<?php else: ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php endwhile; ?>
		</div>
	</div>
</main>

<?php get_footer();
