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

  function hasStartedQuizView() {
    var firstQuestion = document.querySelector(
      '.single-sfwd-quiz .wpProQuiz_listItem, .single-sfwd-quiz .wpProQuiz_questionList'
    );
    var results = document.querySelector(
      '.single-sfwd-quiz .wpProQuiz_results, .single-sfwd-quiz .wpProQuiz_response, .single-sfwd-quiz .wpProQuiz_catOverview'
    );

    return isVisible(firstQuestion) || isVisible(results);
  }

  function setQuizStartedState() {
    var startBlock = document.querySelector(
      '.single-sfwd-quiz .wpProQuiz_text, .single-sfwd-quiz .wpProQuiz_startOnlyRegisteredUser'
    );
    var started = document.body.classList.contains('mp-quiz-started');

    if (!started) {
      started = hasStartedQuizView() && (!startBlock || !isVisible(startBlock));
    }

    document.body.classList.toggle('mp-quiz-started', started);
  }

  function labelVisibleQuestions() {
    var questions = document.querySelectorAll('.single-sfwd-quiz .wpProQuiz_listItem');

    questions.forEach(function (question, index) {
      var questionNumber = index + 1;

      var title = question.querySelector('.wpProQuiz_question_text');
      if (!title) {
        return;
      }

      var original = title.dataset.mpOriginalTitle || title.textContent.trim();
      title.dataset.mpOriginalTitle = original.replace(/^Q\d+\s*:\s*/i, '');

      var heading = title.querySelector('.mp-quiz-question-heading');
      if (!heading) {
        title.innerHTML = '';
        heading = document.createElement('h2');
        heading.className = 'c-h c-h--step-2 mp-quiz-question-heading';
        title.appendChild(heading);
      }

      heading.textContent = 'Q' + questionNumber + ': ' + title.dataset.mpOriginalTitle;
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

  function styleQuestionButton(button) {
    if (!button) {
      return;
    }

    button.style.setProperty('background', 'transparent', 'important');
    button.style.setProperty('background-color', 'transparent', 'important');
    button.style.setProperty('background-image', 'none', 'important');
    button.style.setProperty('border', '2px solid #00b140', 'important');
    button.style.setProperty('border-radius', '0.5rem', 'important');
    button.style.setProperty('box-shadow', 'none', 'important');
    button.style.setProperty('color', '#00b140', 'important');
    button.style.setProperty('font-family', '"Inter", "InterVariable", sans-serif', 'important');
    button.style.setProperty('font-size', '1rem', 'important');
    button.style.setProperty('font-weight', '700', 'important');
    button.style.setProperty('height', '44px', 'important');
    button.style.setProperty('min-width', '170px', 'important');
    button.style.setProperty('padding', '0.625rem 1.1rem', 'important');
    button.style.setProperty('text-shadow', 'none', 'important');

    if (button.dataset.mpQuestionButtonBound === 'true') {
      return;
    }

    button.dataset.mpQuestionButtonBound = 'true';

    ['mouseenter', 'mouseover', 'focus'].forEach(function (eventName) {
      button.addEventListener(eventName, function () {
        button.style.setProperty('background', '#00b140', 'important');
        button.style.setProperty('background-color', '#00b140', 'important');
        button.style.setProperty('background-image', 'none', 'important');
        button.style.setProperty('border-color', '#00b140', 'important');
        button.style.setProperty('color', '#ffffff', 'important');
      });
    });

    ['mouseleave', 'mouseout', 'blur'].forEach(function (eventName) {
      button.addEventListener(eventName, function () {
        button.style.setProperty('background', 'transparent', 'important');
        button.style.setProperty('background-color', 'transparent', 'important');
        button.style.setProperty('background-image', 'none', 'important');
        button.style.setProperty('border-color', '#00b140', 'important');
        button.style.setProperty('color', '#00b140', 'important');
      });
    });
  }

  function syncQuestionButtons() {
    var buttons = document.querySelectorAll(
      '.single-sfwd-quiz .wpProQuiz_button.wpProQuiz_QuestionButton, .single-sfwd-quiz .wpProQuiz_button2.wpProQuiz_QuestionButton, .single-sfwd-quiz input.wpProQuiz_QuestionButton'
    );

    buttons.forEach(function (button) {
      styleQuestionButton(button);
    });
  }

  function styleResultActionButton(button, filled) {
    if (!button) {
      return;
    }

    button.style.setProperty('align-items', 'center', 'important');
    button.style.setProperty('appearance', 'none', 'important');
    button.style.setProperty('background', filled ? '#00b140' : 'transparent', 'important');
    button.style.setProperty('background-color', filled ? '#00b140' : 'transparent', 'important');
    button.style.setProperty('background-image', 'none', 'important');
    button.style.setProperty('border', '2px solid #00b140', 'important');
    button.style.setProperty('border-radius', '0.5rem', 'important');
    button.style.setProperty('box-shadow', 'none', 'important');
    button.style.setProperty('box-sizing', 'border-box', 'important');
    button.style.setProperty('color', filled ? '#ffffff' : '#00b140', 'important');
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
    button.style.setProperty('white-space', 'nowrap', 'important');
    button.style.setProperty('width', '170px', 'important');
  }

  function syncResultActionButtons() {
    var resultButtons = document.querySelectorAll(
      '.single-sfwd-quiz .ld-quiz-actions input[name="restartQuiz"], .single-sfwd-quiz .ld-quiz-actions input[name="reShowQuestion"]'
    );
    var continueButtons = document.querySelectorAll(
      '.single-sfwd-quiz .ld-quiz-actions .quiz_continue_link a, .single-sfwd-quiz .ld-quiz-actions .quiz_continue_link .ld-button'
    );

    resultButtons.forEach(function (button) {
      button.classList.add('mp', 'c-button', 'c-button--outline-green');
      styleResultActionButton(button, false);
    });

    continueButtons.forEach(function (button) {
      button.classList.add('mp', 'c-button', 'c-button--green');
      styleResultActionButton(button, true);
    });
  }

  function getVisibleQuestionItems() {
    var questions = document.querySelectorAll('.single-sfwd-quiz .wpProQuiz_listItem');

    return Array.prototype.filter.call(questions, function (question) {
      return isVisible(question);
    });
  }

  function clearQuestionValidation(question) {
    if (!question) {
      return;
    }

    question.classList.remove('mp-quiz-question-error');

    var error = question.querySelector('.mp-quiz-validation-message');
    if (error) {
      error.remove();
    }
  }

  function renderQuestionValidation(question, message) {
    if (!question) {
      return;
    }

    clearQuestionValidation(question);
    question.classList.add('mp-quiz-question-error');

    var actions = question.querySelector('.wpProQuiz_questionList + div');
    var error = document.createElement('p');
    error.className = 'mp-quiz-validation-message';
    error.textContent = message;

    if (actions && actions.parentNode) {
      actions.parentNode.insertBefore(error, actions);
    } else {
      question.appendChild(error);
    }
  }

  function hasCheckedInput(questionList) {
    return !!questionList.querySelector('.wpProQuiz_questionInput:checked');
  }

  function hasTextAnswer(questionList, selector) {
    var fields = questionList.querySelectorAll(selector);

    return Array.prototype.some.call(fields, function (field) {
      return field.value && field.value.trim() !== '';
    });
  }

  function hasAnsweredQuestion(question) {
    var questionList = question.querySelector('.wpProQuiz_questionList');

    if (!questionList) {
      return true;
    }

    var type = questionList.getAttribute('data-type') || '';

    if (type === 'single' || type === 'multiple' || type === 'assessment_answer') {
      return hasCheckedInput(questionList);
    }

    if (type === 'free_answer') {
      return hasTextAnswer(questionList, '.wpProQuiz_questionInput[type="text"], .wpProQuiz_questionInput[type="email"], .wpProQuiz_questionInput[type="number"], .wpProQuiz_questionInput[type="search"], .wpProQuiz_questionInput[type="tel"], .wpProQuiz_questionInput[type="url"]');
    }

    if (type === 'cloze_answer') {
      return hasTextAnswer(questionList, '.wpProQuiz_cloze input');
    }

    if (type === 'essay') {
      return hasTextAnswer(questionList, '.wpProQuiz_questionEssay');
    }

    return true;
  }

  function validateVisibleQuestions() {
    var visibleQuestions = getVisibleQuestionItems();
    var firstInvalidQuestion = null;

    visibleQuestions.forEach(function (question) {
      var answered = hasAnsweredQuestion(question);

      if (answered) {
        clearQuestionValidation(question);
        return;
      }

      renderQuestionValidation(question, 'Please select an answer first.');

      if (!firstInvalidQuestion) {
        firstInvalidQuestion = question;
      }
    });

    if (firstInvalidQuestion) {
      firstInvalidQuestion.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return false;
    }

    return true;
  }

  function shouldValidateButton(button) {
    if (!button || !button.name) {
      return false;
    }

    return ['next', 'checkSingle', 'wpProQuiz_pageRight'].indexOf(button.name) !== -1;
  }

  function bindAnswerListeners() {
    var containers = document.querySelectorAll('.single-sfwd-quiz .wpProQuiz_listItem');

    containers.forEach(function (question) {
      if (question.dataset.mpValidationBound === 'true') {
        return;
      }

      question.dataset.mpValidationBound = 'true';

      question.addEventListener('change', function () {
        if (hasAnsweredQuestion(question)) {
          clearQuestionValidation(question);
        }
      });

      question.addEventListener('input', function () {
        if (hasAnsweredQuestion(question)) {
          clearQuestionValidation(question);
        }
      });
    });
  }

  function observeQuizUiChanges() {
    var content = document.querySelector('.single-sfwd-quiz .wpProQuiz_content');

    if (!content || content.dataset.mpUiObserverBound === 'true') {
      return;
    }

    content.dataset.mpUiObserverBound = 'true';

    var observer = new MutationObserver(function () {
      window.requestAnimationFrame(function () {
        syncQuestionButtons();
        syncResultActionButtons();
        setQuizStartedState();
        labelVisibleQuestions();
        bindAnswerListeners();
      });
    });

    observer.observe(content, {
      childList: true,
      subtree: true,
      characterData: true,
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    syncStartQuizButton();
    syncQuestionButtons();
    syncResultActionButtons();
    setQuizStartedState();
    labelVisibleQuestions();
    bindAnswerListeners();
    observeQuizUiChanges();
    window.setTimeout(syncStartQuizButton, 150);
    window.setTimeout(syncQuestionButtons, 150);
    window.setTimeout(syncResultActionButtons, 150);
    window.setTimeout(setQuizStartedState, 150);
    window.setTimeout(labelVisibleQuestions, 150);
    window.setTimeout(bindAnswerListeners, 150);
    window.setTimeout(observeQuizUiChanges, 150);
    window.setTimeout(syncStartQuizButton, 600);
    window.setTimeout(syncQuestionButtons, 600);
    window.setTimeout(syncResultActionButtons, 600);
    window.setTimeout(setQuizStartedState, 600);
    window.setTimeout(labelVisibleQuestions, 600);
    window.setTimeout(bindAnswerListeners, 600);
    window.setTimeout(observeQuizUiChanges, 600);

    document.addEventListener(
      'click',
      function (event) {
        var startButton = event.target.closest('.single-sfwd-quiz [name="startQuiz"]');
        if (startButton) {
          document.body.classList.add('mp-quiz-started');
          window.setTimeout(setQuizStartedState, 50);
          window.setTimeout(setQuizStartedState, 250);
          window.setTimeout(setQuizStartedState, 750);
        }

        var button = event.target.closest('.single-sfwd-quiz .wpProQuiz_QuestionButton');

        if (!shouldValidateButton(button)) {
          return;
        }

        if (validateVisibleQuestions()) {
          return;
        }

        event.preventDefault();
        event.stopPropagation();

        if (typeof event.stopImmediatePropagation === 'function') {
          event.stopImmediatePropagation();
        }
      },
      true
    );

    document.addEventListener('click', function (event) {
      if (event.target.closest('.single-sfwd-quiz .wpProQuiz_content')) {
        window.setTimeout(syncQuestionButtons, 100);
        window.setTimeout(setQuizStartedState, 100);
        window.setTimeout(labelVisibleQuestions, 100);
        window.setTimeout(bindAnswerListeners, 100);
      }
    });
  });
})();
