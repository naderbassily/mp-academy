<?php
/**
 * Component: MP Academy — Unified Progress Bar
 *
 * Can be reused in:
 * - My Courses cards
 * - Course pages
 * - Dashboards
 *
 * Args (via get_template_part $args):
 * - course_id (int)  Required
 * - user_id   (int)  Optional, defaults to current user
 * - context   (string) Optional, e.g. 'card', 'course-page'
 * - show_text (bool) Optional, default true
 * - show_date (bool) Optional, default true
 * - show_bar  (bool) Optional, default true
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Parse arguments
// -----------------------------------------------------------------------------
$course_id = isset( $args['course_id'] ) ? (int) $args['course_id'] : get_the_ID();
$user_id   = isset( $args['user_id'] )   ? (int) $args['user_id']   : get_current_user_id();
$context   = isset( $args['context'] )   ? sanitize_html_class( $args['context'] ) : 'card';

$show_text = array_key_exists( 'show_text', $args ) ? (bool) $args['show_text'] : true;
$show_date = array_key_exists( 'show_date', $args ) ? (bool) $args['show_date'] : true;
$show_bar  = array_key_exists( 'show_bar',  $args ) ? (bool) $args['show_bar']  : true;

if ( ! $course_id || ! $user_id ) {
	return;
}

// -----------------------------------------------------------------------------
// Progress percentage
// -----------------------------------------------------------------------------
$progress_pct = 0;

if ( function_exists( 'learndash_course_progress' ) ) {
	$prog = learndash_course_progress(
		[
			'user_id'   => $user_id,
			'course_id' => $course_id,
			'array'     => true,
		]
	);

	if ( is_array( $prog ) && isset( $prog['percentage'] ) ) {
		$progress_pct = $prog['percentage'];

		// Some LD versions return 0–1 float.
		if ( $progress_pct > 0 && $progress_pct <= 1 ) {
			$progress_pct = $progress_pct * 100;
		}
	}
}

// Clamp percentage between 0 and 100.
$progress_pct = (int) max( 0, min( 100, $progress_pct ) );

// -----------------------------------------------------------------------------
// Last activity date
// -----------------------------------------------------------------------------
$last_activity_str = '';

if ( $show_date && function_exists( 'learndash_get_user_activity' ) ) {
	$activity = learndash_get_user_activity(
		[
			'user_id'       => $user_id,
			'course_id'     => $course_id,
			'post_id'       => $course_id,
			'activity_type' => 'course',
			'per_page'      => 1,
			'orderby'       => 'activity_updated',
			'order'         => 'DESC',
		]
	);

	$updated_raw = '';

	if ( is_array( $activity ) && ! empty( $activity ) ) {
		$first       = reset( $activity );
		$updated_raw = isset( $first->activity_updated ) ? $first->activity_updated : '';
	} elseif ( is_object( $activity ) ) {
		$updated_raw = isset( $activity->activity_updated ) ? $activity->activity_updated : '';
	}

	if ( $updated_raw ) {
		if ( is_numeric( $updated_raw ) ) {
			$ts = (int) $updated_raw;

			// If timestamp is in ms, convert to seconds.
			if ( $ts > 1000000000000 ) {
				$ts = (int) round( $ts / 1000 );
			}
		} else {
			$ts = strtotime( $updated_raw );
		}

		if ( ! empty( $ts ) && $ts > 0 ) {
			$last_activity_str = date_i18n( get_option( 'date_format' ), $ts );
		}
	}
}

// -----------------------------------------------------------------------------
// Classes
// -----------------------------------------------------------------------------
$is_completed = ( $progress_pct >= 100 );

$wrapper_classes = [
	'mp-progress',
	'mp-progress--' . $context,
];

$progress_classes = [
	'c-progress',
];

$progress_classes[] = $is_completed ? 'is-complete' : 'is-incomplete';

?>
<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">

	<?php if ( $show_bar ) : ?>
		<div
			class="<?php echo esc_attr( implode( ' ', $progress_classes ) ); ?>"
			role="progressbar"
			aria-valuenow="<?php echo (int) $progress_pct; ?>"
			aria-valuemin="0"
			aria-valuemax="100"
			aria-label="<?php printf( esc_attr__( 'Course progress: %d%% complete', 'mp-academy' ), (int) $progress_pct ); ?>"
		>
			<div class="c-progress__track">
				<div
					class="c-progress__value"
					style="width: <?php echo (int) $progress_pct; ?>%;"
				></div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $show_text || ( $show_date && $last_activity_str ) ) : ?>
		<div class="mp-progress__meta">
			<?php if ( $show_text ) : ?>
				<span class="mp-progress__stat-left">
					<strong><?php echo (int) $progress_pct; ?>%</strong>
					<?php esc_html_e( 'complete', 'mp-academy' ); ?>
				</span>
			<?php endif; ?>

			<?php if ( $show_date && $last_activity_str ) : ?>
				<span class="mp-progress__stat-right ">
					<?php esc_html_e( 'Last activity:', 'mp-academy' ); ?>
					<?php echo esc_html( $last_activity_str ); ?>
				</span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>
