<?php

function hj_categories_get_subcategories($guid = null, $limit = 0) {
    $type = 'object';
    $subtype = 'hjcategory';

    if (!$guid) {
        $guid = elgg_get_site_entity()->guid;
    }

    $container_guid = $guid;

    $categories = hj_framework_get_entities_by_priority($type, $subtype, $owner_guid, $container_guid, $limit);

    return $categories;
}
