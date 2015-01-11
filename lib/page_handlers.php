<?php

namespace hypeJunction\Categories;

use ElggFile;

/**
 * Categories page handler
 *
 * /categories/all/[<container_guid>]
 * /categories/manage/<container_guid>
 * /categories/view/<guid>
 * /categories/group/<group_guid>/<guid>
 * /categories/icon/<guid>/<size>
 *
 * @param array $page URL segments
 * @return boolean
 */
function page_handler($page) {

	switch ($page[0]) {

		case 'all' :

			set_input('container_guid', $page[1]);
			$page = elgg_view('resources/categories/all');
			break;

		case 'manage' :

			set_input('container_guid', $page[1]);
			$page = elgg_view('resources/categories/manage');
			break;

		case 'view' :

			set_input('guid', $page[1]);
			$page = elgg_view('resources/categories/view');
			break;

		case 'group' :

			set_input('group_guid', $page[1]);
			set_input('guid', $page[2]);
			$page = elgg_view('resources/categories/group');
			break;

		case 'icon' :
			
			$entity = get_entity($page[1]);
			$size = strtolower(elgg_extract(2, $page, 'medium'));

			if (!elgg_instanceof($entity)) {
				return false;
			}

			if (!array_key_exists($size, elgg_get_config('icon_sizes'))) {
				$size = 'medium';
			}

			$success = false;

			$filename = "icons/" . $entity->getGUID() . $size . ".jpg";

			$filehandler = new ElggFile();
			$filehandler->owner_guid = $entity->owner_guid;
			$filehandler->setFilename($filename);

			if ($filehandler->open("read")) {
				if ($contents = $filehandler->read($filehandler->getSize())) {
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
	}

	if (!$page) {
		return false;
	}

	echo $page;
	return true;
}
