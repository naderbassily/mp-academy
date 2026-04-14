<?php
/**
 * Template Part: Module List (Accordion)
 * Shows lessons as accordion items, with topics inside
 * 
 * Location: template-parts/course/single/module-list.php
 */

if (!defined('ABSPATH')) exit;

$course_id = (int) ($args['course_id'] ?? get_the_ID());
$user_id   = (int) ($args['user_id'] ?? get_current_user_id());

$is_logged_in = is_user_logged_in();
$is_enrolled  = false;

if ($is_logged_in && function_exists('sfwd_lms_has_access')) {
	$is_enrolled = sfwd_lms_has_access($course_id, $user_id);
}

// Get lessons (these are the modules)
$lessons = [];
if (function_exists('learndash_get_course_lessons_list')) {
	$raw = learndash_get_course_lessons_list($course_id, $user_id);
	if (is_array($raw)) {
		foreach ($raw as $row) {
			$lessons[] = is_object($row) ? $row : ($row['post'] ?? null);
		}
		$lessons = array_filter($lessons);
	}
}

if (empty($lessons)) {
	return;
}
?>

<section class="mp-course-modules u-margin-bottom-l">
	<!-- Module Accordion -->
	<div class="mp-module-accordion">
		<?php 
		$module_number = 1;
		foreach ($lessons as $lesson) :
			$lesson_id = is_object($lesson) ? $lesson->ID : (int) $lesson;
			$lesson_title = get_the_title($lesson_id);
			$lesson_link = get_permalink($lesson_id);
			
			// Get topics (sub-lessons) for this lesson
			$topics = [];
			if (function_exists('learndash_get_topic_list')) {
				$topics = learndash_get_topic_list($lesson_id, $course_id);
			}

			$lesson_status = $is_logged_in ? mp_get_step_status($user_id, $course_id, $lesson_id, 'lesson') : null;
			$module_status = $lesson_status;
			$topic_statuses = [];

			if (!empty($topics)) {
				$all_topics_complete = true;
				$any_topic_started   = false;

				foreach ($topics as $topic) {
					$topic_id = is_object($topic) ? $topic->ID : (int) $topic;
					$status   = $is_logged_in ? mp_get_step_status($user_id, $course_id, $topic_id, 'topic') : null;

					$topic_statuses[$topic_id] = $status;

					if (!$status || empty($status['complete'])) {
						$all_topics_complete = false;
					}

					if ($status && (!empty($status['started']) || !empty($status['complete']))) {
						$any_topic_started = true;
					}
				}

				$module_status = [
					'complete' => $is_logged_in && $all_topics_complete,
					'started'  => $any_topic_started || ($lesson_status && (!empty($lesson_status['started']) || !empty($lesson_status['complete']))),
					'can_view' => $lesson_status['can_view'] ?? true,
				];
			}
			
			// Module classes
			$module_classes = ['mp-accordion-item'];
			if ($module_status && $module_status['complete']) {
				$module_classes[] = 'mp-accordion-item--complete';
			} elseif ($module_status && $module_status['started']) {
				$module_classes[] = 'mp-accordion-item--in-progress';
			}
			
			$accordion_id = 'module-' . $lesson_id;
			?>
			
			<div class="<?php echo esc_attr(implode(' ', $module_classes)); ?>">
				
				<!-- Accordion Header (clickable) -->
					<button 
						class="mp-accordion-header" 
						aria-expanded="false" 
						aria-controls="<?php echo esc_attr($accordion_id); ?>"
						type="button"
					>
					<div class="mp-accordion-header__content">
						
							<?php if ($module_status && $module_status['complete']) : ?>
							<!-- Complete checkmark icon -->
							<span class="mp-accordion-icon mp-accordion-icon--complete" aria-label="<?php esc_attr_e('Complete', 'mp-academy'); ?>">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="12" cy="12" r="10" fill="#00B140"/>
									<path d="M7 12L10.5 15.5L17 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
						<?php else : ?>
							<!-- Empty circle -->
							<span class="mp-accordion-icon" aria-hidden="true"></span>
						<?php endif; ?>
						
						<div class="mp-accordion-title c-h c-h--step-0">
							<?php 
							printf(
								esc_html__('Module %d - %s', 'mp-academy'),
								$module_number,
								esc_html($lesson_title)
							);
								?>
						</div>
						
							<?php if ($module_status) : ?>
								<div class="mp-accordion-badge">
									<?php if ($module_status['complete']) : ?>
										<span class="mp-badge mp-badge--complete">
											<?php esc_html_e('Complete', 'mp-academy'); ?>
										</span>
									<?php elseif ($module_status['started']) : ?>
										<span class="mp-badge mp-badge--in-progress">
											<?php esc_html_e('In progress', 'mp-academy'); ?>
										</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						
					</div>
					
					<!-- Chevron icon -->
					<svg class="mp-accordion-chevron" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				
				<!-- Accordion Content (topics/lessons) -->
				<?php if (!empty($topics)) : ?>
						<div 
							id="<?php echo esc_attr($accordion_id); ?>" 
							class="mp-accordion-content" 
							hidden
						>
							<ul class="mp-topic-list">
								<?php foreach ($topics as $topic) : 
								$topic_id = is_object($topic) ? $topic->ID : (int) $topic;
								$topic_title = get_the_title($topic_id);
								$topic_link = get_permalink($topic_id);
								
									$topic_status = $topic_statuses[$topic_id] ?? null;
								?>
								
									<li class="mp-topic-item">
										<?php if ($is_enrolled && $topic_link) : ?>
											<a href="<?php echo esc_url($topic_link); ?>" class="mp-topic-link">
												<?php if ($topic_status && $topic_status['complete']) : ?>
													<span class="mp-topic-icon mp-topic-icon--complete">✓</span>
												<?php else : ?>
													<span class="mp-topic-icon"> </span>
												<?php endif; ?>
												<?php
$topic_excerpt = trim( get_post_field( 'post_excerpt', $topic_id ) );
?>

<span class="mp-topic-text">
	<span class="mp-topic-title"><?php echo esc_html( $topic_title ); ?></span>

	<?php if ( $topic_excerpt !== '' ) : ?>
		<span class="mp-topic-excerpt u-step--1 u-blue">
			<?php echo esc_html( $topic_excerpt ); ?>
		</span>
	<?php endif; ?>
</span>

											</a>
										<?php else : ?>
											<span class="mp-topic-link mp-topic-link--disabled">
												<span class="mp-topic-icon">○</span>
												<?php $topic_excerpt = trim( get_post_field( 'post_excerpt', $topic_id ) ); ?>
												<span class="mp-topic-text">
													<span class="mp-topic-title"><?php echo esc_html($topic_title); ?></span>
													<?php if ( $topic_excerpt !== '' ) : ?>
														<span class="mp-topic-excerpt u-step--1 u-blue">
															<?php echo esc_html( $topic_excerpt ); ?>
														</span>
													<?php endif; ?>
												</span>
											</span>
										<?php endif; ?>
									</li>
								
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				
			</div>
			
			<?php $module_number++; ?>
		<?php endforeach; ?>
	</div>
	
</section>
