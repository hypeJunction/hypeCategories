<?php

function hj_categories_setup() {
    if (elgg_is_logged_in()) {
        hj_categories_setup_category_form();
        elgg_set_plugin_setting('hj:categories:setup', true);
        return true;
    }
    return false;
}

function hj_categories_setup_category_form() {
    //Setup Category creation form
    $form = new hjForm();
    $form->title = 'hypeCategories:category';
    $form->label = 'Category Creation Form';
    $form->description = 'hypeCategories Category Creation Form';
    $form->subject_entity_subtype = 'hjcategory';
    $form->notify_admins = false;
    $form->add_to_river = false;
    $form->comments_on = false;
    $form->ajaxify = true;

    if ($form->save()) {
        $form->addField(array(
            'title' => 'Icon',
            'name' => 'icon',
            'input_type' => 'entity_icon'
        ));
        $form->addField(array(
            'title' => 'Category Name',
            'name' => 'title',
            'mandatory' => true
        ));
        $form->addField(array(
            'title' => 'Description',
            'name' => 'description',
            'input_type' => 'longtext',
            'class' => 'elgg-input-longtext'
        ));
        $form->addField(array(
            'title' => 'Access Level',
            'name' => 'access_id',
            'input_type' => 'access'
        ));
        return true;
    }
    return false;
}
