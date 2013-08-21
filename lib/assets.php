<?php

elgg_register_js('jquery.nestedsortable.js', '/mod/hypeCategories/vendors/nestedSortable/jquery.mjs.nestedSortable.js');

elgg_register_simplecache_view('css/framework/categories/base');
elgg_register_css('categories.base.css', elgg_get_simplecache_url('css', 'framework/categories/base'));

elgg_register_simplecache_view('js/framework/categories/manage');
elgg_register_js('categories.manage.js', elgg_get_simplecache_url('js', 'framework/categories/manage'));

elgg_register_simplecache_view('js/framework/categories/tree');
elgg_register_js('categories.tree.js', elgg_get_simplecache_url('js', 'framework/categories/tree'));