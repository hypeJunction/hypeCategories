define(['elgg', 'jquery'], function(elgg, $) {

	$('[data-inject][data-selector]').each(function() {
		var selector = $(this).data('selector');
		if (selector && $(selector).length) {
			$(selector).after($(this).show());
		}
	});
	
});


