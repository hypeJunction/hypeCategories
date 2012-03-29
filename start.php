<?php

/* hypeCategories
 *
 * @package hypeJunction
 * @subpackage hypeCategories
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyrigh (c) 2011, Ismayil Khayredinov
 */
?>
<?php

// Initialize hypeFramework
elgg_register_event_handler('init', 'system', 'hj_categories_init');

function hj_categories_init() {

	$plugin = 'hypeCategories';

	if (!elgg_is_active_plugin('hypeFramework')) {
		register_error(elgg_echo('hj:framework:disabled', array($plugin, $plugin)));
		disable_plugin($plugin);
	}

// Add admin menu item
	elgg_register_admin_menu_item('administer', 'categories', 'hj', 200);


	$shortcuts = hj_framework_path_shortcuts($plugin);

	/**
	 * LIBRARIES
	 */
	elgg_register_library('hj:categories:base', $shortcuts['lib'] . 'categories/base.php');
	elgg_load_library('hj:categories:base');

	elgg_register_library('hj:categories:setup', $shortcuts['lib'] . 'categories/setup.php');

	elgg_register_classes($shortcuts['classes']);

	$hj_js = elgg_get_simplecache_url('js', 'hj/categories/base');
	elgg_register_js('hj.categories.base', $hj_js);

	$hj_css = elgg_get_simplecache_url('css', 'hj/categories/base');
	elgg_register_css('hj.categories.base', $hj_css);
	/**
	 * INITIAL SETUP
	 */
	if (!elgg_get_plugin_setting('hj:categories:setup')) {
		elgg_load_library('hj:categories:setup');
		if (hj_categories_setup())
			system_message('hypeCategories were successfully configured');
	}

// hjCategories Icons
	elgg_register_page_handler('category', 'hj_categories_page_handler');

	/**
	 * HOOKS
	 */
// Add new category field type to hypeFormbuilder and hypeFramework
	elgg_register_plugin_hook_handler('hj:formbuilder:fieldtypes', 'all', 'hj_categories_category_input');
	elgg_register_plugin_hook_handler('hj:framework:field:process', 'all', 'hj_categories_category_input_process');

	if (elgg_is_active_plugin('profile_manager')) {
		$profile_options = array(
			"show_on_register" => true,
			"mandatory" => true,
			"user_editable" => true,
			"admin_only" => true,
			"blank_available" => true,
			"count_for_completeness" => true
		);
		add_custom_field_type("custom_profile_field_types", 'category', elgg_echo('profile_manager:admin:options:category'), $profile_options);
	}
// Add menu items
	elgg_register_plugin_hook_handler('register', 'menu:hjcategories', 'hj_categories_menu');
	elgg_register_plugin_hook_handler('register', 'menu:hjentityhead', 'hj_categories_entity_head_menu');

	elgg_extend_view('page/elements/sidebar', 'hj/categories/tree/list');

	elgg_register_event_handler('create', 'all', 'hj_categories_input_process');
	elgg_register_event_handler('update', 'all', 'hj_categories_input_process');

	elgg_register_entity_type('object', 'hjcategory');
	elgg_register_entity_url_handler('object', 'hjcategory', 'hj_categories_url_handler');
}

function hj_categories_url_handler($entity) {
	return "category/view/$entity->guid";
}

function hj_categories_page_handler($page) {

	$plugin = 'hypeCategories';
	$shortcuts = hj_framework_path_shortcuts($plugin);
	$pages = $shortcuts['pages'] . 'category/';

	switch ($page[0]) {
		default :
			return false;
			break;

		case 'edit' :
			if (isset($page[1])) {
				$owner = get_entity($page[1]);
				if (elgg_instanceof($owner, 'site') || elgg_instanceof($owner, 'group')) {
					elgg_set_page_owner_guid($owner->guid);
					set_input('parent', $owner->guid);
				} else {
					forward();
				}
			}
			include "{$pages}edit.php";
			return true;
			break;

		case 'view' :
			if (isset($page[1])) {
				set_input('e', $page[1]);
			} else {
				forward();
			}
			include "{$pages}view.php";
			return true;
			break;
	}
}

function hj_categories_category_input($hook, $type, $return, $params) {
	$return[] = 'category';
	return $return;
}

function hj_categories_category_input_process($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params, false);
	$field = elgg_extract('field', $params, false);
	if (!$entity || !$field) {
		return true;
	}

	switch ($field->input_type) {
		case 'category' :
			$field_name = $field->name;
			$category_guids = get_input('category_guids');

			if ($category_guids && !is_array($category_guids)) {
				$category_guids = array($category_guids);
			}

			$current_categories = elgg_get_entities_from_relationship(array(
				'relationship' => 'filed_in',
				'relationship_guid' => $entity->guid,
					//'inverse_relationship' => true
					));

			if (is_array($current_categories)) {
				foreach ($current_categories as $current_category) {
					if (!in_array($current_category->guid, $category_guids)) {
						remove_entity_relationship($entity->guid, 'filed_in', $current_category->guid);
					}
				}
			}

			if ($category_guids) {
				foreach ($category_guids as $category_guid) {
					$category = get_entity($category_guid);
					while (elgg_instanceof($category)) {
						add_entity_relationship($entity->guid, 'filed_in', $category->guid);
						$category = $category->getContainerEntity();
					}
				}
			}

			$entity->$field_name = implode(',', $category_guids);
			break;
	}
	return true;
}

