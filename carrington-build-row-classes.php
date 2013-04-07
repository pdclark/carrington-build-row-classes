<?php

/*
Plugin Name: Carrington Build: Row Classes
Description: Attach module classes starting with <code>row-</code> to be on the row containing that module.
Version: 1.0
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
*/

/**
 * Copyright (c) 2013 Brainstorm Media. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

add_action( 'init', create_function( '', 'new Storm_Build_Row_Classes();' ) );

class Storm_Build_Row_Classes {

	var $class_prefix = 'row-';

	var $module_classes = array();

	public function __construct() {
		add_filter( 'cfct-generated-row-classes', array( $this, 'row_classes' ), 10, 3 );
		add_filter( 'cfct-build-module-class', array( $this, 'module_class' ), 90, 2 );
	}

	public function module_class( $classes, $data ) {
		$this->module_classes[ $data['block_id'] ][] = $classes;

		$classes = explode( ' ', $classes );

		// Remove classes starting with "row-"
		foreach ( $classes as $key => $class ) {
			if ( $this->class_prefix == substr( $class, 0, strlen( $this->class_prefix ) ) ) {
				unset( $classes[$key] );
			}
		}

		$classes = implode( ' ', $classes );

		return $classes;
	}

	function row_classes( $classes, $module_types, $row ) {
		$row_classes = $this->get_row_classes( $module_types );

		$classes = array_merge( $classes, $row_classes );

		return $classes;
	}

	public function get_row_classes( $module_types ) {
		$row_classes = array();
		$blocks = array_intersect_key( $this->module_classes, $module_types );

		foreach ( $blocks as $block_id => $block ) {
			foreach ( $block as $classes ) {

				// Extract module classes that start with "row-"
				$classes = explode( ' ', $classes );
				
				foreach ( $classes as $class ) {
					if ( $this->class_prefix == substr( $class, 0, strlen( $this->class_prefix ) ) ) {
						$row_classes[] = $class;
					}
				}
			}
		}

		return $row_classes;
	}
}