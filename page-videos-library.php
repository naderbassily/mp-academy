<?php
/**
 * Template Name: Videos Library
 * Franklin Design System — Hero Search (top) + Dynamic checkbox filters + Franklin c-card results
 *
 * CPT: video
 * ACF field: video_url
 * Taxonomies: any public UI taxonomies attached to "video" (e.g., technology)
 *
 * Query params:
 * - q=search
 * - size=n_12_n
 * - tax_{taxonomy}[]=term_slug (multi)
 * - pg=page
 *
 * @package MP_Academy
 */

if (!defined('ABSPATH')) exit;

get_header();

$post_type = 'video';

/* ---------- helpers ---------- */
function mp_get_youtube_id_from_url($url) {
	if (!$url) return '';
	$url = trim((string) $url);

	if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];

	$parts = wp_parse_url($url);
	if (!empty($parts['query'])) {
		parse_str($parts['query'], $q);
		if (!empty($q['v'])) return preg_replace('/[^A-Za-z0-9_-]/', '', (string) $q['v']);
	}

	if (preg_match('~/embed/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];

	return '';
}

function mp_videos_url_with($base, $overrides = []) {
	$params = $_GET;

	foreach ($overrides as $k => $v) {
		if ($v === null) {
			unset($params[$k]);
		} else {
			$params[$k] = $v;
		}
	}

	$q = http_build_query($params);
	return $q ? ($base . '?' . $q) : $base;
}

function mp_video_card_term_label($post_id, $taxonomies, $fallback = '') {
	foreach ($taxonomies as $taxonomy) {
		$terms = get_the_terms($post_id, $taxonomy);
		if (!empty($terms) && !is_wp_error($terms)) {
			return $terms[0]->name;
		}
	}

	return $fallback;
}

function mp_video_card_label($post_id) {
	return mp_video_card_term_label($post_id, ['product', 'technology', 'industry'], __('Video', 'mp-academy'));
}
/* ----------------------------- */

// Page size (size=n_12_n)
$size_raw = isset($_GET['size']) ? sanitize_text_field(wp_unslash($_GET['size'])) : 'n_12_n';
$per_page = 12;
if (preg_match('/n_(\d+)_n/', $size_raw, $m)) {
	$per_page = max(6, min(48, (int) $m[1]));
}

// Search
$search = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';

// View mode
$view_mode = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : 'grid';
if (!in_array($view_mode, ['grid', 'list'], true)) {
	$view_mode = 'grid';
}

// Pagination
$paged = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;

// Dynamic taxonomies attached to video
$taxes = get_object_taxonomies($post_type, 'objects');
$filter_taxes = [];
foreach ($taxes as $tax) {
	if (!empty($tax->public) && !empty($tax->show_ui) && $tax->name !== 'post_format') {
		$filter_taxes[$tax->name] = $tax;
	}
}

// Build tax_query from checkbox arrays
$tax_query = [];
foreach ($filter_taxes as $tax_name => $tax_obj) {
	$key = 'tax_' . $tax_name; // e.g. tax_technology[]
	if (!isset($_GET[$key]) || !is_array($_GET[$key])) continue;

	$slugs = array_values(array_filter(array_map(function ($v) {
		return sanitize_text_field(wp_unslash($v));
	}, $_GET[$key])));

	if (!empty($slugs)) {
		$tax_query[] = [
			'taxonomy' => $tax_name,
			'field'    => 'slug',
			'terms'    => $slugs,
			'operator' => 'IN',
		];
	}
}
if (count($tax_query) > 1) $tax_query['relation'] = 'AND';

// Query
$args = [
	'post_type'      => $post_type,
	'post_status'    => 'publish',
	'posts_per_page' => $per_page,
	'paged'          => $paged,
	's'              => $search,
];
if (!empty($tax_query)) $args['tax_query'] = $tax_query;

$q = new WP_Query($args);
$base_url = get_permalink();
$hero_lede = has_excerpt() ? get_the_excerpt() : __('Science moves faster when we share what we know', 'mp-academy');
$hero_placeholder = 'https://dam.malvernpanalytical.com/fae4c741-f556-475a-b286-b36e0098eefe/website%20hero%20placeholder_Original%20file.svg';
?>

<main id="primary" class="site-main">
	<form method="get" action="<?php echo esc_url($base_url); ?>" class="mp-videos-form">

		<input type="hidden" name="view" value="<?php echo esc_attr($view_mode); ?>" />
		<input type="hidden" name="pg" value="1" />

		<section class="c-hero c-hero--dark mp-videos-hero" style="--placeholder-image: url('<?php echo esc_url($hero_placeholder); ?>')">
			<div class="c-hero__wrap">
				<div class="c-hero__main">
					<?php
					get_template_part('template-parts/components/breadcrumbs', null, [
						'extra_class' => 'c-breadcrumb--dark',
					]);
					?>

					<h1 class="c-hero__heading"><?php echo esc_html(get_the_title()); ?></h1>
					<p class="c-hero__lede"><?php echo esc_html($hero_lede); ?></p>

					<div class="c-hero__search c-form c-form--search">
						<div class="c-form__input-wrap">
							<label for="hero-search" class="u-hidden"><?php esc_html_e('Search', 'mp-academy'); ?></label>
							<input
								id="hero-search"
								placeholder="Search"
								type="search"
								name="q"
								value="<?php echo esc_attr($search); ?>"
								class="c-input c-input--with-button"
							>
							<button type="submit" class="mp-videos-hero__submit">
								<span class="u-hidden"><?php esc_html_e('Search', 'mp-academy'); ?></span>
								<span aria-hidden="true"><?php esc_html_e('Go', 'mp-academy'); ?></span>
							</button>
						</div>
					</div>
				</div>
				<div class="c-hero__media-wrap"></div>
			</div>
		</section>

		<div id="mp-videos-library-results" class="u-wrap mp-videos-content-wrap u-margin-bottom-xl">
			<div class="mp-videos-library">

			<div class="mp-videos-layout">

				<!-- Filters -->
				<aside class="mp-filters" aria-label="Filters">
					<div class="mp-filters__top">
						<p class="mp-filters__title"><?php esc_html_e('Filters', 'mp-academy'); ?></p>
						<a class="mp-filters__clear u-link" href="<?php echo esc_url($base_url); ?>"><?php esc_html_e('Clear', 'mp-academy'); ?></a>
					</div>

					<ul class="mp-facets u-remove-bullets">
						<?php foreach ($filter_taxes as $tax_name => $tax_obj) :
							$key = 'tax_' . $tax_name;

							$selected = [];
							if (isset($_GET[$key]) && is_array($_GET[$key])) {
								$selected = array_values(array_filter(array_map(function ($v) {
									return sanitize_text_field(wp_unslash($v));
								}, $_GET[$key])));
							}

							$terms = get_terms([
								'taxonomy'   => $tax_name,
								'hide_empty' => false,
								'orderby'    => 'name',
								'order'      => 'ASC',
							]);

							if (empty($terms) || is_wp_error($terms)) continue;

							$facet_id = sanitize_html_class($tax_name . '-facet');
							$is_open = ! empty( $selected );
						?>
							<li>
								<details class="c-facet<?php echo $is_open ? ' c-facet--open' : ''; ?>"<?php echo $is_open ? ' open' : ''; ?>>
									<summary id="<?php echo esc_attr($facet_id); ?>" class="c-facet__toggle">
										<?php echo esc_html($tax_obj->labels->name); ?>
										<span class="mp-facet__chevron" aria-hidden="true"></span>
									</summary>

									<ul
										id="<?php echo esc_attr($facet_id . '-options'); ?>"
										class="c-facet__list u-remove-bullets"
									>
										<?php foreach ($terms as $t) :
											$checked = in_array($t->slug, $selected, true);
											$input_id = sanitize_html_class($tax_name . '_' . $t->slug);
										?>
											<li>
												<input
													id="<?php echo esc_attr($input_id); ?>"
													class="c-checkbox"
													type="checkbox"
													name="<?php echo esc_attr($key); ?>[]"
													value="<?php echo esc_attr($t->slug); ?>"
													<?php checked($checked); ?>
												/>
												<label for="<?php echo esc_attr($input_id); ?>">
													<?php echo esc_html($t->name); ?>
												</label>
											</li>
										<?php endforeach; ?>
									</ul>
								</details>
							</li>
						<?php endforeach; ?>
					</ul>
				</aside>

				<!-- Results -->
				<section class="mp-results" aria-label="Results">

					<div class="mp-results__top">
						<div class="mp-results__count"><?php echo (int) $q->found_posts; ?> results</div>

							<div class="mp-results__controls">
								<div>
									<label for="mp-view-toggle" class="c-toggle">
										<?php esc_html_e('Grid', 'mp-academy'); ?>
									<input
										id="mp-view-toggle"
										type="checkbox"
										class="c-toggle__checkbox u-hidden"
										role="switch"
										aria-checked="<?php echo 'list' === $view_mode ? 'true' : 'false'; ?>"
										aria-label="<?php esc_attr_e('Grid / List', 'mp-academy'); ?>"
										<?php checked('list', $view_mode); ?>
									/>
										<span class="c-toggle__slider" aria-hidden="true"></span>
										<?php esc_html_e('List', 'mp-academy'); ?>
									</label>
								</div>

								<div class="mp-results__size">
								<label for="mp-size">Show</label>
								<select id="mp-size" name="size">
									<?php foreach ([12, 24, 36] as $n) :
										$val = "n_{$n}_n";
									?>
										<option value="<?php echo esc_attr($val); ?>" <?php selected($size_raw, $val); ?>>
											<?php echo (int) $n; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<?php if ($q->have_posts()) : ?>
						<div class="mp-video-cards mp-video-cards--<?php echo esc_attr($view_mode); ?>">
							<?php while ($q->have_posts()) : $q->the_post(); ?>
								<?php
								$post_id = get_the_ID();

								// YouTube thumbnail from ACF video_url
								$video_url = function_exists('get_field') ? (string) get_field('video_url', $post_id) : '';
								if (!$video_url) $video_url = (string) get_post_meta($post_id, 'video_url', true);

								$yt_id = mp_get_youtube_id_from_url($video_url);
								$thumb = $yt_id ? "https://i.ytimg.com/vi/{$yt_id}/hqdefault.jpg" : '';

								$tag_label = mp_video_card_label($post_id);
								$meta_label = mp_video_card_term_label($post_id, ['technology'], __('Technology', 'mp-academy'));
								?>

								<?php
								get_template_part('template-parts/video/card', null, [
									'post_id'    => $post_id,
									'thumb'      => $thumb,
									'tag_label'  => $tag_label,
									'meta_label' => $meta_label,
								]);
								?>

							<?php endwhile; wp_reset_postdata(); ?>
						</div>

						<?php
						$total = (int) $q->max_num_pages;
						if ($total > 1) : ?>
							<nav class="mp-pagination" aria-label="Pagination">
								<div class="mp-pagination__side">
									<?php if ($paged > 1) : ?>
										<a class="mp-page mp-page--nav" href="<?php echo esc_url(mp_videos_url_with($base_url, ['pg' => $paged - 1])); ?>">
											<span class="mp-page__arrow" aria-hidden="true">&#8592;</span>
											<span><?php esc_html_e('Previous page', 'mp-academy'); ?></span>
										</a>
									<?php else : ?>
										<span class="mp-page mp-page--nav is-disabled" aria-disabled="true">
											<span class="mp-page__arrow" aria-hidden="true">&#8592;</span>
											<span><?php esc_html_e('Previous page', 'mp-academy'); ?></span>
										</span>
									<?php endif; ?>
								</div>

								<p class="mp-pagination__status">
									<?php
									printf(
										/* translators: 1: current page number, 2: total page count */
										esc_html__('Page %1$d of %2$d', 'mp-academy'),
										(int) $paged,
										(int) $total
									);
									?>
								</p>

								<div class="mp-pagination__side mp-pagination__side--next">
									<?php if ($paged < $total) : ?>
										<a class="mp-page mp-page--nav" href="<?php echo esc_url(mp_videos_url_with($base_url, ['pg' => $paged + 1])); ?>">
											<span><?php esc_html_e('Next page', 'mp-academy'); ?></span>
											<span class="mp-page__arrow" aria-hidden="true">&#8594;</span>
										</a>
									<?php else : ?>
										<span class="mp-page mp-page--nav is-disabled" aria-disabled="true">
											<span><?php esc_html_e('Next page', 'mp-academy'); ?></span>
											<span class="mp-page__arrow" aria-hidden="true">&#8594;</span>
										</span>
									<?php endif; ?>
								</div>
							</nav>
						<?php endif; ?>

					<?php else : ?>
						<p>No videos found.</p>
					<?php endif; ?>

				</section>
			</div>
		</div>
	</form>
</main>

<?php get_footer(); ?>
