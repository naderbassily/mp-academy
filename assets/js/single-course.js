/**
 * Single Course - Accordion Functionality
 * 
 * File: assets/js/single-course.js
 */

(function() {
	'use strict';
	
	document.addEventListener('DOMContentLoaded', function() {
		console.log('Single course JS loaded');
		initAccordion();
	});
	
	/**
	 * Initialize accordion functionality
	 */
	function initAccordion() {
		const accordionHeaders = document.querySelectorAll('.mp-accordion-header');
		
		console.log('Found accordion headers:', accordionHeaders.length);
		
		if (!accordionHeaders.length) {
			console.warn('No accordion headers found!');
			return;
		}
		
		accordionHeaders.forEach(header => {
			header.addEventListener('click', function(e) {
				console.log('Accordion clicked');
				toggleAccordion(this);
			});
		});
		
		console.log('Accordion initialized successfully');
	}
	
	/**
	 * Toggle accordion open/close
	 */
	function toggleAccordion(header) {
		const isExpanded = header.getAttribute('aria-expanded') === 'true';
		const contentId = header.getAttribute('aria-controls');
		const content = document.getElementById(contentId);
		
		console.log('Toggle accordion:', { isExpanded, contentId, content });
		
		if (!content) {
			console.error('Content not found for:', contentId);
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
