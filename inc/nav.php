<?php
/**
 * Navigation — menu registration, WCAG submenu walker.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom nav walker with WCAG-compliant submenu keyboard support.
 */
class HP_Nav_Walker extends Walker_Nav_Menu {

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? [] : (array) $item->classes;
		$has_child = in_array( 'menu-item-has-children', $classes, true );

		$output .= '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		$attrs  = ' href="' . esc_url( $item->url ) . '"';
		$attrs .= ' class="hp-nav-link"';
		if ( $has_child ) {
			$attrs .= ' aria-haspopup="true" aria-expanded="false"';
		}

		$output .= '<a' . $attrs . '>' . esc_html( $item->title ) . '</a>';

		if ( $has_child ) {
			$output .= '<button type="button" class="hp-nav-toggle" aria-label="' . esc_attr__( 'Toggle submenu', 'herbalpearls' ) . '">';
			$output .= '<svg width="12" height="8" viewBox="0 0 12 8" aria-hidden="true"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none"/></svg>';
			$output .= '</button>';
		}
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="hp-sub-menu" role="menu">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}
