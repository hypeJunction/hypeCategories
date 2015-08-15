<?php

return array(
	// All blogs pages
	'blogs' => array(
		'regex' => 'blog\/.*',
		'sidebar' => elgg_echo('categories:sidebar:blogs'),
		//'category_subtypes' => array(\hypeJunction\Categories\Category::SUBTYPE), // you can specify custom category subtypes for each context
		'type_subtype_pairs' => array(
			'object' => array(
				'blog',
			),
		),
		// supported replacements
		// {page_owner_guid} => page_owner_guid
		// {category_guid} => category guid
		// {category_name} => category name
		'category_url' => 'blog/categories/{category_guid}/{category_name}',
		// Prepend breadcrumbs stack with these breadcrumbs
		'breadcrumbs' => array(
			array('text' => elgg_echo('blog:blogs'), 'href' => 'blog/all'),
		),
	),
	// All bookmarks pages
	'bookmarks' => array(
		'regex' => 'bookmarks\/.*',
		'sidebar' => elgg_echo('categories:sidebar:bookmarks'),
		'type_subtype_pairs' => array(
			'object' => array(
				'bookmarks',
			),
		),
		'category_url' => 'bookmarks/categories/{category_guid}/{category_name}',
		'breadcrumbs' => array(
			array('text' => elgg_echo('bookmarks'), 'href' => 'bookmarks/all'),
		),
	),
	// All files pages
	'files' => array(
		'regex' => 'file\/.*',
		'sidebar' => elgg_echo('categories:sidebar:files'),
		'type_subtype_pairs' => array(
			'object' => array(
				'file',
			),
		),
		'category_url' => 'file/categories/{category_guid}/{category_name}',
		'breadcrumbs' => array(
			array('text' => elgg_echo('file'), 'href' => 'file/all'),
		),
	),
	'pages' => array(
		'regex' => 'pages\/.*',
		'sidebar' => elgg_echo('categories:sidebar:pages'),
		'type_subtype_pairs' => array(
			'object' => array(
				'page', 'page_top',
			),
		),
		'category_url' => 'pages/categories/{category_guid}/{category_name}',
		'breadcrumbs' => array(
			array('text' => elgg_echo('pages'), 'href' => 'pages/all'),
		),
	),
	// Group lists
	'groups' => array(
		'regex' => 'groups\/all($|\/.*|\?.*)',
		'sidebar' => elgg_echo('categories:sidebar:groups'),
		'type_subtype_pairs' => array(
			'group' => ELGG_ENTITIES_NO_VALUE,
		),
		'category_url' => 'groups/categories/{category_guid}/{category_name}',
		'breadcrumbs' => array(
			array('text' => elgg_echo('groups'), 'href' => 'groups/all'),
		),
	),
	'groups_edit' => array(
		'regex' => 'groups\/(edit|add)($|\/.*|\?.*)',
		'sidebar' => false,
		'type_subtype_pairs' => array(
			'group' => ELGG_ENTITIES_NO_VALUE,
		),
		'forms' => array(
			array(
				'page_url' => 'groups/add/{container_guid}',
				'selector' => '.elgg-form-groups-edit > fieldset > div:not(.elgg-foot):last',
			),
			array(
				'page_url' => 'groups/edit/{guid}',
				'selector' => '.elgg-form-groups-edit > fieldset > div:not(.elgg-foot):last',
			),
		),
	),
	// Group profiles
	'group_profile' => array(
		'regex' => 'groups\/profile\/.*',
		'sidebar' => elgg_echo('categories:sidebar:group'),
		'category_url' => 'groups/profile/{page_owner_guid}/categories/{category_guid}/{category_name}',
	),
	// Categories
	'categories' => array(
		'regex' => 'categories\/.*',
	),
	//All other pages except /profile and /settings and /activity handlers
	//	'all' => array(
	//		'regex' => '((?!profile|settings|activity).{1,})($|\/)(.*)',
	//		'sidebar' => elgg_echo('categories:sidebar'),
	//		'category_url' => '/categories/view/{category_guid}/{category_name}',
	//	)
);
