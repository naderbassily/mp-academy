<?php
/**
 * Template part: MP Academy – Homepage Hero v3 (Figma logged-out)
 */
if (!defined('ABSPATH')) exit;

$q = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
$hero_fallback_bg = home_url('/wp-content/uploads/2025/12/mp-x-bg.png');
$hero_title = function_exists('get_field') ? (string) get_field('hero_title') : '';
$hero_content = function_exists('get_field') ? (string) get_field('hero_content') : '';
$hero_image = function_exists('get_field') ? get_field('hero_image') : '';

if (is_array($hero_image)) {
  $hero_image = isset($hero_image['url']) ? (string) $hero_image['url'] : '';
}

$hero_bg = $hero_image ?: $hero_fallback_bg;
?>

<section class="mpa-home-hero">
  <div class="u-wrap">
    <div class="c-hero mpa-home-hero-inner" style="--mp-home-hero-bg: url('<?php echo esc_url($hero_bg); ?>');">
      <div class="c-hero__wrap mpa-home-hero__wrap">
        <div class="c-hero__main mpa-home-hero-header">
          <h1 class="c-hero__heading">
            <?php echo esc_html($hero_title ?: 'Malvern Panalytical Academy'); ?>
          </h1>
          <p class="c-hero__lede mpa-home-hero-subtitle">
            <?php echo wp_kses_post($hero_content ?: 'Your home for free training courses and how-to videos'); ?>
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
              <button type="submit" class="mpa-home-hero-search__submit" aria-label="<?php esc_attr_e('Search', 'mp-academy'); ?>">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false" class="mpa-home-hero-search__icon">
                  <circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"></circle>
                  <path d="M20 20L16.65 16.65" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
