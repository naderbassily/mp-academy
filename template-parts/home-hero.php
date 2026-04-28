<figure class="mp c-hero c-hero--homepage">
  <div class="u-wrap">
    <div class="c-hero__content">
      <header class="u-flow--m">
        <h1 class="c-h c-h--page-title"><?php the_field('hero_title'); ?></h1>
        <p><?php the_field('hero_content'); ?></p>
      </header>
      <div class="c-hero__button-wrap">
        <a href="/about" class="mp c-button c-button--inline">About Malvern Panalytical</a>
      </div>
    </div>
  </div>

  <?php if ($bg1 = get_field('hero_image')) : ?>
    <div class="c-hero__image-bg" style="background-image:url('<?php echo esc_url($bg1); ?>');"></div>
  <?php endif; ?>
  <?php if ($bg2 = get_field('hero_bg_2')) : ?>
    <div class="c-hero__image-bg" style="background-image:url('<?php echo esc_url($bg2); ?>');"></div>
  <?php endif; ?>

  <canvas class="c-hero__canvas"></canvas>

  <?php if ($fg1 = get_field('hero_fg_1')) : ?>
    <div class="c-hero__image-fg" style="background-image:url('<?php echo esc_url($fg1); ?>');"></div>
  <?php endif; ?>
  <?php if ($fg2 = get_field('hero_fg_2')) : ?>
    <div class="c-hero__image-fg" style="background-image:url('<?php echo esc_url($fg2); ?>');"></div>
  <?php endif; ?>
</figure>
