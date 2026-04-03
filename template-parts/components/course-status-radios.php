<?php
if (!defined('ABSPATH')) exit;
?>

<div class="u-margin-bottom-m u-flex u-flex-wrap u-gap-s u-align-center">
  <span class="u-text-bold u-margin-right-xs">Show:</span>

  <label class="u-flex u-align-center u-gap-xxs">
    <input type="radio" name="course-status" value="all" checked>
    <span>All courses</span>
  </label>

  <label class="u-flex u-align-center u-gap-xxs">
    <input type="radio" name="course-status" value="in-progress">
    <span>In progress</span>
  </label>

  <label class="u-flex u-align-center u-gap-xxs">
    <input type="radio" name="course-status" value="not-started">
    <span>Not started</span>
  </label>

  <label class="u-flex u-align-center u-gap-xxs">
    <input type="radio" name="course-status" value="completed">
    <span>Completed</span>
  </label>
</div>
