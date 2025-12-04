<?php
/**
 * Template part: MP Academy – Homepage Hero (title + subtitle only)
 * Reuses existing .mp .c-hero structure so no new CSS is required.
 */
if (!defined('ABSPATH')) exit;

// ACF fields with safe fallbacks
$hero_title    = get_field('hero_title_v2') ?: 'MP Academy';
$hero_subtitle = get_field('hero_subtitle_v2') ?: 'Your home for free training courses and how-to videos';
?>

<figure class="mp c-hero">
  <div class="u-wrap">
    <div class="c-hero__content">
      <header class="u-flow--m">
        <h1 class="c-h c-h--page-title"><?php echo esc_html($hero_title); ?></h1>
        <p><?php echo esc_html($hero_subtitle); ?></p>
      </header>
    </div>
  </div>
</figure>
