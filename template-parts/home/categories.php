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
    'icon'  => trailingslashit(get_template_directory_uri()) . 'assets/images/training-icon.svg',
  ],
  [
    'label' => 'Upcoming webinars',
    'href'  => 'https://www.malvernpanalytical.com/en/learn/events-and-training?size=n_10_n&filters%5B0%5D%5Bfield%5D=startdate&filters%5B0%5D%5Bvalues%5D%5B0%5D%5Bfrom%5D=2026-03-16T00:00:00.000Z&filters%5B0%5D%5Btype%5D=all&filters%5B1%5D%5Bfield%5D=eventtype_bizaboo&filters%5B1%5D%5Bvalues%5D%5B0%5D=Webinar&filters%5B1%5D%5Bvalues%5D%5B1%5D=Launch%20Event&filters%5B1%5D%5Btype%5D=any&sort-field=startdate&sort-direction=asc',
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
