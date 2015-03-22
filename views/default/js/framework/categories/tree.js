define(['jquery'], function($) {

	$(document).die('click.categories', '.elgg-child-menu-toggle')
			.on('click.categories', '.elgg-child-menu-toggle', function(e) {
				e.preventDefault();
				$(this).closest('li').toggleClass('elgg-menu-open elgg-menu-closed');
			});

	$(document).die('click.categories', '.elgg-child-menu-toggle + label')
			.on('click.categories', '.elgg-child-menu-toggle + label', function(e) {
				$(this).siblings('.elgg-child-menu-toggle').trigger('click');
			});
});



