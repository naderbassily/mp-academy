<?php
/**
 * Template: LearnDash Single Topic
 *
 * @package MP_Academy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mp_academy_get_topic_navigation_url' ) ) {
	/**
	 * Resolve a LearnDash step navigation URL.
	 *
	 * @param int $course_id Course ID.
	 * @param int $topic_id  Topic ID.
	 * @param int $step_id   Previous/next step ID.
	 * @return string
	 */
	function mp_academy_get_topic_navigation_url( $course_id, $topic_id, $step_id ) {
		if ( empty( $course_id ) || empty( $topic_id ) || empty( $step_id ) ) {
			return '';
		}

		return (string) get_permalink( $step_id );
	}
}

if ( ! function_exists( 'mp_academy_strip_topic_ui_nodes' ) ) {
	/**
	 * Remove LearnDash UI fragments that are replaced by the theme.
	 *
	 * @param DOMXPath $xpath Document xpath helper.
	 * @return void
	 */
	function mp_academy_strip_topic_ui_nodes( DOMXPath $xpath ) {
		$selectors = array(
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-breadcrumbs ')]",
			"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-progress ')]",
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

if ( ! function_exists( 'mp_academy_extract_topic_content_parts' ) ) {
	/**
	 * Split rendered topic content into video and body fragments.
	 *
	 * @param string $html Rendered topic content.
	 * @return array{video:string,body:string}
	 */
	function mp_academy_extract_topic_content_parts( $html ) {
		if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
			return array(
				'video' => '',
				'body'  => $html,
			);
		}

		$internal_errors = libxml_use_internal_errors( true );
		$document        = new DOMDocument();
		$loaded          = $document->loadHTML(
			'<?xml encoding="utf-8" ?><div id="mp-topic-root">' . $html . '</div>',
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
		mp_academy_strip_topic_ui_nodes( $xpath );

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

		$root      = $document->getElementById( 'mp-topic-root' );
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

if ( ! function_exists( 'mp_academy_get_topic_video_settings' ) ) {
	/**
	 * Read LearnDash video settings for a topic safely.
	 *
	 * @param int $topic_id Topic ID.
	 * @return array<string,string>
	 */
	function mp_academy_get_topic_video_settings( $topic_id ) {
		$keys     = array(
			'lesson_video_enabled'       => 'progression',
			'lesson_video_auto_start'    => 'autostart',
			'lesson_video_focus_pause'   => 'focus_pause',
			'lesson_video_track_video'   => 'resume',
			'lesson_video_show_controls' => 'controls',
			'lesson_video_auto_complete' => 'auto_complete',
		);
		$settings = array_fill_keys( array_values( $keys ), '' );

		if ( ! function_exists( 'learndash_get_setting' ) ) {
			return $settings;
		}

		foreach ( $keys as $setting_key => $mapped_key ) {
			$settings[ $mapped_key ] = (string) learndash_get_setting( $topic_id, $setting_key );
		}

		return $settings;
	}
}

if ( ! function_exists( 'mp_academy_get_activity_date' ) ) {
	/**
	 * Resolve a LearnDash activity date for a step.
	 *
	 * @param int    $user_id       User ID.
	 * @param int    $course_id     Course ID.
	 * @param int    $post_id       Step ID.
	 * @param string $activity_type LearnDash activity type.
	 * @return string
	 */
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

get_header();

$topic_id   = get_the_ID();
$user_id    = get_current_user_id();
$lesson_id  = function_exists( 'learndash_get_setting' ) ? (int) learndash_get_setting( $topic_id, 'lesson' ) : 0;
$course_id  = function_exists( 'learndash_get_course_id' ) ? (int) learndash_get_course_id( $topic_id ) : 0;
$course_url = $course_id ? get_permalink( $course_id ) : '';

$is_completed = false;
if ( function_exists( 'learndash_is_topic_complete' ) ) {
	$is_completed = $course_id ? (bool) learndash_is_topic_complete( $user_id, $topic_id, $course_id ) : (bool) learndash_is_topic_complete( $user_id, $topic_id );
} elseif ( function_exists( 'learndash_is_item_complete' ) ) {
	$is_completed = (bool) learndash_is_item_complete( $user_id, $topic_id, $course_id );
}

$prev_id             = function_exists( 'learndash_get_previous_step_id' ) ? (int) learndash_get_previous_step_id( $course_id, $topic_id ) : 0;
$next_id             = function_exists( 'learndash_get_next_step_id' ) ? (int) learndash_get_next_step_id( $course_id, $topic_id ) : 0;
$prev_url            = mp_academy_get_topic_navigation_url( $course_id, $topic_id, $prev_id );
$next_url            = mp_academy_get_topic_navigation_url( $course_id, $topic_id, $next_id );
$topic_video_settings = mp_academy_get_topic_video_settings( $topic_id );
$lesson_topics        = ( $lesson_id && function_exists( 'learndash_get_topic_list' ) ) ? learndash_get_topic_list( $lesson_id, $course_id ) : array();
$lesson_step_total    = is_array( $lesson_topics ) ? count( $lesson_topics ) : 0;
$lesson_step_complete = 0;

if ( $lesson_step_total && $user_id && function_exists( 'learndash_is_topic_complete' ) ) {
	foreach ( $lesson_topics as $lesson_topic ) {
		$lesson_topic_id = is_object( $lesson_topic ) ? (int) $lesson_topic->ID : (int) $lesson_topic;

		if ( $course_id ? learndash_is_topic_complete( $user_id, $lesson_topic_id, $course_id ) : learndash_is_topic_complete( $user_id, $lesson_topic_id ) ) {
			$lesson_step_complete++;
		}
	}
}

$lesson_progress_percent = $lesson_step_total ? (int) round( ( $lesson_step_complete / $lesson_step_total ) * 100 ) : ( $is_completed ? 100 : 0 );
$last_activity           = mp_academy_get_activity_date( $user_id, $course_id, $topic_id, 'topic' );

if ( ! $last_activity && $lesson_id ) {
	$last_activity = mp_academy_get_activity_date( $user_id, $course_id, $lesson_id, 'lesson' );
}

get_template_part(
	'template-parts/lesson/single/hero',
	null,
	array(
		'post_id'        => $topic_id,
		'lesson_id'      => $lesson_id,
		'course_id'      => $course_id,
		'show_eyebrow'   => true,
		'is_completed'   => $is_completed,
		'topic_progress' => array(
			'percent'       => $lesson_progress_percent,
			'steps_done'    => $lesson_step_complete,
			'steps_total'   => $lesson_step_total,
			'last_activity' => $last_activity,
		),
	)
);
?>

<main id="primary" class="site-main u-wrap u-space--section u-flow u-margin-top-xl">
	<header class="u-flow--s">
		<div class="u-display-flex u-flex-wrap u-gap-s u-align-items-center u-margin-top-xl u-justify-content-between">
			<span class="u-margin-left-auto"></span>

			<?php if ( $prev_url ) : ?>
				<a href="<?php echo esc_url( $prev_url ); ?>" class="mp c-button c-button--outline-green">← Previous Topic</a>
			<?php endif; ?>

			<?php if ( $next_url ) : ?>
				<a href="<?php echo esc_url( $next_url ); ?>" class="mp c-button c-button--outline-green">Next Topic →</a>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php
			$content_parts = mp_academy_extract_topic_content_parts( apply_filters( 'the_content', get_the_content() ) );
			$video_block   = $content_parts['video'];
			$body_block    = $content_parts['body'];
			?>

			<?php if ( $video_block ) : ?>
				<section
					class="mp-topic-video-wrapper"
					data-ld-topic-id="<?php echo esc_attr( $topic_id ); ?>"
					data-ld-is-complete="<?php echo esc_attr( $is_completed ? '1' : '0' ); ?>"
					data-ld-progression="<?php echo esc_attr( $topic_video_settings['progression'] ); ?>"
					data-ld-autostart="<?php echo esc_attr( $topic_video_settings['autostart'] ); ?>"
					data-ld-focus-pause="<?php echo esc_attr( $topic_video_settings['focus_pause'] ); ?>"
					data-ld-resume="<?php echo esc_attr( $topic_video_settings['resume'] ); ?>"
					data-ld-controls="<?php echo esc_attr( $topic_video_settings['controls'] ); ?>"
					data-ld-auto-complete="<?php echo esc_attr( $topic_video_settings['auto_complete'] ); ?>"
				>
					<?php echo $video_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>
			<?php endif; ?>

			<div class="u-wrap">
				<?php echo $body_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php if ( $course_url ) : ?>
				<div class="u-wrap u-space--section u-flow mp-topic-back-link-wrap">
					<p>
						<a href="<?php echo esc_url( $course_url ); ?>" class="mp-topic-back-link c-button c-button--outline-green">
							← Back to course overview
						</a>
					</p>
				</div>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
