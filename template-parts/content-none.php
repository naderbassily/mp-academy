<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package MP_Academy
 */

?>

<section class="no-results not-found u-wrap">
	<?php if ( ! is_search() ) : ?>
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'mp-academy' ); ?></h1>
		</header><!-- .page-header -->
	<?php endif; ?>

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'mp-academy' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<div class="mp-search-empty u-text-center">
				<img
					class="mp-search-empty__icon"
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/no-courses.svg' ); ?>"
					alt=""
					loading="lazy"
				/>
				<h2 class="c-h c-h--step-2 mp-search-empty__title"><?php esc_html_e( 'No results', 'mp-academy' ); ?></h2>
				<p class="mp-search-empty__copy">
					<?php esc_html_e( 'Sorry, we couldn’t find anything for your search.', 'mp-academy' ); ?>
				</p>
				<p class="mp-search-empty__actions">
					<a class="mp c-button c-button--inline c-button--outline-green" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php esc_html_e( 'Back to homepage', 'mp-academy' ); ?>
					</a>
				</p>
			</div>

			<?php
		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'mp-academy' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
