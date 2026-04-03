<?php
/**
 * Template Part: Archive Header
 * Title (left) + Filters (right) on same line
 * 
 * Location: template-parts/course/archive/header.php
 */
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();
?>

<header class="mp-archive-header u-margin-bottom-xl">
	<div class="mp-archive-header__inner">
		
		<!-- Title (Left) -->
		<h1 class="mp-archive-header__title c-heading c-heading--h1">
			<?php esc_html_e('All courses', 'mp-academy'); ?>
		</h1>
		
		<!-- Filters (Right) - Only for logged-in users -->
		<?php if ($user_id) : ?>
		<div class="mp-archive-header__filters" data-course-filters>
			<span class="mp-archive-header__filter-label">
				<?php esc_html_e('Show:', 'mp-academy'); ?>
			</span>
			
			<div class="mp-filter-group">
				
				<!-- Radio: All courses (default) -->
				<label class="mp-filter-item mp-filter-item--radio">
					<input 
						type="radio" 
						name="course-filter" 
						value="all" 
						class="mp-filter-item__input"
						data-filter="all"
						checked
					>
					<span class="mp-filter-item__label">
						<?php esc_html_e('All courses', 'mp-academy'); ?>
					</span>
				</label>
				
				<!-- Checkbox: In progress -->
				<label class="mp-filter-item mp-filter-item--checkbox">
					<input 
						type="checkbox" 
						name="course-filter-progress" 
						value="in-progress" 
						class="mp-filter-item__input"
						data-filter="in-progress"
					>
					<span class="mp-filter-item__label">
						<?php esc_html_e('In progress', 'mp-academy'); ?>
					</span>
				</label>
				
				<!-- Checkbox: Not started -->
				<label class="mp-filter-item mp-filter-item--checkbox">
					<input 
						type="checkbox" 
						name="course-filter-not-started" 
						value="not-started" 
						class="mp-filter-item__input"
						data-filter="not-started"
					>
					<span class="mp-filter-item__label">
						<?php esc_html_e('Not started', 'mp-academy'); ?>
					</span>
				</label>
				
				<!-- Checkbox: Completed -->
				<label class="mp-filter-item mp-filter-item--checkbox">
					<input 
						type="checkbox" 
						name="course-filter-completed" 
						value="completed" 
						class="mp-filter-item__input"
						data-filter="completed"
					>
					<span class="mp-filter-item__label">
						<?php esc_html_e('Completed', 'mp-academy'); ?>
					</span>
				</label>
				
			</div>
		</div>
		<?php endif; ?>
		
	</div>
</header>