<?php
/**
 * Template part: Video card
 *
 * Expected args:
 * - post_id
 * - thumb
 * - tag_label
 * - meta_label
 */

if (!defined('ABSPATH')) {
	exit;
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
$thumb = isset($args['thumb']) ? (string) $args['thumb'] : '';
$tag_label = isset($args['tag_label']) ? (string) $args['tag_label'] : '';
$meta_label = isset($args['meta_label']) ? (string) $args['meta_label'] : '';
?>

<article class="mp c-card c-card--layout-single c-card--size-small c-card--alt c-card--has-tag c-card--has-image">
	<span class="c-card__tag"><?php echo esc_html($tag_label); ?></span>
	<div class="c-card__wrapper">
		<figure class="c-card__image">
			<a href="<?php the_permalink(); ?>">
				<?php if ($thumb) : ?>
					<img class="u-2/1" src="<?php echo esc_url($thumb); ?>" alt="Alt" loading="lazy" />
				<?php else : ?>
					<img class="u-2/1" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/no-courses.svg'); ?>" alt="Alt" loading="lazy" />
				<?php endif; ?>
			</a>
		</figure>

		<div class="c-card__primary">
			<header class="c-card__header u-flow--2xs">
				<?php if ($meta_label !== '') : ?>
					<p class="c-card__meta"><?php echo esc_html($meta_label); ?></p>
				<?php endif; ?>
				<h2 class="c-h c-card__title">
					<?php the_title(); ?>
				</h2>
			</header>
		</div>

		<a class="u-fill u-fill--link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</div>
</article>
