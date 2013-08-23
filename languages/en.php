<?php

$english = array(

	'item:object:hjcategory' => 'Categories',
	
	'hj:categories' => 'Categories',
	'hj:categories:category' => 'Category: %s',
	'hj:categories:subcategories' => 'Subcategories',
	'hj:categories:latest_items' => 'Latest items',
	
	'hj:categories:manage' => 'Manage categories',

	'hj:categories:site' => 'Site-wide Categories',
	'hj:categories:group' => '%s Categories',

	'hj:categories:select:site' => 'Select category',
	'hj:categories:select:group' => 'Select group category',

	'hj:categories:category:title' => '%s [%s]',
	'hj:categories:count:filed' => '%s items in this category',
	'hj:categories:edit' => 'Toggle edit form',
	'hj:categories:add_subcategory' => 'Add subcategory',

	'hj:categories:edit:icon' => 'Upload an Icon',
	'hj:categories:edit:title' => 'Category Name',
	'hj:categories:edit:description' => 'Category Description',
	'hj:categories:edit:access_id' => 'Visibility',

	'hj:categories:type_subtype_pairs' => 'Types of content items that categories should be applied to',
	'hj:categories:type_subtype_pairs:help' => 'Selected types will appear in the category page filter.
			This setting will also restrict the scope of to the default event handler, since each action
			can trigger multiple create and update events (creating a blog for example is accompanying with creating notifications, notices etc).
			Please note that selecting or unselecting an item will not automatically include or remove a category input field to/from the form',
	'hj:categories:entity_menu' => 'Display category in entity menu',
	'hj:categories:group_categories' => 'Enable group categories',
	'hj:categories:group_tree_site' => 'Add site categories to the group tree',

	'hj:categories:display' => 'Display',
	'hj:categories:donotdisplay' => 'Do not display',
	'hj:categories:enable' => 'Enable',
	'hj:categories:disable' => 'Disable',

	'hj:categories:remove:confirm' => 'Are you sure you want to remove this category and all subcategories under it?',

	'hj:categories:filter:type' => '%s [%s]',

	'hj:categories:empty' => 'There are no items in this category',
	'hj:categories:view_all' => 'View all items',
	
	'profile_manager:admin:options:category' => 'Category',

    );

add_translation("en", $english);
?>