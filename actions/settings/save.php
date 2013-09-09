<?php

$params = get_input('params', array(), false); // don't filter the results so that html inputs remain unchanged
$plugin_id = 'hypeCategories';
$plugin = elgg_get_plugin_from_id($plugin_id);

if (!($plugin instanceof ElggPlugin)) {
	register_error(elgg_echo('plugins:settings:save:fail', array($plugin_id)));
	forward(REFERER);
}

$plugin_name = $plugin->getManifest()->getName();

$result = false;

foreach ($params as $k => $v) {
	if (is_array($v)) {
		$v = serialize($v);
	}
	$result = $plugin->setSetting($k, $v);
	if (!$result) {
		register_error(elgg_echo('plugins:settings:save:fail', array($plugin_name)));
		forward(REFERER);
		exit;
	}
}

system_message(elgg_echo('plugins:settings:save:ok', array($plugin_name)));
forward(REFERER);