define(['jquery', 'elgg', 'jquery.nestedSortable'], function($, elgg) {

	var categories = {
		init: function() {

			$(document).on('click.notfile', '.categories-icon-upload', function(e) {
				e.preventDefault();
				$(this).find('input[type="file"]').change($(this).addClass('categories-icon-checked'));
				$(this).find('input[type="file"]').trigger('click.file');
			});

			$(document).on('click', '.categories-icon-plus', function(e) {
				e.preventDefault();
				var $clone = $(this).closest('li').clone().hide();
				$clone.find('input').val('');
				$clone.find('img').remove();
				$(this).closest('li').after($clone.fadeIn());
			});

			$(document).on('click', '.categories-icon-minus', function(e) {
				e.preventDefault();
				if (!confirm(elgg.echo('categories:remove:confirm'))) {
					return false;
				}
				var $li = $(this).closest('li').fadeOut();
				$li.closest('form').append($li);
				$li.find('[name="categories[title][]"]').val('').removeAttr('value');
			});

			$(document).on('click', '.categories-icon-info', function(e) {
				$(this).siblings('.categories-category-meta').toggleClass('hidden');
			});

			$('.categories-manage .elgg-menu-categories').nestedSortable({
				handle: 'div .categories-icon-move',
				items: 'li',
				toleranceElement: '> div',
				listType: 'ul',
				placeholder: 'categories-draggable-placeholder',
				create: categories.updateHierarchy,
				stop: categories.updateHierarchy,
				rootID: 1,
				protectRoot: false,
				isTree: true,
			});

			$(document).on('submit', 'form.elgg-form-categories-manage', function(e) {
				categories.updateHierarchy();
				return true;
			});
		},
		updateHierarchy: function(event, ui) {
			$('.elgg-menu-categories li').each(function(key, val) {
				$(this).attr('id', 'category-node-' + key);
				$(this).find('[name="categories[hierarchy][]"]').val(key);
			});
			$('#category-hierarchy').val(JSON.stringify($('.categories-manage .elgg-menu-categories').nestedSortable('toHierarchy')));
		}
	};

	return categories.init();
});