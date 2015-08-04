<?php

/**
 * Traverse Categories for Elgg
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2011-2015, Ismayil Khayredinov
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2
 */
try {
	require_once __DIR__ . '/lib/autoloader.php';
	hypeCategories()->boot();
} catch (Exception $ex) {
	register_error($ex->getMessage());
}