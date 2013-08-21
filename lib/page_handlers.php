<?php

elgg_register_page_handler('category', 'hj_categories_page_handler');
elgg_register_page_handler('categories', 'hj_categories_page_handler'); // alias

/**
 * Categories page handler
 * 
 * @param array $page Array of url segments
 * @return boolean
 */
function hj_categories_page_handler($page) {

	switch ($page[0]) {

		case 'all' :

			$container = get_entity($page[1]);
			if (!elgg_instanceof($container)) {
				$container = elgg_get_site_entity();
			}

			elgg_push_breadcrumb(elgg_echo('hj:categories'));

			$title = elgg_echo('hj:categories');

			$content = elgg_view('framework/categories/all', array(
				'container' => $container
			));

			$layout = elgg_view_layout('content', array(
				'title' => $title,
				'content' => $content,
				'filter' => false,
			));

			echo elgg_view_page($title, $layout);
			return true;
			break;

		// categories/manage/$container_guid
		case 'manage' :
			$container = get_entity($page[1]);
			if (!elgg_instanceof($container)) {
				$container = elgg_get_site_entity();
			}
			// Categories can only be contained by the site or a group
			if (!elgg_instanceof($container, 'site') && !elgg_instanceof($container, 'group')) {
				return false;
			}
			// User should be able to edit an entity to add categories to it
			if (!$container->canEdit()) {
				return false;
			}

			if (elgg_instanceof($container, 'group')) {
				elgg_set_page_owner_guid($container->guid);
				$title = elgg_echo('hj:categories:group', array($container->name));
				$layout = 'one_sidebar';
			} else {
				elgg_set_context('admin');
				$title = elgg_echo('hj:categories:site');
				$layout = 'admin';
			}

			$content = elgg_view_form('categories/manage', array(
				'enctype' => 'multipart/form-data',
					), array(
				'container' => $container
			));

			$layout = elgg_view_layout($layout, array(
				'title' => $title,
				'content' => $content,
			));

			echo elgg_view_page($title, $layout);
			return true;
			break;

		case 'view' :

			$guid = elgg_extract(1, $page);
			$entity = get_entity($guid);

			if (!elgg_instanceof($entity, 'object', 'hjcategory')) {
				return false;
			}

			$crumbs = hj_categories_get_hierarchy($entity->guid, false);
			if ($crumbs) {
				foreach ($crumbs as $crumb) {
					if (elgg_instanceof($crumb)) {
						elgg_push_breadcrumb($crumb->title, $crumb->getURL());
						$container = $crumb->getContainerEntity();
						if (elgg_instanceof($container, 'group')) {
							elgg_set_page_owner_guid($container->guid);
						}
					}
				}
			}
			elgg_push_breadcrumb($entity->title);

			$title = elgg_echo('hj:categories:category', array($entity->title));

			$content = elgg_view_entity($entity, array(
				'full_view' => true
			));

			$layout = elgg_view_layout('content', array(
				'title' => $title,
				'content' => $content,
				'filter' => false
			));

			echo elgg_view_page($title, $layout);

			return true;
			break;

		case 'group' :

			if (!HYPECATEGORIES_GROUP_CATEGORIES) {
				return false;
			}

			$group_guid = elgg_extract(1, $page);
			$guid = elgg_extract(2, $page);
			$entity = get_entity($guid);

			elgg_set_page_owner_guid($group_guid);

			group_gatekeeper();

			if (!elgg_instanceof($entity, 'object', 'hjcategory')) {
				return false;
			}

			$crumbs = hj_categories_get_hierarchy($entity->guid, false);
			if ($crumbs) {
				foreach ($crumbs as $crumb) {
					elgg_push_breadcrumb($crumb->title, $crumb->getURL());
				}
			}
			elgg_push_breadcrumb($entity->title);

			$title = elgg_echo('hj:categories:category', array($entity->title));

			$content = elgg_view_entity($entity, array(
				'full_view' => true
			));

			$layout = elgg_view_layout('content', array(
				'title' => $title,
				'content' => $content,
				'filter' => false,
			));

			echo elgg_view_page($title, $layout);

			return true;
			break;

		case 'icon' :
			$entity = get_entity($page[1]);
			$size = strtolower(elgg_extract(2, $page, 'medium'));

			if (!elgg_instanceof($entity)) {
				return false;
			}

			$size = strtolower(get_input('size'));

			if (!array_key_exists($size, elgg_get_config('icon_sizes'))) {
				$size = 'medium';
			}

			$success = false;

			$filename = "icons/" . $entity->getGUID() . $size . ".jpg";

			$filehandler = new ElggFile();
			$filehandler->owner_guid = $entity->owner_guid;
			$filehandler->setFilename($filename);

			if ($filehandler->open("read")) {
				if ($contents = $filehandler->read($filehandler->size())) {
					$success = true;
				}
			}

			header("Content-type: image/jpeg");
			header('Expires: ' . date('r', time() + 864000));
			header("Pragma: public");
			header("Cache-Control: public");
			header("Content-Length: " . strlen($contents));
			echo $contents;
			return true;
			break;
	}

	return false;
}
