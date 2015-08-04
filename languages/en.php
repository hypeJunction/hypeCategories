<?php

namespace hypeJunction\Categories;

$general = array(
	'item:object:hjcategory' => 'Categories',
	'categories' => 'Categories',
	'categories:category' => 'Category: %s',
	'categories:subcategories' => 'Subcategories',
	'categories:latest_items' => 'Latest items',
	'categories:manage' => 'Manage categories',
	'categories:site' => 'Site-wide Categories',
	'categories:group' => '%s Categories',
	'categories:group:all' => 'Categories',
	'categories:owner' => '%\'s Categories',
	'categories:select' => 'Select category',
	'categories:select:site' => 'Select category',
	'categories:select:group' => 'Select group category',
	'categories:category:title' => '%s <em>%s</em>',
	'categories:count:filed' => '%s items in this category',
	'categories:add_subcategory' => 'Add subcategory',
	'categories:empty' => 'There are no items in this category',
	'categories:view_all' => 'View all items',
	'profile_manager:admin:options:category' => 'Category',
	'categories:category_filter' => 'Filter by content type',
	'categories:num_display' => 'Categories per page',
	'categories:num_display:help' => 'Number of categories to display per page on categories/all page (set to 0 to show all)',
	'categories:groupoption:enable' => 'Enable group categories',
	'categories:no_results' => 'There are no categories to display',
);

$navigation = array(
	'categories:edit' => 'Toggle edit form',
	'categories:edit:icon' => 'Upload an Icon',
	'categories:edit:title' => 'Category Name',
	'categories:edit:description' => 'Category Description',
	'categories:edit:access_id' => 'Visibility',
	'categories:type_subtype_pairs' => 'Types of content items that categories should be applied to',
	'categories:type_subtype_pairs:help' => 'Selected types will appear in the category page filter.
			This setting will also restrict the scope of to the default event handler, since each action
			can trigger multiple create and update events (creating a blog for example is accompanying with creating notifications, notices etc).
			Please note that selecting or unselecting an item will not automatically include or remove a category input field to/from the form',
);

$forms = array(
	'categories:entity_menu' => 'Display category in entity menu',
	'categories:group_categories' => 'Enable group categories',
	'categories:group_tree_site' => 'Add site categories to the group tree',
	'categories:input_multiple' => 'Unless specified otherwise programically, categories field should accept multiple inputs',
	'categories:display' => 'Display',
	'categories:donotdisplay' => 'Do not display',
	'categories:enable' => 'Enable',
	'categories:disable' => 'Disable',
	'categories:multiple' => 'Multiple',
	'categories:single' => 'Single',
	'categories:remove:confirm' => 'Are you sure you want to remove this category and all subcategories under it?',
);

$actions = array(
	'categories:action:error' => 'An error has occurred and has been logged. Please contact site administrator if the error persists',
	'categories:validation:error' => 'One or more inputs are incomplete or contain wrong data. Please double check the form',
	'categories:permissions:error' => 'You do not have sufficient permissions for this action',
	'categories:entity:error' => 'Entity does not exist or you do not have permissions to access it',
	'categories:manage:success' => 'Categories have been updated successfully',
);

return array_merge($general, $navigation, $forms, $actions);