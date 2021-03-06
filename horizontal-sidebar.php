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
	private $sidebars = array();
	private $dropdown_showed = array();

	function __construct() {
		if( is_admin() )
			add_filter( 'dynamic_sidebar_params', array( $this, 'add_selectbox' ), 10000 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'wp_ajax_horizontal_sidebar_columns', array( $this, 'ajax_save_columns' ) );

		add_action( 'plugins_loaded', array( $this, 'load_sidebar_type' ) );
	}


	function load_sidebar_type() {
		$type = apply_filters( 'horizontal_sidebar_type', 'default' );

		if( 'masonry' == $type ) {
			include 'inc/masonry.php';
			new Horizontal_Sidebar_Masonry();
		}
		else {
			include 'inc/default.php';
			new Horizontal_Sidebar_Default();
		}
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

	public function registered_sidebars() {
		return array_keys( $this->sidebars );
	}


	public function add_selectbox( $params ) {
		global $wp_registered_sidebars;

		$sidebar_id = $params[0]['id'];

		if( ! isset( $this->sidebars[ $sidebar_id ] ) )
			return $params;

		$screen = get_current_screen();

		if( 'widgets' == $screen->base ) {
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
		$current_amount = $this->get_columns_for_sidebar( $sidebar_id );
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

	public function get_columns_for_sidebar( $sidebar_id ) {
		$options = get_option( 'horizontal_sidebar_columns', array() );

		if( isset( $options[ $sidebar_id ] ) )
			return absint( $options[ $sidebar_id ] );

		return $this->sidebars[ $sidebar_id ][0] ;
	}

	function admin_enqueue() {
		$screen = get_current_screen();

		if ( 'widgets' === $screen->base )
			wp_enqueue_script( 'horizontal-sidebar', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ) );
	}

	function ajax_save_columns() {
		global $wp_registered_sidebars;

		check_ajax_referer( 'save-sidebar-widgets', 'action_nonce' );

		header( "Content-Type: application/json" );

		if( isset( $_POST['sidebar'], $_POST['amount_columns'] ) && ! empty( $wp_registered_sidebars[ $_POST['sidebar'] ] ) ) {
			$options = get_option( 'horizontal_sidebar_columns', array() );
			$options[ $_POST['sidebar'] ] = absint( $_POST['amount_columns'] );
			update_option( 'horizontal_sidebar_columns', $options );

			echo json_encode( array( 'success' => true ) );
		}
		else {
			echo json_encode( array( 'success' => false ) );
		}

		die();
	}

}

$GLOBALS['horizontal_sidebar'] = new Horizontal_Sidebar();

function horizontal_sidebar_register( $sidebar_id, $columns ) {
	global $horizontal_sidebar;

	return $horizontal_sidebar->register( $sidebar_id, $columns );
}

