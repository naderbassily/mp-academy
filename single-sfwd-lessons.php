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

if ( ! function_exists( 'mp_academy_strip_lesson_ui_nodes' ) ) {
	/**
	 * Remove LearnDash lesson fragments replaced by the theme.
	 *
	 * @param DOMXPath $xpath Document xpath helper.
	 * @return void
	 */
	function mp_academy_strip_lesson_ui_nodes( DOMXPath $xpath ) {
			$selectors = array(
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-breadcrumbs ')]",
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-progress ')]",
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-lesson-topic-list ')]",
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-item-list ')]",
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-status ')]",
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-topic-status ')]",
				"//*[contains(concat(' ', normalize-space(@class), ' '), ' learndash-course-infobar ')]",
				"//div[@id='ld-tab-panel-content' and not(normalize-space()) and not(*)]",
			);

		foreach ( $selectors as $selector ) {
			foreach ( $xpath->query( $selector ) as $node ) {
				if ( $node->parentNode ) {
					$node->parentNode->removeChild( $node );
				}
			}
		}
	}
}

if ( ! function_exists( 'mp_academy_extract_lesson_content_parts' ) ) {
	/**
	 * Split rendered lesson content into video and body fragments.
	 *
	 * @param string $html Rendered lesson content.
	 * @return array{video:string,body:string}
	 */
	function mp_academy_extract_lesson_content_parts( $html ) {
		if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
			return array(
				'video' => '',
				'body'  => $html,
			);
		}

		$internal_errors = libxml_use_internal_errors( true );
		$document        = new DOMDocument();
		$loaded          = $document->loadHTML(
			'<?xml encoding="utf-8" ?><div id="mp-lesson-root">' . $html . '</div>',
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);

		if ( ! $loaded ) {
			libxml_clear_errors();
			libxml_use_internal_errors( $internal_errors );

			return array(
				'video' => '',
				'body'  => $html,
			);
		}

		$xpath = new DOMXPath( $document );
		mp_academy_strip_lesson_ui_nodes( $xpath );

		$video_html = '';
		$video_node = $xpath->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-video ')]" )->item( 0 );

		if ( $video_node instanceof DOMNode ) {
			$container = $video_node->parentNode;

			if ( $container instanceof DOMElement && 1 === $container->childNodes->length ) {
				$video_node = $container;
			}

			$video_html = $document->saveHTML( $video_node );

			if ( $video_node->parentNode ) {
				$video_node->parentNode->removeChild( $video_node );
			}
		}

		$root      = $document->getElementById( 'mp-lesson-root' );
		$body_html = '';

		if ( $root instanceof DOMElement ) {
			foreach ( $root->childNodes as $child ) {
				$body_html .= $document->saveHTML( $child );
			}
		} else {
			$body_html = $html;
		}

		libxml_clear_errors();
		libxml_use_internal_errors( $internal_errors );

		return array(
			'video' => $video_html,
			'body'  => $body_html,
		);
	}
}

if ( ! function_exists( 'mp_academy_get_lesson_navigation_url' ) ) {
	/**
	 * Resolve a LearnDash lesson navigation URL.
	 *
	 * @param int $course_id Course ID.
	 * @param int $lesson_id Lesson ID.
	 * @param int $step_id   Previous/next step ID.
	 * @return string
	 */
	function mp_academy_get_lesson_navigation_url( $course_id, $lesson_id, $step_id ) {
		if ( empty( $course_id ) || empty( $lesson_id ) || empty( $step_id ) ) {
			return '';
		}

		return (string) get_permalink( $step_id );
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
$is_standalone_lesson = mp_ld_is_standalone_lesson( $lesson_id, $course_id );
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

$prev_id = 0;
$next_id = 0;

if ( $is_standalone_lesson && ! empty( $lesson_ids ) ) {
	$lesson_ids    = array_map( 'intval', $lesson_ids );
	$current_index = array_search( (int) $lesson_id, $lesson_ids, true );

	if ( false !== $current_index ) {
		if ( isset( $lesson_ids[ $current_index - 1 ] ) ) {
			$prev_id = (int) $lesson_ids[ $current_index - 1 ];
		}

		if ( isset( $lesson_ids[ $current_index + 1 ] ) ) {
			$next_id = (int) $lesson_ids[ $current_index + 1 ];
		}
	}
} else {
	$prev_id = function_exists( 'learndash_get_previous_step_id' ) ? (int) learndash_get_previous_step_id( $course_id, $lesson_id ) : 0;
	$next_id = function_exists( 'learndash_get_next_step_id' ) ? (int) learndash_get_next_step_id( $course_id, $lesson_id ) : 0;
}

$prev_url               = mp_academy_get_lesson_navigation_url( $course_id, $lesson_id, $prev_id );
$next_url               = mp_academy_get_lesson_navigation_url( $course_id, $lesson_id, $next_id );
$lesson_video_settings  = mp_ld_get_step_video_settings( $lesson_id );

$should_lock_next_step = $is_standalone_lesson && ! $is_completed;

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

<main
	id="primary"
	class="site-main u-wrap<?php echo $is_standalone_lesson ? ' u-space--section u-flow u-margin-top-xl' : ' u-margin-top-xl u-margin-bottom-xl'; ?><?php echo $is_standalone_lesson ? ' mp-single-lesson--standalone' : ''; ?>"
	data-mp-step-complete="<?php echo esc_attr( $is_completed ? '1' : '0' ); ?>"
	data-mp-lock-next-step="<?php echo esc_attr( $should_lock_next_step ? '1' : '0' ); ?>"
>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( $is_standalone_lesson ) : ?>
			<?php
			$content_parts = mp_academy_extract_lesson_content_parts( apply_filters( 'the_content', get_the_content() ) );
			$video_block   = $content_parts['video'];
			$body_block    = $content_parts['body'];
			?>

			<?php if ( $video_block ) : ?>
				<section
					class="mp-topic-video-wrapper"
					data-ld-step-id="<?php echo esc_attr( $lesson_id ); ?>"
					data-ld-is-complete="<?php echo esc_attr( $is_completed ? '1' : '0' ); ?>"
					data-ld-progression="<?php echo esc_attr( $lesson_video_settings['enabled'] ); ?>"
					data-ld-autostart="<?php echo esc_attr( $lesson_video_settings['autostart'] ); ?>"
					data-ld-focus-pause="<?php echo esc_attr( $lesson_video_settings['focus_pause'] ); ?>"
					data-ld-resume="<?php echo esc_attr( $lesson_video_settings['resume'] ); ?>"
					data-ld-controls="<?php echo esc_attr( $lesson_video_settings['controls'] ); ?>"
					data-ld-auto-complete="<?php echo esc_attr( $lesson_video_settings['auto_complete'] ); ?>"
				>
					<?php echo $video_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>
			<?php endif; ?>

			<?php if ( trim( wp_strip_all_tags( $body_block ) ) !== '' ) : ?>
				<div class="u-wrap">
					<?php echo $body_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="mp-single-lesson">
				<div class="u-wrap--content">
					<?php
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
				</div>
			</div>
		<?php endif; ?>
	<?php endwhile; ?>
</main>

<?php get_footer();
