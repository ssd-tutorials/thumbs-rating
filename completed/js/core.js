var rateObject = {
	urlRate : '/mod/rate.php',
	urlReset : '/mod/reset.php',
	rate : function(obj) {
		obj.live('click', function(e) {
			var thisObj = $(this);
			var thisType = thisObj.hasClass('rateUp') ? 'up' : 'down';
			var thisItem = thisObj.attr('data-item');
			var thisValue = thisObj.children('span').text();
			jQuery.getJSON(rateObject.urlRate, { type : thisType, item : thisItem }, function(data) {
				if (!data.error) {
					thisObj.children('span').html(parseInt(thisValue, 10) + 1);
					thisObj.parent('.rateWrapper').find('.rate').addClass('rateDone').removeClass('rate');
					thisObj.addClass('active');
				}
			});
			e.preventDefault();
		});
	},
	reset : function(obj) {
		obj.live('click', function(e) {
			jQuery.getJSON(rateObject.urlReset, function(data) {
				if (!data.error) {
					location.reload();
				}
			});
			e.preventDefault();
		});
	}
};
$(function() {
	jQuery.ajaxSetup({ cache:false });
	rateObject.rate($('.rate'));
	rateObject.reset($('.reset'));
});





