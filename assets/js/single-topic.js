/**
 * MP Academy – LearnDash Topic helpers
 * - Keeps LD buttons/links working (DO NOT replace elements)
 * - Styles: adds Franklin classes to Mark Complete + Prev/Next nav links
 * - Video options: progression, resume, pause on unfocus, autostart, controls, auto-complete
 */
(function ($) {
  'use strict';

  // ---------- helpers ----------
  function isOn(val) {
    return val === true || val === 1 || val === '1' || val === 'on' || val === 'true';
  }
  function isOff(val) {
    return val === false || val === 0 || val === '0' || val === 'off' || val === '' || val === null || typeof val === 'undefined';
  }

  function setCookie(name, value, days) {
    var expires = '';
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/';
  }

  function getCookie(name) {
    var prefix = name + '=';
    var parts = document.cookie.split(';');
    for (var i = 0; i < parts.length; i++) {
      var part = parts[i].trim();
      if (part.indexOf(prefix) === 0) {
        return decodeURIComponent(part.substring(prefix.length));
      }
    }
    return '';
  }

  function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
  }
  function unlockNextStepButtons() {
    document.querySelectorAll('[data-mp-next-step-url]').forEach(function (button) {
      var nextUrl = button.getAttribute('data-mp-next-step-url');
      if (!nextUrl) {
        return;
      }

      var link = document.createElement('a');
      link.href = nextUrl;
      link.className = button.className;
      link.textContent = button.textContent;
      link.removeAttribute('disabled');
      link.removeAttribute('aria-disabled');
      link.removeAttribute('title');
      link.style.removeProperty('opacity');
      link.style.removeProperty('cursor');
      button.replaceWith(link);
    });

    document.querySelectorAll('.ld-navigation__next-link, .ld-js-next-lesson, .ld-lesson-nav-next a, .ld-content-action__next .ld-button').forEach(function (link) {
      var nextUrl = link.getAttribute('data-mp-next-url');
      if (nextUrl && link.tagName.toLowerCase() === 'a') {
        link.setAttribute('href', nextUrl);
      }

      link.removeAttribute('aria-disabled');
      link.classList.remove('is-disabled');
      link.style.removeProperty('opacity');
      link.style.removeProperty('cursor');
      link.style.removeProperty('pointer-events');
    });
  }

  function submitStepCompleteForm(form, stepMain, submitButton) {
    if (!form || form.dataset.mpSubmitting === 'true') {
      return;
    }

    form.dataset.mpSubmitting = 'true';

    if (submitButton) {
      submitButton.disabled = true;
    }

    fetch(form.getAttribute('action') || window.location.href, {
      method: (form.getAttribute('method') || 'POST').toUpperCase(),
      body: new FormData(form),
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Mark complete failed');
      }

      if (stepMain) {
        stepMain.setAttribute('data-mp-step-complete', '1');
      }

      unlockNextStepButtons();

      if (submitButton) {
        submitButton.value = 'Completed';
        submitButton.textContent = 'Completed';
        submitButton.setAttribute('aria-label', 'Completed');
      }
    }).catch(function () {
      form.dataset.mpSubmitting = 'false';
      if (submitButton) {
        submitButton.disabled = false;
      }
      form.submit();
    });
  }

  $(function () {
    var isStepPage = document.body.classList.contains('single-sfwd-topic') || document.body.classList.contains('single-sfwd-lessons');
    var stepMain = document.querySelector('main[data-mp-step-complete]');
    var stepIsComplete = stepMain ? stepMain.getAttribute('data-mp-step-complete') === '1' : true;

    // =========================
    // 1) Add Franklin wrapper to LD navigation
    // =========================
    document.querySelectorAll('.ld-navigation').forEach(function (nav) {
      nav.classList.add('u-wrap');
    });

    // =========================
    // 2) Style "Mark Complete" button (keep LD functionality)
    // =========================
    document.querySelectorAll('.learndash_mark_complete_button').forEach(function (btn) {
      btn.classList.remove(
        'ld-navigation__progress-mark-complete-button',
        'ld-navigation__progress-mark-complete-button--topic',
        'ld--ignore-inline-css'
      );

      btn.classList.add('mp', 'c-button', 'c-button--blue');

      // remove inline styles LD may inject
      btn.removeAttribute('style');
      btn.style.setProperty('background', '#00a2c2', 'important');
      btn.style.setProperty('background-color', '#00a2c2', 'important');
      btn.style.setProperty('background-image', 'none', 'important');
      btn.style.setProperty('border', 'none', 'important');
      btn.style.setProperty('border-color', '#00a2c2', 'important');
      btn.style.setProperty('color', '#ffffff', 'important');
      btn.style.setProperty('box-shadow', 'none', 'important');
      btn.value = 'Mark as complete';
      btn.textContent = 'Mark as complete';
      btn.setAttribute('aria-label', 'Mark as complete');
    });

    document.querySelectorAll('.ld-progress, .ld-status, .ld-topic-status, .ld-progress-inline').forEach(function (el) {
      if (!el.querySelector('.learndash_mark_complete_button, #learndash_mark_complete_button, input[type="submit"]')) {
        el.remove();
      }
    });

    document.querySelectorAll('.ld-navigation__progress > *').forEach(function (el) {
      if (!el.querySelector('.learndash_mark_complete_button, #learndash_mark_complete_button, input[type="submit"]')) {
        el.remove();
      }
    });

    // =========================
    // 3) Style LearnDash Native Prev/Next Navigation
    // =========================
    document.querySelectorAll('.ld-navigation__previous-link, .ld-navigation__next-link, .ld-js-next-lesson, .ld-js-previous-lesson, .ld-lesson-nav-previous a, .ld-lesson-nav-next a, .ld-content-action__previous .ld-button, .ld-content-action__next .ld-button, .ld-navigation a.ld-primary-color, .ld-course-step-back a').forEach(function (link) {
      // Remove LD bloat
      link.classList.remove('ld-primary-color-hover', 'ld-primary-color', 'ld-button');

      link.classList.add('mp', 'c-button', 'c-button--outline-green');
    });

    if (!stepIsComplete) {
      document.querySelectorAll('.ld-navigation__next-link, .ld-js-next-lesson, .ld-lesson-nav-next a, .ld-content-action__next .ld-button').forEach(function (link) {
        if (link.tagName.toLowerCase() === 'a') {
          if (link.getAttribute('href')) {
            link.setAttribute('data-mp-next-url', link.getAttribute('href'));
          }
          link.removeAttribute('href');
        }

        link.setAttribute('aria-disabled', 'true');
        link.classList.add('is-disabled');
        link.style.setProperty('opacity', '0.45', 'important');
        link.style.setProperty('cursor', 'not-allowed', 'important');
        link.style.setProperty('pointer-events', 'none', 'important');
      });
    }

    if (isStepPage) {
      document.addEventListener('click', function (event) {
        if (stepIsComplete) {
          return;
        }

        var submitButton = event.target.closest('form[name="sfwd-mark-complete"] input[type="submit"], form[name="sfwd-mark-complete"] button[type="submit"], .learndash_mark_complete_button');
        if (!submitButton) {
          return;
        }

        var form = submitButton.closest('form[name="sfwd-mark-complete"]');
        if (!form) {
          return;
        }

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
          event.stopImmediatePropagation();
        }

        stepIsComplete = true;
        submitStepCompleteForm(form, stepMain, submitButton);
      }, true);

      document.addEventListener('submit', function (event) {
        if (stepIsComplete) {
          return;
        }

        var form = event.target.closest('form[name="sfwd-mark-complete"]');
        if (!form) {
          return;
        }

        var submitButton = form.querySelector('input[type="submit"], button[type="submit"], .learndash_mark_complete_button');

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
          event.stopImmediatePropagation();
        }

        stepIsComplete = true;
        submitStepCompleteForm(form, stepMain, submitButton);
      }, true);
    }

    // =========================
    // 4) Small LearnDash video options helper
    // =========================
    var $wrap = $('.mp-topic-video-wrapper').first();
    if (!$wrap.length) return;

    var $video = $wrap.find('video').first();
    if (!$video.length) return;

      var v = $video[0];
      var $ld = $wrap.find('.ld-video').first();

      var progression = isOn($wrap.data('ld-progression')) || isOn($ld.data('video-progression'));
      var autostart = isOn($wrap.data('ld-autostart')) || isOn($ld.data('video-autostart'));
      var focusPause = isOn($wrap.data('ld-focus-pause')) || isOn($ld.data('video-focus-pause'));
      var controlsOn = !isOff($wrap.data('ld-controls'));
      var autoComplete = isOn($wrap.data('ld-auto-complete'));
      var isCompleted = isOn($wrap.data('ld-is-complete'));

      // Use LearnDash cookie key if present, else fallback to topic id.
      // LearnDash only emits data-video-cookie-key when "Track Video Progress"
      // is enabled (globally or per-topic), so its presence is the source of
      // truth for whether resume should be active.
      var fallbackId = $wrap.data('ld-step-id') || $wrap.data('ld-topic-id');
      var ldCookieKey = $ld.data('video-cookie-key');
      var cookieKey = ldCookieKey || ('mp_ld_video_' + fallbackId);
      var resume = !!ldCookieKey
        || isOn($wrap.data('ld-resume'))
        || isOn($ld.data('video-resume'));

      // Controls
      if (!controlsOn) {
        v.controls = false;
        $video.removeAttr('controls');
      }

      // Match LearnDash's native cookie name exactly — `learndash-video-progress-<hash>` —
      // so this resume state is interchangeable with LD's own storage if LD ever
      // wires up `_wpmejsSettings.success` on this template.
      var resumeStorageKey = cookieKey;
      var resumeApplied = !resume;
      var resumeTargetTime = 0;
      var resumeRetryTimer = null;
      var resumeRetryCount = 0;

      function saveResumePosition() {
        if (!resume || !resumeApplied || !isFinite(v.currentTime) || v.currentTime <= 0) {
          return;
        }

        localStorage.setItem(resumeStorageKey, String(v.currentTime));
        setCookie(resumeStorageKey, String(v.currentTime), 30);
      }

      function restoreResumePosition() {
        if (!resume || resumeApplied || !resumeTargetTime || !isFinite(resumeTargetTime)) {
          return;
        }

        var maxTime = isFinite(v.duration) && v.duration > 0 ? Math.max(0, v.duration - 1) : resumeTargetTime;
        var targetTime = Math.min(resumeTargetTime, maxTime);

        if (targetTime > 0) {
          try {
            v.currentTime = targetTime;
          } catch (e) {
            return;
          }
        }

        if (Math.abs(v.currentTime - targetTime) <= 1) {
          resumeApplied = true;

          if (resumeRetryTimer) {
            window.clearInterval(resumeRetryTimer);
            resumeRetryTimer = null;
          }
        }
      }

      function applyResumePosition() {
        if (!resume) {
          return;
        }

        var saved = getCookie(resumeStorageKey) || localStorage.getItem(resumeStorageKey);
        var savedTime = saved ? parseFloat(saved) : 0;

        if (!savedTime || savedTime <= 0 || !isFinite(savedTime)) {
          resumeApplied = true;
          return;
        }

        resumeTargetTime = savedTime;
        restoreResumePosition();
      }

      function tryAutostart() {
        if (!autostart) {
          return;
        }

        v.muted = true;
        var playPromise = v.play();
        if (playPromise && playPromise.catch) {
          playPromise.catch(function () { });
        }
      }

      if (resume) {
        if (v.readyState >= 1) {
          applyResumePosition();
        } else {
          v.addEventListener('loadedmetadata', applyResumePosition, { once: true });
        }

        v.addEventListener('loadeddata', restoreResumePosition);
        v.addEventListener('canplay', restoreResumePosition);
        v.addEventListener('play', restoreResumePosition);

        if (resume) {
          resumeRetryTimer = window.setInterval(function () {
            if (resumeApplied || resumeRetryCount >= 12) {
              if (resumeRetryTimer) {
                window.clearInterval(resumeRetryTimer);
                resumeRetryTimer = null;
              }
              return;
            }

            resumeRetryCount += 1;
            restoreResumePosition();
          }, 250);
        }

        $video.on('timeupdate', function () {
          saveResumePosition();
        });

        $video.on('pause', function () {
          saveResumePosition();
        });

        $video.on('seeking seeked', function () {
          saveResumePosition();
        });

        $video.on('ended', function () {
          localStorage.removeItem(resumeStorageKey);
          deleteCookie(resumeStorageKey);
        });

        window.addEventListener('pagehide', saveResumePosition);
        window.addEventListener('beforeunload', saveResumePosition);
        document.addEventListener('visibilitychange', function () {
          if (document.visibilityState === 'hidden') {
            saveResumePosition();
          }
        });
      }

      if (autostart) {
        if (v.readyState >= 2) {
          tryAutostart();
        } else {
          v.addEventListener('canplay', tryAutostart, { once: true });
        }
      }

      // Pause on unfocus
      if (focusPause) {
        $(window).on('blur', function () {
          if (!v.paused) v.pause();
        });
      }

      // Progression: keep Mark Complete visible so users can manually complete
      // the topic when needed, while still supporting optional auto-complete.
      if (progression) {
        var $mark = $('.learndash_mark_complete_button, #learndash_mark_complete_button, form[name="sfwd-mark-complete"] input[type="submit"]').first();

        if ($mark.length && !isCompleted) {
          $mark.hide();
        }

        $video.on('ended', function () {
          if ($mark.length) $mark.show();
          localStorage.setItem(cookieKey + '_state', 'complete');

          if (autoComplete && $mark.length) {
            setTimeout(function () { $mark.trigger('click'); }, 300);
          }
        });
      }
    });

  })(jQuery);
