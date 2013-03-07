<?php

class Horizontal_Sidebar_Masonry {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'wp_inline_scripts' ) );
	}

	function wp_enqueue_scripts() {
		global $horizontal_sidebar;

		$sidebars = $horizontal_sidebar->registered_sidebars();

		foreach( $sidebars as $sidebar_id ) {
			if ( is_active_sidebar( $sidebar_id ) ) {
				wp_enqueue_script( 'jquery-masonry' );
				break;
			}
		}
	}

	function wp_inline_scripts() {
		global $horizontal_sidebar;

		if( wp_script_is( 'jquery_masonry', 'done' ) ) {
			$sidebars = $horizontal_sidebar->registered_sidebars();
			?>

			<script type="text/javascript">
				( function( $ ) {
					if ( $.isFunction( $.fn.masonry ) ) {
						<?php
						foreach( $sidebars as $sidebar_id ) {
							$amount_columns = $horizontal_sidebar->get_columns_for_sidebar( $sidebar_id );
							$selector       = apply_filters( 'horizontal_sidebar_masonry_selector', false, $sidebar_id );
							$item_selector  = apply_filters( 'horizontal_sidebar_masonry_item_selector', false, $sidebar_id );
							$width          = apply_filters( 'horizontal_sidebar_masonry_width', 0, $amount_columns, $sidebar_id );
							$gutter_width   = apply_filters( 'horizontal_sidebar_masonry_gutter_width', 20, $amount_columns, $sidebar_id );

							if( ! $width )
								$width = 'function( containerWidth ) { return containerWidth / ' . $amount_columns . '; }';

							if( ! $item_selector || ! $item_selector )
								continue;
						?>

						var columnWidth = <?php echo $width; ?>;

						$( '<?php echo $selector; ?>' ).masonry( {
							itemSelector: '<?php echo $item_selector; ?>',
							columnWidth:  columnWidth,
							gutterWidth:  <?php echo $gutter_width; ?>
						} );

						<?php } ?>
					}
				} )( jQuery );
			</script>

			<?php

		}
	}
}