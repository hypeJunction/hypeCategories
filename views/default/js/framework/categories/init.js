define(function(require) {

	var $ = require('jquery');

	if ($('.categories-manage').length) {
		require(['framework/categories/manage'], function(categories) {
			categories.init();
		});
	}

	var selectors = '.categories-tree,.categories-input';
	if ($(selectors).length) {
		require('framework/categories/tree');
	}

	$(document).ajaxSuccess(function(data) {
		if ($(data).has(selectors)) {
			require('framework/categories/tree');
		}
	});
	
});