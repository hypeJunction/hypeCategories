<?php

$item = $vars['item'];

//$item_class = 'elgg-menu-closed';
$item_class = 'elgg-menu-open';

$children = $item->getChildren();
if ($children) {
	$item_class = "$item_class elgg-menu-parent";
	$toggle = '<span class="elgg-child-menu-toggle"><span class="collapse ">&#9698;</span><span class="expand">&#9654;</span></span>';
} else {
	$item_class = "$item_class elgg-menu-nochildren";
}

$item_class = "$item_class {$item->getItemClass()}";
if ($item->getSelected()) {
	$item_class = "$item_class elgg-state-selected";
}
if (isset($vars['item_class']) && $vars['item_class']) {
	$item_class .= ' ' . $vars['item_class'];
}

echo "<li class=\"$item_class\">";
echo $toggle . $item->getContent();
if ($children) {
	echo elgg_view('navigation/menu/categories/section', array(
		'items' => $children,
		'class' => 'elgg-menu elgg-child-menu',
	));
}
echo '</li>';
