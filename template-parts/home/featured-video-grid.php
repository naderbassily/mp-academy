<?php
/**
 * Template part: MP Academy — Latest Videos Grid
 *
 * Shows recent videos that already exist in the local MP Academy video library.
 *
 * @package MP_Academy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mp_academy_home_get_youtube_id_from_url' ) ) {
	/**
	 * Extract a YouTube video ID from a stored video URL.
	 *
	 * @param string $url Video URL.
	 * @return string
	 */
	function mp_academy_home_get_youtube_id_from_url( $url ) {
		if ( ! $url ) {
			return '';
		}

		$url = trim( (string) $url );

		if ( preg_match( '~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $matches ) ) {
			return $matches[1];
		}

		$parts = wp_parse_url( $url );
		if ( ! empty( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
			if ( ! empty( $query['v'] ) ) {
				return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $query['v'] );
			}
		}

		if ( preg_match( '~/embed/([A-Za-z0-9_-]{6,})~', $url, $matches ) ) {
			return $matches[1];
		}

		return '';
	}
}

if ( ! function_exists( 'mp_academy_home_video_term_label' ) ) {
	/**
	 * Get the first matching video term label.
	 *
	 * @param int      $post_id    Video post ID.
	 * @param string[] $taxonomies Candidate taxonomies.
	 * @param string   $fallback   Fallback label.
	 * @return string
	 */
	function mp_academy_home_video_term_label( $post_id, $taxonomies, $fallback = '' ) {
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				return $terms[0]->name;
			}
		}

		return $fallback;
	}
}

if ( ! function_exists( 'mp_academy_home_video_card_label' ) ) {
	/**
	 * Match the Video Library card tag label logic.
	 *
	 * @param int $post_id Video post ID.
	 * @return string
	 */
	function mp_academy_home_video_card_label( $post_id ) {
		return mp_academy_home_video_term_label( $post_id, array( 'product', 'technology', 'industry' ), __( 'Video', 'mp-academy' ) );
	}
}

$videos_url = home_url( '/videos-library/' );
$video_query = new WP_Query(
	array(
		'post_type'      => 'video',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>

<section class="mp mp-featured-videos mp-section" aria-labelledby="mp-featured-videos-title">
	<div class="u-wrap">
		<header class="mp-featured-videos__hd">
			<p id="mp-featured-videos-title" class="c-h c-h--step-4">
				<?php esc_html_e( 'Latest videos', 'mp-academy' ); ?>
			</p>

			<a class="mp-featured-videos__browse u-blue" href="<?php echo esc_url( $videos_url ); ?>">
				<span class="mp-featured-videos__browse-icon" aria-hidden="true">→</span>
				<span><?php esc_html_e( 'Browse all videos', 'mp-academy' ); ?></span>
			</a>
		</header>

		<?php if ( $video_query->have_posts() ) : ?>
			<div class="mp-videos-library">
				<div class="mp-video-cards">
				<?php
				while ( $video_query->have_posts() ) :
					$video_query->the_post();

					$post_id   = get_the_ID();
					$video_url = function_exists( 'get_field' ) ? (string) get_field( 'video_url', $post_id ) : '';

					if ( ! $video_url ) {
						$video_url = (string) get_post_meta( $post_id, 'video_url', true );
					}

					$youtube_id = mp_academy_home_get_youtube_id_from_url( $video_url );
					$thumbnail  = $youtube_id ? 'https://i.ytimg.com/vi/' . rawurlencode( $youtube_id ) . '/hqdefault.jpg' : '';

					get_template_part(
						'template-parts/video/card',
						null,
						array(
							'post_id'    => $post_id,
							'thumb'      => $thumbnail,
							'tag_label'  => mp_academy_home_video_card_label( $post_id ),
							'meta_label' => mp_academy_home_video_term_label( $post_id, array( 'technology' ), __( 'Technology', 'mp-academy' ) ),
						)
					);
				endwhile;
				wp_reset_postdata();
				?>
				</div>
			</div>
		<?php else : ?>
			<p class="mp-featured-videos__note">
				<?php esc_html_e( 'No videos available at the moment.', 'mp-academy' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>
