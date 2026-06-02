<?php
/**
 * Template Name: Contact / Support
 *
 * @package MP_Academy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$intro = has_excerpt()
	? get_the_excerpt()
	: __( 'Need help with a technical issue on Malvern Panalytical Academy?', 'mp-academy' );
?>

<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<section class="mp-contact-support-hero u-wrap">
			<div class="mp-contact-support-hero__inner">
				<?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>

				<h1 class="c-h mp-contact-support-hero__title">
					<?php the_title(); ?>
				</h1>

				<p class="mp-contact-support-hero__lede">
					<?php echo esc_html( $intro ); ?>
				</p>
			</div>
		</section>

		<section class="mp-contact-support u-wrap u-margin-bottom-xl" aria-labelledby="mp-contact-support-form-title">
			<div class="mp-contact-support__content">
				<?php if ( trim( get_the_content() ) ) : ?>
					<div class="mp-contact-support__copy entry-content">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>

				<div class="mp-contact-support__form">
					<h2 id="mp-contact-support-form-title" class="u-hidden">
						<?php esc_html_e( 'Support request form', 'mp-academy' ); ?>
					</h2>

					<p class="mp-contact-support__form-intro">
						<?php esc_html_e( 'If you are experiencing technical issues while using Malvern Panalytical Academy, please complete the form below and our team will assist you as soon as possible.', 'mp-academy' ); ?>
					</p>

					<div class="mp-contact-support__form-note">
						<p>
							<?php esc_html_e( 'This form is intended for technical issues related to the Malvern Panalytical Academy platform only, including course access, progress tracking, login issues, or website errors.', 'mp-academy' ); ?>
						</p>
						<p>
							<?php esc_html_e( 'For instrument inquiries, application support, or general business requests, please use our main', 'mp-academy' ); ?>
							<a href="https://www.malvernpanalytical.com/en/about-us/contact-us">
								<?php esc_html_e( 'Contact Us', 'mp-academy' ); ?>
							</a>
							<?php esc_html_e( 'page.', 'mp-academy' ); ?>
						</p>
					</div>

					<script type="text/javascript" src="https://form.jotform.com/jsform/261395421547157"></script>
				</div>
			</div>
		</section>

	<?php endwhile; ?>
</main>

<?php
get_footer();
