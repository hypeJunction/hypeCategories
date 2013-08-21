<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>
	elgg.provide('framework.categories');

	framework.categories.manage = function() {

		$('.categories-icon-upload')
		.live('click.notfile', function(e) {
			e.preventDefault();
			$(this).find('input[type="file"]').change($(this).addClass('categories-icon-checked'));
			$(this).find('input[type="file"]').trigger('click.file');
		})

		$('.categories-icon-plus')
		.live('click', function(e) {
			e.preventDefault();
			var $clone = $(this).closest('li').clone().hide();
			$clone.find('input').val('');
			$clone.find('img').remove();
			$(this).closest('li').after($clone.fadeIn());
		})


		$('.categories-icon-minus')
		.live('click', function(e) {
			e.preventDefault();
			if (!confirm(elgg.echo('hj:categories:remove:confirm'))) {
				return false;
			}
			$(this).closest('li').fadeOut().appendTo($(this).closest('form')).find('[name="categories[title][]"]').val('');
		})

		$('.categories-icon-info')
		.live('click', function(e) {
			$(this).siblings('.categories-category-meta').toggleClass('hidden');
		})
		
		 $('.categories-manage .elgg-menu-categories').nestedSortable({
            handle: 'div .categories-icon-move',
            items: 'li',
            toleranceElement: '> div',
			listType: 'ul',
			placeholder: 'categories-draggable-placeholder',
			stop: framework.categories.updateHierarchy,
			rootID: 1,
			protectRoot: true
        });

		$('form.elgg-form-categories-manage')
		.submit(function(e) {

			framework.categories.updateHierarchy();
			return true;

		})

	};
	
	framework.categories.updateHierarchy = function(event, ui) {
		$('.elgg-menu-categories li')
		.each(function(key, val) {
			$(this).attr('id', 'category-node-' + key);
			$(this).find('[name="categories[hierarchy][]"]').val(key);
		})
		$('#category-hierarchy').val(JSON.stringify($('.elgg-menu-categories').nestedSortable('toHierarchy')));
	}

	elgg.register_hook_handler('init', 'system', framework.categories.manage);

<?php if (FALSE) : ?>
	</script>
<?php endif; ?>