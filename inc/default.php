<?php

class Horizontal_Sidebar_Default {
	public static $end_row = '<div class="clearfix"></div>';
	private $counters = array();

	function __construct() {
		if( ! is_admin() )
			add_filter( 'dynamic_sidebar_params', array( $this, '_sidebar_params' ), 10000 );
	}

	function _sidebar_params( $params ) {
		global $horizontal_sidebar;

		$sidebar_id = $params[0]['id'];
		$sidebars   = $horizontal_sidebar->registered_sidebars();

		if( ! isset( $sidebars[ $sidebar_id ] ) )
			return $params;

		if( ! isset( $this->counters[ $sidebar_id ] ) )
			$this->counters[ $sidebar_id ] = 1;

		$amount_columns = $horizontal_sidebar->get_columns_for_sidebar( $sidebar_id );

		$additional_classes = trim( apply_filters( 'horizontal_sidebar_classes', '', $amount_columns, $sidebar_id ) );

		if( $this->counters[ $sidebar_id ] % $amount_columns == 0 ) {
			$additional_classes .= ' last';
			$params[0]['after_widget']  = $params[0]['after_widget'] . self::$end_row;
		}

		if( $additional_classes )
			$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"" . $additional_classes . " ", $params[0]['before_widget'], 1 );

		$this->counters[ $sidebar_id ]++;

		return $params;
	}

}