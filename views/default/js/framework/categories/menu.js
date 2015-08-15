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

	$(document).on('change', '.categories-tree-node input[type="checkbox"]', function (e) {
		var $elem = $(this);
		var name = $(this).attr('name');
		var $parent = $elem.closest('.elgg-menu-parent,.elgg-menu-nochildren');
		var $parents = $elem.parentsUntil('.elgg-menu-categories');
		if ($elem.prop('checked') === true) {
			$parents
					.children('.categories-tree-node')
					.children('[name="' + name + '"]')
					.prop('checked', true);
		} else {
			$parent.find('[name="' + name + '"]').prop('checked', false);
		}
	});
});



