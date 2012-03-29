<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

elgg.provide('hj.categories.base');

   
hj.categories.base.init = function() {
	$('.elgg-menu-page .elgg-menu-parent')
	.die('dblclick')
	.live('dblclick', function(event) {
		event.preventDefault();
		window.location = $(this).attr('href');
	});

	$('.hj-category-input-item > a')
	.die('click')
	.live('click', function(event) {
		var id = $(this).parent().attr('id').replace('elgg-object-', '');
		if ($(this).parent().hasClass('elgg-state-selected')) {
			$(this).parent().removeClass('elgg-state-selected');
			if (id.length > 0) {
				$('input[name="category_guids[' + id + ']"][value="' + id + '"]').remove();
			}
		} else {
			$(this).parent().addClass('elgg-state-selected');
			$(this).parents('form').first().append($('<input>').attr('type', 'hidden').attr('name', 'category_guids[' + id + ']').val(id));
		}
	})
}


    
elgg.register_hook_handler('init', 'system', hj.categories.base.init);
elgg.register_hook_handler('success', 'hj:framework:ajax', hj.categories.base.init);
<?php if (FALSE) : ?></script><?php endif; ?>