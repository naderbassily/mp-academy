<?php
/**
 * Template part: MP Academy — Featured Courses Grid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Course archive URL (Browse all courses).
$archive_url = get_post_type_archive_link( 'sfwd-courses' );
?>
<section class="mp mp-featured-courses mp-section" aria-labelledby="mp-featured-courses-title">
	<div class="u-wrap">
		<header class="mp-featured-courses__hd">
			<p class="c-h c-h--step-4">
				<?php esc_html_e( 'Featured courses', 'mp-academy' ); ?>
</p>

			<?php if ( $archive_url ) : ?>
				<a class=" mp-featured-courses__browse u-blue" href="<?php echo esc_url( $archive_url ); ?>">
					<span class="mp-featured-courses__browse-icon" aria-hidden="true">→</span>
					<span><?php esc_html_e( 'Browse all courses', 'mp-academy' ); ?></span>
				</a>
			<?php endif; ?>
		</header>

		<?php
		// Simple featured list: first 4 published courses A–Z.
		// (Later you can add a "featured" flag/taxonomy if needed.)
		$args = [
			'post_type'      => 'sfwd-courses',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'orderby'        => 'title',
			'order'          => 'ASC',
		];

		$featured_query = new WP_Query( $args );
		?>

		<?php if ( $featured_query->have_posts() ) : ?>
			<div class=" o-grid o-grid--of-four">
				<?php
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();
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
		<?php else : ?>
			<p class="mp-featured-courses__note">
				<?php esc_html_e( 'No featured courses available at the moment.', 'mp-academy' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>