function hj_categories_input_process($action, $type, $entity) {

	$category_guids = get_input('category_guids', false);

	if (!$category_guids) {
		return true;
	}

	if (!$entity || !elgg_instanceof($entity)) {
		return true;
	}

	$field_name = 'categories';
	$category_guids = get_input('category_guids');

	$current_categories = elgg_get_entities_from_relationship(array(
		'relationship' => 'filed_in',
		'relationship_guid' => $entity->guid,
			//'inverse_relationship' => true
			));

	if (is_array($current_categories)) {
		foreach ($current_categories as $current_category) {
			if (!in_array($current_category->guid, $categories)) {
				remove_entity_relationship($entity->guid, 'filed_in', $current_category->guid);
			}
		}
	}
	if ($category_guids) {
		if (!is_array($category_guids)) {
			$category_guids = array($category_guids);
		}
		foreach ($category_guids as $category_guid) {
			$category = get_entity($category_guid);
			if (elgg_instanceof($category, 'object', 'hjcategory')) {
				add_entity_relationship($entity->guid, 'filed_in', $category->guid);
			}
		}
	}

	$entity->$field_name = implode(',', $category_guids);

	set_input('category_guids', false);
	return true;
}

function hj_categories_menu($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params, elgg_get_site_entity());
	$handler = elgg_extract('handler', $params, null);
	$context = elgg_extract('context', $params, null);

	$obj_params = elgg_extract('params', $params, null);
	$owner = elgg_extract('owner', $obj_params, null);
	$container = elgg_extract('container', $obj_params, $entity);
	$type = elgg_extract('type', $obj_params, null);
	$subtype = elgg_extract('subtype', $obj_params, null);

	if (!$title = $entity->title) {
		$title = $entity->name;
	}

	$root_menu_item = array(
		'name' => "$entity->guid",
		'text' => $title,
		'href' => $entity->getURL(),
		'class' => 'hj-categories-menu-root',
		'priority' => $entity->priority
	);
	$root_menu_item = ElggMenuItem::factory($root_menu_item);
	$return[] = $root_menu_item;
	$categories = hj_categories_get_subcategories($entity->guid);

	if (is_array($categories)) {
		foreach ($categories as $category) {
			$submenu = $category->getMenuItem($root_menu_item);
			foreach ($submenu as $submenu_item) {
				$return[] = $submenu_item;
			}
		}
	}
	if ($entity->canEdit() && (elgg_instanceof($entity, 'site') || elgg_instanceof($entity, 'group')) && elgg_get_context() == 'category') {
		$form = hj_framework_get_data_pattern('object', 'hjcategory');
		$params = array('params' => array(
				'form_guid' => $form->guid,
				'container_guid' => $entity->guid,
				'subject_guid' => null,
				'entity_guid' => null,
				'dom_order' => 'append',
				'full_view' => false,
				'list_type' => 'tree',
				'target' => "hj-category-parent-$entity->guid",
				'fbox_x' => '900'
				));
		$params = hj_framework_json_query($params);
		$add_new = array(
			'name' => "{$root_menu_item->getName()}:addnew",
			'parent_name' => "{$root_menu_item->getName()}",
			'text' => elgg_echo('hj:categories:addnew'),
			'href' => "action/framework/entities/edit",
			'data-options' => htmlentities($params, ENT_QUOTES, 'UTF-8'),
			'is_action' => true,
			'rel' => 'fancybox',
			'class' => 'hj-ajaxed-add',
			'priority' => 1
		);
		$return[] = ElggMenuItem::factory($add_new);
	}

	return $return;
}

function hj_categories_entity_head_menu($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params);
	$handler = elgg_extract('handler', $params);

	if (elgg_instanceof($entity, 'object', 'hjcategory')) {
		if ($entity->canEdit()) {
			$form = hj_framework_get_data_pattern('object', 'hjcategory');
			$params = array('params' => array(
					'form_guid' => $form->guid,
					'container_guid' => $entity->guid,
					'subject_guid' => null,
					'entity_guid' => null,
					'dom_order' => 'append',
					'full_view' => false,
					'list_type' => 'tree',
					'target' => "hj-category-parent-$entity->guid",
					'fbox_x' => '900'
					));
			$params = hj_framework_json_query($params);

			$add_new = array(
				'name' => "{$entity->guid}:addnew",
				'text' => elgg_echo('hj:categories:addsub'),
				'href' => "action/framework/entities/edit",
				'data-options' => htmlentities($params, ENT_QUOTES, 'UTF-8'),
				'is_action' => true,
				'rel' => 'fancybox',
				'class' => 'hj-ajaxed-add',
				'priority' => 100
			);
			$return[] = ElggMenuItem::factory($add_new);
		}
	}
	return $return;
}

