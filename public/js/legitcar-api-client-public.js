var legitCar = (function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	var verification = function () {
			$("#legitcar-verification-form").on("submit", function (e) {
				e.preventDefault();
				var form = this;
				var vin = $(form).find("#legitcar-verification-vin").val();
				console.log(vin);
				//make ajax request
				$.ajax({
					url: legitcar_data.ajax_url,
					type: 'post',
					data: {
						action: legitcar_data.verification_url,
						vin: vin
					},
					success: function (response) {
						alert(JSON.parse(response));
					}
				});
				return false;
			});
		},

		ready = function () {
			verification();
		};

	//expose our ready function
	return {
		ready: ready
	}

})(jQuery);

jQuery(legitCar.ready);