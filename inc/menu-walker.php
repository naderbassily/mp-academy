<?php
if ( ! defined('ABSPATH') ) exit;

/*Franklin menu walker (minimal)*/
class Franklin_Menu_Walker extends Walker_Nav_Menu {

  public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
    $classes = is_array($item->classes) ? $item->classes : [];

    $li_classes = array_filter($classes);
    $li_classes[] = 'c-navigation__item';

    if (in_array('current-menu-item', $classes, true) || in_array('current_page_item', $classes, true)) {
      $li_classes[] = 'is-active';
    }

    $output .= '<li class="' . esc_attr(implode(' ', array_unique($li_classes))) . '">';

    $atts = [];
    $atts['href']   = ! empty($item->url) ? $item->url : '';
    $atts['class']  = 'c-navigation__link';
    $atts['title']  = ! empty($item->attr_title) ? $item->attr_title : '';
    $atts['target'] = ! empty($item->target) ? $item->target : '';
    $atts['rel']    = ! empty($item->xfn) ? $item->xfn : '';

    if ($atts['target'] === '_blank' && empty($atts['rel'])) {
      $atts['rel'] = 'noopener noreferrer';
    }

    $attributes = '';
    foreach ($atts as $attr => $value) {
      if ($value !== '') {
        $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
      }
    }

    $title = apply_filters('the_title', $item->title, $item->ID);

    $output .= '<a' . $attributes . '>' . esc_html($title) . '</a>';
  }

  public function end_el( &$output, $item, $depth = 0, $args = null ) {
    $output .= "</li>\n";
  }
}
