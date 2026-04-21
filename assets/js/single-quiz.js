/**
 * MP Academy – Quiz page UI fixes
 * Force the Start Quiz control to match the topic nav button styling,
 * even if LearnDash rewrites classes/styles after render.
 */
(function () {
  'use strict';

  function isVisible(element) {
    return !!(element && element.offsetParent !== null);
  }

  function setQuizStartedState() {
    var startBlock = document.querySelector(
      '.single-sfwd-quiz .wpProQuiz_text, .single-sfwd-quiz .wpProQuiz_startOnlyRegisteredUser'
    );
    var firstQuestion = document.querySelector(
      '.single-sfwd-quiz .wpProQuiz_listItem, .single-sfwd-quiz .wpProQuiz_questionList'
    );
    var started = isVisible(firstQuestion) && (!startBlock || !isVisible(startBlock));

    document.body.classList.toggle('mp-quiz-started', started);

    var statusBadge = document.querySelector('.single-sfwd-quiz .mp-quiz-status-badge');
    if (statusBadge && statusBadge.dataset.status === 'not-started' && started) {
      statusBadge.dataset.status = 'in-progress';
      statusBadge.textContent = 'In progress';
      statusBadge.style.backgroundColor = '#dbeafe';
      statusBadge.style.color = '#1d4ed8';
      statusBadge.style.border = 'none';
    }
  }

  function labelVisibleQuestions() {
    var questions = document.querySelectorAll('.single-sfwd-quiz .wpProQuiz_listItem');
    var visibleIndex = 0;

    questions.forEach(function (question) {
      if (!isVisible(question)) {
        return;
      }

      visibleIndex += 1;

      var title = question.querySelector('.wpProQuiz_question_text');
      if (!title) {
        return;
      }

      var original = title.dataset.mpOriginalTitle || title.textContent.trim();
      title.dataset.mpOriginalTitle = original.replace(/^Q\d+\s*:\s*/i, '');
      title.textContent = 'Q' + visibleIndex + ': ' + title.dataset.mpOriginalTitle;
    });
  }

  function styleStartQuizButton(button) {
    if (!button) {
      return;
    }

    button.style.setProperty('align-items', 'center', 'important');
    button.style.setProperty('appearance', 'none', 'important');
    button.style.setProperty('background', 'transparent', 'important');
    button.style.setProperty('background-color', 'transparent', 'important');
    button.style.setProperty('background-image', 'none', 'important');
    button.style.setProperty('border', '2px solid #00b140', 'important');
    button.style.setProperty('border-radius', '0.5rem', 'important');
    button.style.setProperty('box-shadow', 'none', 'important');
    button.style.setProperty('box-sizing', 'border-box', 'important');
    button.style.setProperty('color', '#00b140', 'important');
    button.style.setProperty('cursor', 'pointer', 'important');
    button.style.setProperty('display', 'inline-flex', 'important');
    button.style.setProperty('float', 'none', 'important');
    button.style.setProperty('font-family', '"Inter", "InterVariable", sans-serif', 'important');
    button.style.setProperty('font-size', '1rem', 'important');
    button.style.setProperty('font-weight', '700', 'important');
    button.style.setProperty('height', '44px', 'important');
    button.style.setProperty('justify-content', 'center', 'important');
    button.style.setProperty('line-height', '1.2', 'important');
    button.style.setProperty('min-width', '170px', 'important');
    button.style.setProperty('outline', 'none', 'important');
    button.style.setProperty('padding', '0.625rem 1.1rem', 'important');
    button.style.setProperty('text-decoration', 'none', 'important');
    button.style.setProperty('text-shadow', 'none', 'important');
    button.style.setProperty('width', 'auto', 'important');

    ['mouseenter', 'mouseover', 'focus'].forEach(function (eventName) {
      button.addEventListener(eventName, function () {
        button.style.setProperty('background', '#00b140', 'important');
        button.style.setProperty('background-color', '#00b140', 'important');
        button.style.setProperty('color', '#ffffff', 'important');
        button.style.setProperty('border', '2px solid #00b140', 'important');
        button.style.setProperty('box-shadow', 'none', 'important');
      });
    });

    ['mouseleave', 'mouseout', 'blur'].forEach(function (eventName) {
      button.addEventListener(eventName, function () {
        button.style.setProperty('background', 'transparent', 'important');
        button.style.setProperty('background-color', 'transparent', 'important');
        button.style.setProperty('color', '#00b140', 'important');
        button.style.setProperty('border', '2px solid #00b140', 'important');
        button.style.setProperty('box-shadow', 'none', 'important');
      });
    });
  }

  function styleStartQuizWrapper(wrapper) {
    if (!wrapper) {
      return;
    }

    wrapper.style.setProperty('background', 'transparent', 'important');
    wrapper.style.setProperty('background-color', 'transparent', 'important');
    wrapper.style.setProperty('border', '0', 'important');
    wrapper.style.setProperty('box-shadow', 'none', 'important');
    wrapper.style.setProperty('display', 'inline-flex', 'important');
    wrapper.style.setProperty('padding', '0', 'important');
  }

  function styleParentChain(button) {
    if (!button) {
      return;
    }

    var node = button.parentElement;
    var depth = 0;

    while (node && depth < 4) {
      node.style.setProperty('background', 'transparent', 'important');
      node.style.setProperty('background-color', 'transparent', 'important');
      node.style.setProperty('background-image', 'none', 'important');
      node.style.setProperty('border-color', 'transparent', 'important');
      node.style.setProperty('box-shadow', 'none', 'important');
      depth += 1;
      node = node.parentElement;
    }
  }

  function syncStartQuizButton() {
    var wrappers = document.querySelectorAll(
      '.single-sfwd-quiz .startQuiz, .single-sfwd-quiz .wpProQuiz_text, .single-sfwd-quiz .wpProQuiz_startOnlyRegisteredUser'
    );

    wrappers.forEach(function (wrapper) {
      if (wrapper.classList.contains('startQuiz')) {
        styleStartQuizWrapper(wrapper);
      }

      var button = wrapper.querySelector(
        'input[name="startQuiz"], .wpProQuiz_button[name="startQuiz"], .wpProQuiz_button2, input.wpProQuiz_button2'
      );

      if (button) {
        styleStartQuizButton(button);
        styleParentChain(button);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    syncStartQuizButton();
    setQuizStartedState();
    labelVisibleQuestions();
    window.setTimeout(syncStartQuizButton, 150);
    window.setTimeout(setQuizStartedState, 150);
    window.setTimeout(labelVisibleQuestions, 150);
    window.setTimeout(syncStartQuizButton, 600);
    window.setTimeout(setQuizStartedState, 600);
    window.setTimeout(labelVisibleQuestions, 600);

    document.addEventListener('click', function (event) {
      if (event.target.closest('.single-sfwd-quiz .wpProQuiz_content')) {
        window.setTimeout(setQuizStartedState, 100);
        window.setTimeout(labelVisibleQuestions, 100);
      }
    });
  });
})();
