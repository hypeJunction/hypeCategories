define(['jquery'], function ($) {

	$(document).on('click.categories', '.elgg-child-menu-toggle', function (e) {
		if (!$(e.target).is('input')) {
			e.preventDefault();
			$(this).closest('li').toggleClass('elgg-menu-open elgg-menu-closed');
		}
	});

	$(document).on('click.categories', '.elgg-child-menu-toggle + label', function (e) {
		if (!$(e.target).is('input')) {
			$(this).siblings('.elgg-child-menu-toggle').trigger('click');
		}
	});

	$(document).on('change', '.categories-tree-node input[type="checkbox"]', function(e) {
		$(this).closest('li').find('[name="' + $(this).attr('name') + '"]').prop('checked', $(this).prop('checked'));
	});
});



