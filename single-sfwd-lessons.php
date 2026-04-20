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
$lesson_id = get_the_ID();
$course_id = function_exists('learndash_get_course_id')
	? learndash_get_course_id($lesson_id)
	: 0;
$course_url = $course_id ? get_permalink($course_id) : '';

$topics = array();
if ( function_exists( 'learndash_get_topic_list' ) ) {
	$topics = learndash_get_topic_list( $lesson_id, $course_id );
}

$lesson_status = $user_id && $course_id ? mp_get_step_status( $user_id, $course_id, $lesson_id, 'lesson' ) : null;
$is_completed  = $lesson_status && ! empty( $lesson_status['complete'] );
$is_started    = $lesson_status && ! empty( $lesson_status['started'] );

$steps_total    = is_array( $topics ) ? count( $topics ) : 0;
$steps_done     = 0;
$last_activity  = mp_academy_get_activity_date( $user_id, $course_id, $lesson_id, 'lesson' );

if ( $steps_total > 0 && $user_id && $course_id ) {
	foreach ( $topics as $topic ) {
		$topic_id = is_object( $topic ) ? $topic->ID : (int) $topic;
		$status   = mp_get_step_status( $user_id, $course_id, $topic_id, 'topic' );

		if ( ! empty( $status['complete'] ) ) {
			$steps_done++;
		}

		if ( ! $last_activity ) {
			$last_activity = mp_academy_get_activity_date( $user_id, $course_id, $topic_id, 'topic' );
		}
	}
}

$progress_percent = $steps_total > 0 ? (int) round( ( $steps_done / $steps_total ) * 100 ) : ( $is_completed ? 100 : 0 );

get_template_part('template-parts/lesson/single/hero', null, [
	'post_id'                 => $lesson_id,
	'lesson_id'               => $lesson_id,
	'course_id'               => $course_id,
	'course_url'              => $course_url,
	'show_eyebrow'            => true,
	'is_completed'            => $is_completed,
	'is_started'              => $is_started,
	'status_complete_label'   => __('Lesson completed', 'mp-academy'),
	'status_in_progress_label'=> __('Lesson in progress', 'mp-academy'),
	'status_not_started_label'=> __('Lesson not started', 'mp-academy'),
	'topic_progress'          => array(
		'percent'       => $progress_percent,
		'steps_done'    => $steps_done,
		'steps_total'   => $steps_total,
		'last_activity' => $last_activity,
	),
]);

function mp_academy_strip_lesson_ui_nodes( $html ) {
	if ( '' === trim( (string) $html ) ) {
		return '';
	}

	$previous = libxml_use_internal_errors( true );

	$dom = new DOMDocument();
	$dom->loadHTML(
		'<?xml encoding="utf-8" ?><div id="mp-lesson-root">' . $html . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);

	$xpath = new DOMXPath( $dom );
	$queries = array(
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-layout__header ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-progress ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-progress-inline ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-lesson-topics-list ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' learndash_topic_dots ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-topic-list ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' ld-table-list ')]",
	);

	foreach ( $queries as $query ) {
		$nodes = $xpath->query( $query );

		if ( ! $nodes ) {
			continue;
		}

		foreach ( iterator_to_array( $nodes ) as $node ) {
			if ( $node->parentNode ) {
				$node->parentNode->removeChild( $node );
			}
		}
	}

	$root = $dom->getElementById( 'mp-lesson-root' );
	$output = '';

	if ( $root ) {
		foreach ( $root->childNodes as $child ) {
			$output .= $dom->saveHTML( $child );
		}
	}

	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	return $output;
}
?>

<main id="primary" class="site-main u-wrap u-margin-top-xl u-margin-bottom-xl">
	<div class="mp-single-lesson">
		<div class="u-wrap--content">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php $lesson_content = mp_academy_strip_lesson_ui_nodes( apply_filters( 'the_content', get_the_content() ) ); ?>
			<div class="mp-lesson-content">
				<h2 class="mp-section-title"><?php esc_html_e( 'Lesson content', 'mp-academy' ); ?></h2>
				<?php echo $lesson_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php if ( ! empty( $topics ) ) : ?>
				<section class="mp-lesson-topics u-margin-top-xl">
					<h2 class="mp-section-title"><?php esc_html_e( 'Topics', 'mp-academy' ); ?></h2>
					<ul class="mp-topic-list mp-topic-list--lesson">
						<?php foreach ( $topics as $topic ) : ?>
							<?php
							$topic_id     = is_object( $topic ) ? $topic->ID : (int) $topic;
							$topic_title  = get_the_title( $topic_id );
							$topic_link   = get_permalink( $topic_id );
							$topic_status = $user_id && $course_id ? mp_get_step_status( $user_id, $course_id, $topic_id, 'topic' ) : null;
							?>
							<li class="mp-topic-item">
								<a href="<?php echo esc_url( $topic_link ); ?>" class="mp-topic-link">
									<?php if ( $topic_status && ! empty( $topic_status['complete'] ) ) : ?>
										<span class="mp-topic-icon mp-topic-icon--complete" aria-hidden="true">✓</span>
									<?php else : ?>
										<span class="mp-topic-icon" aria-hidden="true"></span>
									<?php endif; ?>

									<span class="mp-topic-text">
										<span class="mp-topic-title"><?php echo esc_html( $topic_title ); ?></span>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>
		<?php endwhile; ?>
		</div>
	</div>
</main>

<?php
get_footer();
