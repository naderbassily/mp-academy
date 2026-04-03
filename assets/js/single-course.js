/**
 * Single Course - Accordion Functionality
 * 
 * File: assets/js/single-course.js
 */

(function() {
	'use strict';
	
	document.addEventListener('DOMContentLoaded', function() {
		initAccordion();
	});
	
	/**
	 * Initialize accordion functionality
	 */
	function initAccordion() {
		const accordionHeaders = document.querySelectorAll('.mp-accordion-header');

		if (!accordionHeaders.length) {
			return;
		}
		
		accordionHeaders.forEach(header => {
			header.addEventListener('click', function() {
				toggleAccordion(this);
			});
		});
	}
	
	/**
	 * Toggle accordion open/close
	 */
	function toggleAccordion(header) {
		const isExpanded = header.getAttribute('aria-expanded') === 'true';
		const contentId = header.getAttribute('aria-controls');
		const content = document.getElementById(contentId);

		if (!content) {
			return;
		}
		
		if (isExpanded) {
			// Close accordion
			header.setAttribute('aria-expanded', 'false');
			content.hidden = true;
		} else {
			// Open accordion
			header.setAttribute('aria-expanded', 'true');
			content.hidden = false;
		}
	}
	
})();
