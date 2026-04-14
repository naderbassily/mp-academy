<?php
/**
 * Template part: MP Academy – Homepage Signposts
 */
if (!defined('ABSPATH')) exit;

$items = [
  [
    'label' => 'Theory 101',
    'href'  => null,
    'slug'  => 'theory-101',
    'icon'  => trailingslashit(get_template_directory_uri()) . 'assets/images/bulb-icon.svg',
  ],
  [
    'label' => 'Instrument courses',
    'href'  => null,
    'slug'  => 'instrument-videos',
    'icon'  => trailingslashit(get_template_directory_uri()) . 'assets/images/instrument-icon.svg',
  ],
  [
    'label' => 'Video Library',
    'href'  => home_url('/videos-library/'),
    'slug'  => '',
    'icon'  => trailingslashit(get_template_directory_uri()) . 'assets/images/recorded-webinars-icon.svg',
  ],
  [
    'label' => 'Upcoming webinars',
    'href'  => 'https://www.malvernpanalytical.com/en/learn/events-and-training',
    'slug'  => '',
    'icon'  => trailingslashit(get_template_directory_uri()) . 'assets/images/training-icon.svg',
  ],
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
    <div class="o-grid o-grid--of-four o-grid--of-four-early">
      <?php foreach ($items as $i):
        $href = !empty($i['href']) ? $i['href'] : mpacad_term_link($i['slug'], $tax_candidates);
      ?>
        <article class="mp c-signpost c-signpost--bordered mp-home-cats__signpost">
          <a
            class="c-signpost__link"
            href="<?php echo esc_url($href); ?>"
            <?php echo str_starts_with($href, 'http') && strpos($href, home_url()) !== 0 ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
          >
            <figure class="mp-home-cats__image">
              <img src="<?php echo esc_url($i['icon']); ?>" alt="" loading="lazy" />
            </figure>
            <div class="c-signpost__content u-flow--2xs">
              <h3 class="c-h c-h--step--1"><?php echo esc_html($i['label']); ?></h3>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
