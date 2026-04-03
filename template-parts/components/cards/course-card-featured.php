<?php
/**
 * Component: MP Academy – Featured Course Card
 * Shows progress bar if user is enrolled
 *
 * Used in: Featured courses section, archive listings, etc.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Allow passing course_id via get_template_part args.
$course_id = isset( $args['course_id'] ) ? (int) $args['course_id'] : get_the_ID();
if ( ! $course_id ) {
	return;
}

$user_id   = get_current_user_id();
$title     = get_the_title( $course_id );
$permalink = get_permalink( $course_id );

// Image (fallback placeholder).
$image_url = get_the_post_thumbnail_url( $course_id, 'large' );
if ( ! $image_url ) {
	$image_url = get_template_directory_uri() . '/assets/images/placeholder.jpg';
}

// Short description.
$short_desc = get_post_meta( $course_id, '_learndash_course_grid_short_description', true );
if ( empty( $short_desc ) ) {
	$short_desc = wp_trim_words( get_post_field( 'post_content', $course_id ), 25 );
}

// Tag label: course category if available, else generic label.
$cat_label = '';
$terms     = get_the_terms( $course_id, 'ld_course_category' );
if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms ) ) {
	$cat_label = $terms[0]->name;
} else {
	$cat_label = __( 'Video training course', 'mp-academy' );
}

// Check if we should show progress bar (only on archive page)
$show_progress = isset( $args['show_progress'] ) ? (bool) $args['show_progress'] : false;

// Check if user is enrolled (only if we need to show progress)
$is_enrolled = false;
if ( $show_progress && $user_id && function_exists( 'sfwd_lms_has_access' ) ) {
	$is_enrolled = sfwd_lms_has_access( $course_id, $user_id );
}
?>

<article class="mp c-card c-card--layout-single c-card--size-medium c-card--alt c-card--has-tag c-card--has-image mp-course-card--featured">
	<div class="c-card__wrapper">
		<figure class="c-card__image">
			<a href="<?php echo esc_url( $permalink ); ?>">
				<img
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $title ); ?>"
					loading="lazy"
				/>
			</a>
			<span class="c-card__tag">
				<?php echo esc_html( $cat_label ); ?>
			</span>
		</figure>

		<div class="c-card__primary">
			<header class="c-card__header u-flow--2xs">
				<h3 class="c-h c-card__title">
					<a href="<?php echo esc_url( $permalink ); ?>">
						<?php echo esc_html( $title ); ?>
					</a>
				</h3>
			</header>

			<div class="c-card__content u-flow">
				<p class="mp-course-card__excerpt">
					<?php echo esc_html( $short_desc ); ?>
				</p>

				<?php if ( $is_enrolled ) : ?>
					<!-- Show progress bar if user is enrolled -->
					<?php 
					get_template_part( 'template-parts/components/progress-bar', null, [
						'course_id' => $course_id,
						'user_id'   => $user_id,
						'context'   => 'card',
						'show_text' => true,
						'show_date' => false,
						'show_bar'  => true,
					] ); 
					?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>