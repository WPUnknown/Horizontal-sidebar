jQuery(document).ready(function($) {
	$(document).on("change", ".sidebar-columns", function(evt){
		evt.preventDefault();

		$(this).closest('div.widgets-holder-wrap').find('.spinner').css('display', 'inline-block');

		var data = {
			action: 'horizontal_sidebar_columns',
			action_nonce: $('#_wpnonce_widgets').val(),

			sidebar: $(this).closest('.widgets-sortables').attr('id'),
			amount_columns: $( 'option:selected', this).val()
		};

		jQuery.post(ajaxurl, data, function(response) {
			$('.spinner').css('display', 'none');
		});
	});
});