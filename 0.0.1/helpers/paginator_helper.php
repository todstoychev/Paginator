<?php

/**
 * Sets the model name to work with
 * 
 * @param string $model Model name to work with
 */
function set_model($model) {
    $CI = & get_instance();
    $CI->paginator->set_model($model);
}

/**
 * Sets the translation files. Can accept array of filenames.
 * 
 * @param mixed $filenames Language filenames
 * @param string $language Language to use 
 */
function set_translation_file($filenames, $language) {
    $CI = & get_instance();
    $CI->lang->load($filenames, $language);
}

/**
 * Renders table header row.
 * 
 * @param array $columns Array of columns. 
 * Format @example 
 * array('<column_name_in_the_table>' => array('<column_name_to_show>', [<sortable>]))
 * 'sortable' can be true or false
 * @param string $order Order direction. Accepts 'asc' and 'desc' or 'ASC' and 'DESC'. 
 * @param string $uri URI to the controller
 */
function table_header($columns, $items_per_page = 10, $page = 1, $order = 'desc', $uri = null) {
    $CI = & get_instance();
    echo $CI->paginator->set_table_header($columns, $items_per_page, $page, $order, $uri);
}

/**
 * Gets the data from the database
 * 
 * @param CI_DB_mysql_driver $query Initial query
 * @param string $table Database table name
 * @param integer $items_per_page Items per page
 * @param integer $page Page number
 * @param string $order Sort direction
 * @param string $param Database column name to sort
 * @return \CI_model object Query result
 */
function get_data($query, $table, $items_per_page, $page, $order = 'desc', $param = null) {
    $CI = & get_instance();

    return $CI->paginator->query($query, $table, $items_per_page, $page, $order, $param);
}

/**
 * Renders items per page dropdown
 * 
 * @param string $uri URI to controller
 * @param integer $items_per_page Items per page
 * @param array $attr Attributes to use for the form
 */
function items_per_page($uri, $items_per_page, $attr = []) {
    $CI = & get_instance();

    echo '<form action="' . base_url() . $uri . '" id="per-page" method="POST" ';
    foreach ($attr as $key => $value) {
        echo $key . '="' . $value . '" ';
    }
    echo '>';
    $CI->paginator->get_items_per_page_dropdown($items_per_page);
    echo '</form>';
}

/**
 * Creates the pagination links
 * 
 * @param integer $items_per_page Items per page
 * @param string $uri URI to the controller action
 * @param integer $page Page number
 * @param string $order Order direction
 * @param string $param Column from the database to sort
 */
function pagination($items_per_page, $uri, $page = 1, $order = null, $param = null) {
    $CI = & get_instance();
    $CI->paginator->get_pagination($items_per_page, $uri, $page, $order, $param);
}
