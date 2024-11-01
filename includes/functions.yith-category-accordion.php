<?php
/**
 * This file contain all plugin functions.
 *
 * @package YITH WooCommerce Category Accordion\Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_wc_categories' ) ) {
	/**
	 * Return the product $categories
	 *
	 * @param bool $hide_empty Show or not empty categories.
	 * @author YITH
	 * @since 1.0.0
	 * @return array
	 */
	function get_wc_categories( $hide_empty = false ) {

		$args = array( 'hide_empty' => $hide_empty );

		$categories_term = get_terms( 'product_cat', $args );

		$categories = array();

		foreach ( $categories_term as $category ) {

			$categories[ $category->slug ] = '#' . $category->term_id . '-' . $category->name;
		}

		return $categories;

	}
}
