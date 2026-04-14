<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package MP_Academy
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="mp-404 u-wrap" aria-labelledby="mp-404-title">
		<div class="mp-404__panel">
			<div class="mp-404__content">
				<span class="mp-404__eyebrow"><?php esc_html_e( '404 error', 'mp-academy' ); ?></span>
				<h1 id="mp-404-title" class="mp-404__title"><?php esc_html_e( 'Page not found.', 'mp-academy' ); ?></h1>
				<p class="mp-404__copy">
					<?php esc_html_e( 'The page you requested is unavailable or may have moved. Continue to one of the main MP Academy sections below, or search the site.', 'mp-academy' ); ?>
				</p>

				<div class="mp-404__actions">
					<a class="c-button c-button--blue" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php esc_html_e( 'Go to homepage', 'mp-academy' ); ?>
					</a>
					<a class="c-button c-button--outline-green" href="<?php echo esc_url( get_post_type_archive_link( 'sfwd-courses' ) ?: home_url( '/courses/' ) ); ?>">
						<?php esc_html_e( 'Browse courses', 'mp-academy' ); ?>
					</a>
				</div>

				<div class="mp-404__search" role="search">
					<p class="mp-404__search-title"><?php esc_html_e( 'Search MP Academy', 'mp-academy' ); ?></p>
					<?php get_search_form(); ?>
				</div>

			</div>
		</div>
	</section>
</main>

<?php
get_footer();
