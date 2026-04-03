/**
 * All Courses Archive Filtering
 * File: assets/js/all-courses.js
 */

(function() {
	'use strict';
	
	// Wait for DOM to be ready
	document.addEventListener('DOMContentLoaded', function() {
		initCourseFilters();
	});
	
	/**
	 * Initialize course filtering
	 */
	function initCourseFilters() {
		const filterContainer = document.querySelector('[data-archive-filters]');
		
		if (!filterContainer) return;
		
		const filterButtons = filterContainer.querySelectorAll('[data-filter]');
		const courseCards = document.querySelectorAll('[data-course-status]');
		
		if (!filterButtons.length || !courseCards.length) return;
		
		// Handle filter button clicks
		filterButtons.forEach(button => {
			button.addEventListener('click', function() {
				const filterValue = this.getAttribute('data-filter');
				
				// Update active state
				updateActiveButton(filterButtons, this);
				
				// Filter courses
				filterCourses(courseCards, filterValue);
			});
		});
	}
	
	/**
	 * Update active button state
	 * @param {NodeList} buttons - All filter buttons
	 * @param {Element} activeButton - The clicked button
	 */
	function updateActiveButton(buttons, activeButton) {
		buttons.forEach(btn => {
			btn.classList.remove('is-active');
			btn.setAttribute('aria-pressed', 'false');
		});
		
		activeButton.classList.add('is-active');
		activeButton.setAttribute('aria-pressed', 'true');
	}
	
	/**
	 * Filter courses based on status
	 * @param {NodeList} cards - All course cards
	 * @param {string} filter - Filter value: 'all', 'in-progress', 'not-started', 'completed'
	 */
	function filterCourses(cards, filter) {
		cards.forEach(card => {
			const status = card.getAttribute('data-course-status');
			const isEnrolled = card.getAttribute('data-course-enrolled') === 'true';
			
			let shouldShow = false;
			
			switch(filter) {
				case 'all':
					shouldShow = true;
					break;
				case 'in-progress':
					shouldShow = status === 'in-progress' && isEnrolled;
					break;
				case 'not-started':
					shouldShow = status === 'not-started' && isEnrolled;
					break;
				case 'completed':
					shouldShow = status === 'completed' && isEnrolled;
					break;
			}
			
			// Toggle visibility
			if (shouldShow) {
				card.classList.remove('is-hidden');
			} else {
				card.classList.add('is-hidden');
			}
		});
	}
	
})();
