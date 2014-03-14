//<script>

	elgg.provide('framework.categories');

	framework.categories.input = function() {
		$('.elgg-child-menu-toggle').live('click', function(e) {
			e.preventDefault();
			$(this).closest('li').toggleClass('elgg-menu-open elgg-menu-closed');
		});
	};

	elgg.register_hook_handler('init', 'system', framework.categories.input);
