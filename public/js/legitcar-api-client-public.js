var legitCar = (function ($) {
	'use strict';

	var verification = function () {
			$("#legitcar-verification-form").on("submit", function (e) {
				e.preventDefault();
				var form = $(this);
				var vin = form.find("#legitcar-verification-vin").val();
				var btn = form.find("#legitcar-verification-submit");
				var text = btn.val();
				btn.val("Please wait...");
				//make ajax request
				$.ajax({
					url: legitcar_data.ajax_url,
					type: 'post',
					data: {
						action: legitcar_data.verification_url,
						vin: vin
					},
					success: function (data, textStatus) {
						window.location.href = form.attr("data-url");
					},
					fail: function (xhr, textStatus, error) {
						btn.val(text);
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
	};

})(jQuery);

jQuery(legitCar.ready);