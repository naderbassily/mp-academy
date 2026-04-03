<?php
/**
 * Custom override for [course_content]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_id = learndash_get_course_id();
$lessons = learndash_get_lesson_list( $course_id );

if ( ! empty( $lessons ) ) : ?>
	<section class="u-wrap u-space--section">
		<h2 class="c-h u-margin-bottom-s">Course Content</h2>
		<ul class="o-list u-flow">
			<?php foreach ( $lessons as $lesson ) : ?>
				<li class="c-list-item">
					<a href="<?php echo esc_url( get_permalink( $lesson->ID ) ); ?>" class="c-link">
						<?php echo esc_html( get_the_title( $lesson->ID ) ); ?>
					</a>
					<?php
					$topics = learndash_get_topic_list( $lesson->ID, $course_id );
					if ( ! empty( $topics ) ) : ?>
						<ul class="u-flow--xs u-margin-left-s">
							<?php foreach ( $topics as $topic ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $topic->ID ) ); ?>" class="c-link c-link--sub">
										<?php echo esc_html( get_the_title( $topic->ID ) ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
<?php endif; ?>
