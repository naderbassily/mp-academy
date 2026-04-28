<?php
/**
 * Template: Course Archive (All Courses)
 * Franklin Design System
 * 
 * @package MP_Academy
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<main id="primary" class="site-main">
	
	<div class="mp-course-archive u-margin-top-xl u-margin-bottom-xl">
		<div class="u-wrap">
			
			<?php 
			// Breadcrumb: Home > Courses
			get_template_part('template-parts/components/breadcrumbs'); 
			?>
			
			<?php 
			// Header: Title (left) + Filters (right) on same line
			get_template_part('template-parts/course/archive/header'); 
			?>
			
			<?php 
			// Course list organized by categories
			get_template_part('template-parts/course/archive/course-list'); 
			?>
			
		</div>
	</div>

</main>

<?php
get_footer();
