<?php
/**
 * Template part: MP Academy – Homepage Hero v3 (Figma logged-out)
 */
if (!defined('ABSPATH')) exit;

$q = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
$hero_bg = home_url('/wp-content/uploads/2025/12/mp-x-bg.png');
?>

<section class="mpa-home-hero">
  <div class="u-wrap">
    <div class="c-hero mpa-home-hero-inner">
      <div class="c-hero__wrap mpa-home-hero__wrap">
        <div class="c-hero__main mpa-home-hero-header">
          <h1 class="c-hero__heading">
            MP Academy
          </h1>
          <p class="c-hero__lede mpa-home-hero-subtitle">
            Your home for free training courses and how-to videos
          </p>

          <form
            class="c-hero__search c-form c-form--search mpa-home-hero-search"
            action="<?php echo esc_url(home_url('/')); ?>"
            method="get"
            role="search"
          >
            <div class="c-form__input-wrap">
              <label for="academy-q" class="u-hidden"><?php esc_html_e('Search', 'mp-academy'); ?></label>
              <input
                class="c-input c-input--with-button"
                type="search"
                name="s"
                id="academy-q"
                value="<?php echo esc_attr($q); ?>"
                placeholder="<?php esc_attr_e('Search', 'mp-academy'); ?>"
                aria-label="<?php esc_attr_e('Search courses', 'mp-academy'); ?>"
              />
              <input type="hidden" name="post_type" value="sfwd-courses" />
              <button type="submit" class="mpa-home-hero-search__submit" aria-label="<?php esc_attr_e('Search', 'mp-academy'); ?>">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false" class="mpa-home-hero-search__icon">
                  <circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"></circle>
                  <path d="M20 20L16.65 16.65" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
              </button>
            </div>
          </form>
        </div>
        <div class="c-hero__media-wrap mpa-home-hero__media" style="background-image: url('<?php echo esc_url($hero_bg); ?>');"></div>
      </div>
    </div>
  </div>
</section>
