<?php
/**
 * Template: Single Video (CPT: video)
 * Franklin Design System
 *
 * ACF Fields:
 * - video_url
 * - video_description
 *
 * ACF Taxonomy:
 * - technology
 *
 * @package MP_Academy
 */

if (!defined('ABSPATH')) exit;

get_header();

function mp_get_youtube_id_from_url($url) {
	if (!$url) return '';
	$url = trim($url);

	// youtu.be/ID
	if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];

	// youtube.com/watch?v=ID
	$parts = wp_parse_url($url);
	if (!empty($parts['query'])) {
		parse_str($parts['query'], $q);
		if (!empty($q['v'])) return preg_replace('/[^A-Za-z0-9_-]/', '', $q['v']);
	}

	// youtube.com/embed/ID
	if (preg_match('~/embed/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];

	return '';
}
?>

<main id="primary" class="site-main u-wrap u-margin-top-xl u-margin-bottom-xl">
	<div class="u-wrap--content mp-single-video">

		<?php while (have_posts()) : the_post(); ?>
			<?php
			$post_id = get_the_ID();

			// Breadcrumbs component (we will fix its logic later; leave structure consistent now)
			get_template_part('template-parts/components/breadcrumbs', null, [
				'post_id' => $post_id,
				'context' => 'video',
			]);

			$video_url = function_exists('get_field') ? (string) get_field('video_url', $post_id) : '';
			if (!$video_url) $video_url = (string) get_post_meta($post_id, 'video_url', true);

			$video_description = function_exists('get_field') ? (string) get_field('video_description', $post_id) : '';
			if (!$video_description) $video_description = (string) get_post_meta($post_id, 'video_description', true);

			$yt_id = mp_get_youtube_id_from_url($video_url);

			$tech_terms = get_the_terms($post_id, 'technology');
			?>

			<header class="mp-video-header u-margin-bottom-lg">
				<h1 class="mp-video-title u-margin-bottom-sm"><?php the_title(); ?></h1>

				<?php if (!empty($tech_terms) && !is_wp_error($tech_terms)) : ?>
					<div class="mp-video-meta u-margin-top-sm">
						<span class="mp c-eyebrow mp-video-pill">
							<span class="mp-video-pill__label"><?php esc_html_e('Technology:', 'mp-academy'); ?></span>
							<?php
							$names = array_map(static fn($t) => $t->name, $tech_terms);
							echo esc_html(implode(', ', $names));
							?>
						</span>
					</div>
				<?php endif; ?>
			</header>

			<?php if ($yt_id) : ?>
				<div class="mp-video-player u-margin-bottom-lg">
					<div class="mp-video-embed">
						<iframe
							src="<?php echo esc_url('https://www.youtube-nocookie.com/embed/' . $yt_id); ?>"
							title="<?php echo esc_attr(get_the_title()); ?>"
							frameborder="0"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
							allowfullscreen></iframe>
					</div>
				</div>
			<?php else : ?>
				<?php if (!empty($video_url)) : ?>
					<p class="mp-video-fallback-link">
						<a href="<?php echo esc_url($video_url); ?>" target="_blank" rel="noopener">Watch video</a>
					</p>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (!empty(trim(wp_strip_all_tags($video_description)))) : ?>
				<section class="mp-video-description o-prose u-flow--prose">
					<?php echo apply_filters('the_content', $video_description); ?>
				</section>
			<?php endif; ?>

			<?php if (trim(get_the_content())) : ?>
				<section class="mp-video-content o-prose u-flow--prose u-margin-top-lg">
					<?php the_content(); ?>
				</section>
			<?php endif; ?>

		<?php endwhile; ?>

	</div>
</main>

<?php get_footer(); ?>
