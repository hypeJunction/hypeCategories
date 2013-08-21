hypeCategories
==============

Categories and taxonomy for Elgg

## Features ##

* Support for site-wide and internal group categories
* User-friendly UI for adding and managing categories
* Support for category icons

## Notes ##

* hypeFramework is not required

## Usage ##

To add a category input to your form, add the following code:

```
$input = elgg_view('input/category', array(
	'name_override' => 'my_categories', // do not include, unless you are attaching some custom event hooks to process user input
	'multiple' => true, // specifies whether users should have an option to select multiple categories
	'entity' => $entity, // an entity, which is being edited (will be used to obtain currently selected categories, unless 'value' parameter is present)
	'value' => array(), // an array of category GUIDs to be selected by default
));
```

By default, the plugin will listed to ```'create','all'``` and ```'update','all'``` events
and create a 'filed_in' relationship with selected categories (and all parent
categories).

To display entity categories, use:

```
$output = elgg_view('output/category', array(
	'entity' => $entity // Entity for which the categories should be displayed
));
```


## Screenshots ##

![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/manager.png "Category Management Tool")
![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/formfield.png "Form Field")
![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/page.png "Categories Overview Page")
![alt text](https://raw.github.com/hypeJunction/hypeCategories/master/screenshots/fullview.png "Category Full View")