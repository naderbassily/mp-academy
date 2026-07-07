/**
 * Videos Library filters
 *
 * - Franklin facet toggle behavior
 * - Auto-submit checkbox filters
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.mp-videos-form');
    if (!form) {
      return;
    }

    var pageInput = form.querySelector('input[name="pg"]');
    var viewInput = form.querySelector('input[name="view"]');
    var resultsContainer = document.querySelector('.mp-results');
    var activeRequest = null;

    function buildUrl() {
      var formData = new FormData(form);
      var params = new URLSearchParams();

      formData.forEach(function (value, key) {
        if (value !== '') {
          params.append(key, value);
        }
      });

      var action = form.getAttribute('action') || window.location.pathname;
      action = action.replace(/#.*$/, '');

      return action + (params.toString() ? ('?' + params.toString()) : '');
    }

    function replaceResultsFromResponse(html) {
      var parser = new DOMParser();
      var doc = parser.parseFromString(html, 'text/html');
      var newResults = doc.querySelector('.mp-results');

      if (!newResults || !resultsContainer) {
        return;
      }

      resultsContainer.replaceWith(newResults);
      resultsContainer = newResults;
    }

    function submitFiltersInPlace() {
      var url = buildUrl();

      if (activeRequest && typeof activeRequest.abort === 'function') {
        activeRequest.abort();
      }

      activeRequest = new AbortController();

      form.classList.add('is-loading');

      fetch(url, {
        credentials: 'same-origin',
        signal: activeRequest.signal,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      }).then(function (response) {
        if (!response.ok) {
          throw new Error('Filter request failed');
        }

        return response.text();
      }).then(function (html) {
        replaceResultsFromResponse(html);
        window.history.replaceState({}, '', url);
      }).catch(function (error) {
        if (error && error.name === 'AbortError') {
          return;
        }

        window.location.assign(url);
      }).finally(function () {
        form.classList.remove('is-loading');
        activeRequest = null;
      });
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      if (pageInput) {
        pageInput.value = '1';
      }

      submitFiltersInPlace();
    });

    form.addEventListener('click', function (event) {
      var summary = event.target.closest('.mp-filters details.c-facet > summary.c-facet__toggle');
      if (summary) {
        event.preventDefault();

        var details = summary.parentElement;
        if (!details) {
          return;
        }

        if (details.hasAttribute('open')) {
          details.removeAttribute('open');
        } else {
          details.setAttribute('open', 'open');
        }

        return;
      }

      var clearLink = event.target.closest('.mp-filters__clear');
      if (clearLink) {
        event.preventDefault();

        form.querySelectorAll('.mp-filters input[type="checkbox"]').forEach(function (checkbox) {
          checkbox.checked = false;
        });

        if (pageInput) {
          pageInput.value = '1';
        }

        submitFiltersInPlace();
        return;
      }

      var paginationLink = event.target.closest('.mp-page');
      if (paginationLink) {
        event.preventDefault();

        var targetUrl = paginationLink.getAttribute('href');
        if (!targetUrl) {
          return;
        }

        var parsedUrl = new URL(targetUrl, window.location.origin);
        if (pageInput) {
          pageInput.value = parsedUrl.searchParams.get('pg') || '1';
        }

        submitFiltersInPlace();
      }
    });

    form.addEventListener('change', function (event) {
      if (event.target.matches('.mp-filters input[type="checkbox"]')) {
        if (pageInput) {
          pageInput.value = '1';
        }
        submitFiltersInPlace();
        return;
      }

      if (event.target.matches('#mp-size')) {
        if (pageInput) {
          pageInput.value = '1';
        }
        submitFiltersInPlace();
        return;
      }

      if (event.target.matches('.c-toggle__checkbox') && viewInput) {
        viewInput.value = event.target.checked ? 'list' : 'grid';

        if (pageInput) {
          pageInput.value = '1';
        }

        submitFiltersInPlace();
      }
    });
  });
})();
