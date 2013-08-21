<?php

// Add custom field type to profile manager
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