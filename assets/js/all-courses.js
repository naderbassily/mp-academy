/**
 * All Courses Archive - Filtering
 * Radio (All) + Checkboxes (In progress, Not started, Completed)
 * 
 * File: assets/js/all-courses.js
 */

(function() {
	'use strict';
	
	document.addEventListener('DOMContentLoaded', function() {
		initCourseFilters();
	});
	
	/**
	 * Initialize course filtering
	 */
	function initCourseFilters() {
		const filterContainer = document.querySelector('[data-course-filters]');
		
		if (!filterContainer) return;
		
		const radioInput = filterContainer.querySelector('input[type="radio"]');
		const checkboxInputs = filterContainer.querySelectorAll('input[type="checkbox"]');
		const courseCards = document.querySelectorAll('[data-course-status]');
		
		if (!radioInput || !checkboxInputs.length || !courseCards.length) return;
		
		// Radio: "All courses" clicked
		radioInput.addEventListener('change', function() {
			if (this.checked) {
				// Uncheck all checkboxes
				checkboxInputs.forEach(cb => cb.checked = false);
				// Show all courses
				showAllCourses(courseCards);
			}
		});
		
		// Checkboxes: Any checkbox clicked
		checkboxInputs.forEach(checkbox => {
			checkbox.addEventListener('change', function() {
				// If any checkbox is checked, uncheck radio
				const anyChecked = Array.from(checkboxInputs).some(cb => cb.checked);
				if (anyChecked) {
					radioInput.checked = false;
					// Filter by checked boxes
					filterByCheckboxes(courseCards, checkboxInputs);
				} else {
					// If all unchecked, check radio and show all
					radioInput.checked = true;
					showAllCourses(courseCards);
				}
			});
		});
	}
	
	/**
	 * Show all courses
	 */
	function showAllCourses(cards) {
		cards.forEach(card => {
			card.classList.remove('is-hidden');
		});
	}
	
	/**
	 * Filter courses by checked checkboxes
	 */
	function filterByCheckboxes(cards, checkboxes) {
		// Get checked filter values
		const checkedFilters = Array.from(checkboxes)
			.filter(cb => cb.checked)
			.map(cb => cb.getAttribute('data-filter'));
		
		if (checkedFilters.length === 0) {
			showAllCourses(cards);
			return;
		}
		
		// Show/hide cards based on status
		cards.forEach(card => {
			const status = card.getAttribute('data-course-status');
			const isEnrolled = card.getAttribute('data-course-enrolled') === 'true';
			
			// Only show enrolled courses with matching status
			if (isEnrolled && checkedFilters.includes(status)) {
				card.classList.remove('is-hidden');
			} else {
				card.classList.add('is-hidden');
			}
		});
	}
	
})();