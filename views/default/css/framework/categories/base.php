<?php if (false) : ?><style type="text/css"><?php endif; ?>

	<?php $path = elgg_get_site_url() . 'mod/hypeCategories/graphics/' ?>

	[class*="categories-icon-"] {
		height:22px;
		width:22px;
		display:inline-block;
		vertical-align:middle;
		background-position:50% 50%;
		background-repeat:no-repeat;
		background-size:100%;
		opacity:0.5;
	}
	[class*="categories-icon-"].icon-small {
		height:16px;
		width:16px;
	}
	[class*="categories-icon-"] + span {
		margin-left: 5px;
		display: inline-block;
		vertical-align: middle;
		line-height: 26px;
	}
	[class*="categories-icon-"].icon-small + span {
		line-height: 16px;
	}
	.categories-icon-move {
		background-image:url(<?php echo $path ?>icons/move.png);
		cursor: move;
	}
	.categories-icon-edit {
		background-image:url(<?php echo $path ?>icons/edit.png);
	}
	.categories-icon-plus {
		background-image:url(<?php echo $path ?>icons/plus.png);
	}
	.categories-icon-minus {
		background-image:url(<?php echo $path ?>icons/minus.png);
	}
	.categories-icon-upload {
		background-image:url(<?php echo $path ?>icons/upload.png);
	}
	.categories-icon-checked {
		background-image:url(<?php echo $path ?>icons/checked.png);
	}
	.categories-icon-info {
		background-image:url(<?php echo $path ?>icons/info.png);
	}
	.elgg-menu-categories .categories-icon-move {
		margin: 10px;
	}
	.elgg-menu-categories .categories-icon-plus,
	.elgg-menu-categories .categories-icon-minus {
		float:right;
		margin:10px 5px;
	}
	.elgg-menu-categories .categories-icon-info,
	.elgg-menu-categories .categories-icon-icon {
		cursor:pointer;
	}
	.elgg-menu-categories .elgg-menu-closed .elgg-child-menu {
		display: none;
	}
	.elgg-menu-categories .elgg-child-menu-toggle {
		margin: -5px 5px 0 0;
		cursor: pointer;
		color: #666;
		font-size: 11px;
		display: inline-block;
		vertical-align: bottom;
	}
	.elgg-menu-categories .elgg-menu-closed > .elgg-child-menu-toggle > .collapse {
		display: none;
	}
	.elgg-menu-categories .elgg-menu-open > .elgg-child-menu-toggle > .collapse {
		display: block;
	}
	.elgg-menu-categories .elgg-menu-closed > .elgg-child-menu-toggle > .expand {
		display: block;
	}
	.elgg-menu-categories .elgg-menu-open > .elgg-child-menu-toggle > .expand {
		display: none;
	}
	.elgg-menu-categories li {
		line-height: 18px;
		vertical-align: middle;
	}
	.elgg-menu-categories .elgg-menu-closed {
		/*margin-left: 14px;*/
	}
	.elgg-menu-categories .elgg-menu-closed.elgg-menu-parent {
		margin-left: auto;
	}

	.categories-manage .categories-category-title, .categories-manage .categories-category-description, .categories-manage .categories-category-access {
		display: inline-block;
		margin: 5px 10px;
		vertical-align: middle;
		width: 25%;
	}
	.categories-manage .categories-category-block {
		border: 1px solid #e8e8e8;
		margin: 3px;
		background: #f4f4f4;
	}
	.elgg-menu-categories ul {
		margin-left: 35px;
	}
	.categories-manage .categories-icon-upload img {
		max-width: 100%;
	}

	.categories-manage .elgg-menu-categories ul li .categories-icon-plus {
		display: none;
	}
	.categories-manage .elgg-menu-categories ul li:last-child .categories-icon-plus {
		display: inline-block;
	}
	.categories-manage .categories-draggable-placeholder {
		border:2px dashed #e8e8e8;
		height:auto;
		min-height:200px;
	}
	.categories-manage .elgg-child-menu-toggle {
		display:none;
	}
	.categories-manage .elgg-menu-categories .elgg-menu-closed .elgg-child-menu {
		display: block;
	}
	.categories-tree .elgg-menu-categories ul {
		margin-left:13px;
	}
	.categories-tree .elgg-menu-categories li.elgg-menu-nochildren {
		margin-left: 13px;
	}
	.categories-tree .categories-category-icon img {
		height: 16px;
		width: auto;
		display: inline-block;
vertical-align: middle;
	}
	.categories-tree li > span,
	.categories-tree li > a,
	.categories-tree li > a span {
		margin-right: 5px;
		display: inline;
		vertical-align: top;
		line-height: 26px;
	}

	.categories-category-full > .elgg-image {
		max-width:100px;
		margin-right:20px;
	}

	.categories-sidebar-tree-module .elgg-foot {
		margin-top:10px;
		text-align:right;
	}
	<?php if (false) : ?></style><?php endif; ?>