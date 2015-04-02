define(['jquery', 'elgg'], function ($, elgg) {

	var CategoriesTree = function ($container) {
		this.$container = $container;
	};

	CategoriesTree.prototype = {
		constructor: CategoriesTree,
		initStatic: function () {
			var self = this;
			require(['jquery.jstree'], function () {
				self.$container
						.on('select_node.jstree', self.onNodeSelect.bind(self))
						.jstree();
			}.bind(self));

		},
		initDynamic: function () {
			var self = this;
			require(['jquery.jstree'], function () {
				var self = this; // $container object
				var opts = {
					'core': {
						'data': {
							'url': self.$container.data('url'),
							'dataType' : 'json',
							'data': function (node) {
								var requestData = {};
								if (node.id === '#') {
									requestData.container_guid = self.$container.data('containerGuid')
								} else {
									requestData.container_guid = node.id;
								}
								return requestData;
							}
						}
					}
				};
				self.$container
						.on('select_node.jstree', self.onNodeSelect.bind(self))
						.jstree(opts);
			}.bind(self));

		},
		onNodeSelect: function (e, data) {
			e.preventDefault();
			var $node = this.$container.jstree(true).get_node(data.node, true);
			var href = $node.find('a').attr('href');
			if (href) {
				window.location.href = href;
			}
		}
	};

	$('.js-categories-static-tree').each(function () {
		if (!$(this).data('CategoriesTree')) {
			var tree = new CategoriesTree($(this));
			tree.initStatic();
			$(this).data('CategoriesTree', tree);
		}
	});

	$('.js-categories-dynamic-tree').each(function () {
		if (!$(this).data('CategoriesTree')) {
			var tree = new CategoriesTree($(this));
			tree.initDynamic();
			$(this).data('CategoriesTree', tree);
		}
	});

});



