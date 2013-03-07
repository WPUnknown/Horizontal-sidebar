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
	private $dropdown_showed = array();

	function __construct() {
		if( is_admin() )
			add_filter( 'dynamic_sidebar_params', array( $this, 'add_selectbox' ), 10000 );
	}

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


	public function add_selectbox( $params ) {
		global $wp_registered_sidebars;

		$screen = get_current_screen();

		if( 'widgets' == $screen->base ) {
			$sidebar_id = $params[0]['id'];

			if( is_admin() && ! isset( $this->dropdown_showed[ $sidebar_id ] ) && count( $this->sidebars[ $sidebar_id ] ) > 1 ) {
				echo $this->show_selectbox( $sidebar_id, $this->sidebars[ $sidebar_id ] );

				$this->dropdown_showed[ $sidebar_id ] = true;
			}
		}

		return $params;
	}

	private function show_selectbox( $sidebar_id, $columns ) {
		$return  = '<div class="sidebar-description" style="margin-top:-10px;">' . __( 'Amount of columns', 'horizontal_sidebar' ) . ' &nbsp; <select class="sidebar-columns">';

		$default_amount = $columns[0];
		$current_amount = $this->get_columns_for_sidebar( $sidebar_id, $default_amount );
		sort( $columns );

		foreach( $columns as $column ) {
			if( $column == $default_amount )
				$column_name = $column . ' (' . __( 'default', 'horizontal_sidebar' ) . ')';
			else
				$column_name = $column;

			if( $column == $current_amount )
				$return .= '<option value="' . $column . '" selected="selected">' . $column_name . '</option>';
			else
				$return .= '<option value="' . $column . '">' . $column_name . '</option>';
		}

		$return .= '</select></div>';

		return $return;
	}

	private function get_columns_for_sidebar( $sidebar_id, $default ) {
		$options = get_option( 'horizontal_sidebar_columns', array() );

		if( isset( $options[ $sidebar_id ] ) )
			return absint( $options[ $sidebar_id ] );

		return $default;
	}
}

$GLOBALS['horizontal_sidebar'] = new Horizontal_Sidebar();

function horizontal_sidebar_register( $sidebar_id, $columns ) {
	global $horizontal_sidebar;

	return $horizontal_sidebar->register( $sidebar_id, $columns );
}

