<?php
/**
 * Template for displaying search results pages.
 *
 * @package MP_Academy
 */

get_header();

$search_query = get_search_query();
$grouped_results = array(
	'sfwd-courses' => array(),
	'video'        => array(),
	'post'         => array(),
);

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$post_type = get_post_type();

		if ( ! isset( $grouped_results[ $post_type ] ) ) {
			$grouped_results[ $post_type ] = array();
		}

		$grouped_results[ $post_type ][] = get_post();
	}

	wp_reset_postdata();
}

$sections = array(
	'sfwd-courses' => __( 'Courses', 'mp-academy' ),
	'video'        => __( 'Videos', 'mp-academy' ),
	'post'         => __( 'Articles', 'mp-academy' ),
);

$result_count = 0;
foreach ( $grouped_results as $items ) {
	$result_count += count( $items );
}

/**
 * Build a short result description.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function mp_academy_search_result_description( WP_Post $post ) {
	$excerpt = trim( wp_strip_all_tags( get_the_excerpt( $post ) ) );

	if ( '' !== $excerpt ) {
		return $excerpt;
	}

	return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post ) ), 28 );
}

/**
 * Build the pill label for a search result.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function mp_academy_search_result_tag( WP_Post $post ) {
	switch ( get_post_type( $post ) ) {
		case 'sfwd-courses':
			return __( 'Course', 'mp-academy' );
		case 'video':
			return __( 'Video', 'mp-academy' );
		default:
			return __( 'Article', 'mp-academy' );
	}
}

/**
 * Build a readable URL path for search cards.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function mp_academy_search_result_path( WP_Post $post ) {
	$path = wp_parse_url( get_permalink( $post ), PHP_URL_PATH );

	if ( ! is_string( $path ) || '' === $path ) {
		return '/';
	}

	return untrailingslashit( $path );
}
?>

<main id="primary" class="site-main u-wrap mp-search-results-page">
	<?php if ( $result_count > 0 ) : ?>
		<header class="mp-search-results-page__header">
			<h1 class="c-h mp-search-results-page__title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Search results for: %s', 'mp-academy' ),
					'<span>' . esc_html( $search_query ) . '</span>'
				);
				?>
			</h1>
		</header>

		<div class="mp-search-results-page__groups">
			<?php foreach ( $sections as $post_type => $label ) : ?>
				<?php if ( empty( $grouped_results[ $post_type ] ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>

				<section class="mp-search-results-group" aria-labelledby="mp-search-group-<?php echo esc_attr( $post_type ); ?>">
					<header class="mp-search-results-group__header">
						<h2 id="mp-search-group-<?php echo esc_attr( $post_type ); ?>" class="c-h c-h--step-2">
							<?php echo esc_html( $label ); ?>
						</h2>
					</header>

					<div class="mp-search-results-group__list">
						<?php foreach ( $grouped_results[ $post_type ] as $result_post ) : ?>
							<article class="mp c-search-result-card">
								<header class="c-search-result-card__header">
									<h3 class="c-search-result-card__title">
										<a href="<?php echo esc_url( get_permalink( $result_post ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $result_post ) ); ?>">
											<?php echo esc_html( get_the_title( $result_post ) ); ?>
										</a>
									</h3>
									<div class="c-search-result-card__tag">
										<span class="mp c-pill c-pill--blue"><?php echo esc_html( mp_academy_search_result_tag( $result_post ) ); ?></span>
									</div>
								</header>
								<div class="c-search-result-card__body">
									<p class="c-search-result-card__description">
										<?php echo esc_html( mp_academy_search_result_description( $result_post ) ); ?>
									</p>
									<span class="c-search-result-card__url">
										<?php echo esc_html( mp_academy_search_result_path( $result_post ) ); ?>
									</span>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>

<?php
get_footer();
