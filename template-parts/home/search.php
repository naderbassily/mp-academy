<?php
/**
 * Template part: MP Academy – Search for courses (Franklin c-filter-search base)
 */
if (!defined('ABSPATH')) exit;

$q = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
?>
<section class="mp mp-course-search " aria-labelledby="mp-course-search-title">
  <div class="u-wrap ">
    <div class="u-bg-petrol-step-3">

<form class="mp c-filter-search mp-course-search__row"
      action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search" aria-labelledby="mp-course-search-title">

  <!-- Left heading (not clickable) -->
  <h2 id="mp-course-search-title" class="u-step-1 u-strong mp-course-search__label">
    <?php esc_html_e('Search for courses', 'mp-academy'); ?>
  </h2>

  <!-- Field group (Franklin component) -->
  <div class="c-filter-search__fields mp-course-search__fields">
    <input
      class="c-input c-input--with-button"
      type="search"
      name="s"
      id="academy-q"
      value="<?php echo esc_attr($q); ?>"
      placeholder="<?php esc_attr_e('Search by product type, industry or measurement type', 'mp-academy'); ?>"
      aria-label="<?php esc_attr_e('Search courses', 'mp-academy'); ?>"
    />
    <!-- Magnifier (decorative; not submitting) -->
    <button type="button" class="c-button--reset u-blue mp-course-search__magnifier" aria-hidden="true" tabindex="-1">
      <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--search">
        <use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#search"></use>
      </svg>
    </button>
  </div>

  <input type="hidden" name="post_type" value="sfwd-courses" />

  <!-- The ONLY interactive submit -->
  <button type="submit" class="c-button c-button--success mp-course-search__submit">
    <?php esc_html_e('Search', 'mp-academy'); ?>
  </button>
</form>

    </div>
  </div>
</section>
