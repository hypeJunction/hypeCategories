hypeCategories
==============

Categories and taxonomy for Elgg

## Features

* Support for site-wide and internal group categories
* User-friendly UI for adding and managing categories
* Support for category icons
* Context-aware category trees
* Context-aware content filtering by category (e.g. Blogs by category on blogs pages)
* Extendable API that allows adding new category subtypes

## Configuration

* If you are working with a large taxonomy, you will most likely need to update your PHP runtime configuration.
If, when managing categories, you loose entries or hierarchies, increase the value of ```max_input_vars```
http://www.php.net/manual/en/info.configuration.php

* Category configuration is context aware. You can specify which category subyptes to list,
which categorizied entity subtypes to count/display, what category URL pattern to use for each specific context.
To modify the default behavior, by filtering the config array using the `'get_context_settings', 'framework:categories'` hook.
See ```mod/hypeCategories/settings/default.php``` for a sample config. Place more general rules below more specific rules,
as the plugin will use the settings from the first rule that matches the current page URL.

* The plugin uses regex patterns to match page URLs to category contexts, as seen in the settings file.
If you don't want to modify the forms to include category input, you can do so, by injecting it via JS. Add the 'forms' parameter
for a given context, and specify the page URL and the CSS selector to inject the input after.

## Usage

### Adding category input

To add a category input to your form, add the following code:

```
$input = elgg_view('input/category', array(
	'name_override' => 'my_categories', // do not include, unless you are attaching some custom event hooks to process user input
	'multiple' => true, // specifies whether users should have an option to select multiple categories
	'entity' => $entity, // an entity, which is being edited (will be used to obtain currently selected categories, unless 'value' parameter is present)
	'value' => array(), // an array of category GUIDs to be selected by default
));
```

By default, the plugin will listen to ```'create','all'``` and ```'update','all'``` events
and create a ```filed_in``` relationship with selected categories (and all parent categories).

To display entity categories, use:

```
$output = elgg_view('output/category', array(
	'entity' => $entity // Entity for which the categories should be displayed
));
```

### Custom category subtypes

To add custom category subtypes to the workflow globally, update 'taxonomy_tree_subtypes' config value.

For example, you may want to have multiple taxonomies for categorizing content by topic, by context etc. The easiest way to achieve that, is by using different
category subtypes, e.g. topic, cluster, grouping etc, or blog_categories, bookmark_categories etc.

Context-based filtering of category subtypes can be achieved via ```'get_subtypes', 'framework:categories'``` hook.


### Internationalization

You can internationalize category names by adding translations to your language files. Translations should be namespaced with ```"category:$category_title"```. 


## Upgrading to 3.1

The focus of 3.1 is to improve performance. There are several changes that should  not but may affect your install:

1. Subcategory querying no longer uses LEFT JOIN for retrieving 'priority' metadata. If you are missing categories from your list,
run a batch query and set priority on all your categories to 0 (this will place them at the end of the list)


## Screenshots ##

![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/manage.png "Category Management Tool")
![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/form.png "Form Field")
![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/tree.png "Categories Tree")
![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/category_view.png "Category Full View")