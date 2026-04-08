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

  $(function () {

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

      // (Franklin completely removed from Mark Complete buttons as requested)
      
      // remove inline styles LD may inject
      btn.removeAttribute('style');
      btn.value = 'Mark topic as complete';
      btn.textContent = 'Mark topic as complete';
      btn.setAttribute('aria-label', 'Mark topic as complete');
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

      // (Franklin completely removed from pagination links)
    });

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
      var resume = isOn($wrap.data('ld-resume')) || isOn($ld.data('video-resume'));
      var controlsOn = !isOff($wrap.data('ld-controls'));
      var autoComplete = isOn($wrap.data('ld-auto-complete'));

      // Use LearnDash cookie key if present, else fallback to topic id
      var fallbackId = $wrap.data('ld-topic-id');
      var cookieKey = $ld.data('video-cookie-key') || ('mp_ld_video_' + fallbackId);

      // Controls
      if (!controlsOn) {
        v.controls = false;
        $video.removeAttr('controls');
      }

      // Autostart (muted for autoplay policies)
      if (autostart) {
        v.muted = true;
        var p = v.play();
        if (p && p.catch) p.catch(function () { });
      }

      // Resume
      if (resume) {
        var saved = localStorage.getItem(cookieKey + '_time');
        if (saved && parseFloat(saved) > 0) {
          v.currentTime = parseFloat(saved);
        }
        $video.on('timeupdate', function () {
          localStorage.setItem(cookieKey + '_time', v.currentTime);
        });
      }

      // Pause on unfocus
      if (focusPause) {
        $(window).on('blur', function () {
          if (!v.paused) v.pause();
        });
      }

      // Progression: hide Mark Complete until ended (+ optional auto-complete)
      if (progression) {
        var $mark = $('.learndash_mark_complete_button, #learndash_mark_complete_button, form[name="sfwd-mark-complete"] input[type="submit"]').first();
        if ($mark.length) $mark.hide();

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
