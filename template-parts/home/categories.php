<?php
/**
 * Template part: MP Academy – Homepage Categories (icons with separators)
 */
if (!defined('ABSPATH')) exit;

// Upload your SVGs here: /assets/icons/theory.svg, /assets/icons/instrument.svg, /assets/icons/user-training.svg
$items = [
  ['label' => 'Theory 101',        'slug' => 'theory-101',        'icon' => 'bulb-icon.svg'],
  ['label' => 'Instrument videos', 'slug' => 'instrument-videos', 'icon' => 'instrument-icon.svg'],
  ['label' => 'User training',     'slug' => 'user-training',     'icon' => 'training-icon.svg'],
];

$tax_candidates = ['ld_course_category', 'course_category', 'category'];

function mpacad_term_link($slug, $cands) {
  foreach ($cands as $tx) {
    if (!taxonomy_exists($tx)) continue;
    $t = get_term_by('slug', $slug, $tx);
    if ($t && !is_wp_error($t)) {
      $url = get_term_link($t);
      if (!is_wp_error($url)) return $url;
    }
  }
  return '#';
}
?>
<section class="mp mp-home-cats" aria-label="<?php esc_attr_e('Course categories', 'mp-academy'); ?>">
  <div class="u-wrap">
    <ul class="mp-home-cats__list">
      <?php foreach ($items as $i): 
        $href = mpacad_term_link($i['slug'], $tax_candidates);
        $src  = trailingslashit(get_template_directory_uri()) . 'assets/images/' . $i['icon'];
      ?>
        <li class="mp-home-cats__item">
          <a class="mp-home-cats__link" href="<?php echo esc_url($href); ?>">
            <img class="mp-home-cats__img" src="<?php echo esc_url($src); ?>" alt="" width="96" height="96" loading="lazy" />
            <span class="mp-home-cats__label"><?php echo esc_html($i['label']); ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
