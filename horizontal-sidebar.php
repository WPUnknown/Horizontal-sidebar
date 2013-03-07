<?php
/*
Plugin Name: Horizontal sidebar
Plugin URI: http://github.com/wpunknown/horizontal-sidebar
Description: Gives you the possibility to have a horizontal sidebar
Author: WP Unknown
Author URI: http://wpunknown.com
Version: 1.0

Horizontal sidebar is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Horizontal sidebar is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Horizontal sidebar. If not, see <http://www.gnu.org/licenses/>.
*/

/*
Example code

add_action( 'widgets_init', 'horizontal_sidebar_example', 100 );

function horizontal_sidebar_example() {
	horizontal_sidebar_register( 'sidebar-1', array( 4, 2 ) );
}
*/

class Horizontal_Sidebar {
	public static $end_row = '<div class="clearfix"></div>';
	private $sidebars = array();

	public function register( $sidebar_id, $columns ) {
		global $wp_registered_sidebars, $_wp_sidebars_widgets;

		if( ! is_array( $columns ) )
			$columns = array( $columns );

		if( ! empty( $wp_registered_sidebars[ $sidebar_id ] ) ) {
			$columns = array_filter( array_map( "absint", $columns ) );
			$this->sidebars[ $sidebar_id ] = $columns;

			return true;
		}

		return false;
	}
}

$GLOBALS['horizontal_sidebar'] = new Horizontal_Sidebar();

function horizontal_sidebar_register( $sidebar_id, $columns ) {
	global $horizontal_sidebar;

	return $horizontal_sidebar->register( $sidebar_id, $columns );
}

