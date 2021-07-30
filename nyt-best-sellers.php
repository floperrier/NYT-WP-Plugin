<?php

/**
 * Plugin Name: New York Times - Best Sellers
 * Description: This plugin will provides you all newest bestsellers.
 * Version: 1.0
 * Author: The best team ever
 */

function nyt_activate()
{
    global $wpdb;
 
    $charset_collate = $wpdb->get_charset_collate();
 
    $tableName = $wpdb->prefix . 'books';
 
    $sql = "CREATE TABLE $tableName (
        id bigint(20) unsigned NOT NULL auto_increment,
        title varchar(255) NOT NULL,
        description varchar(255) NOT NULL,
        author varchar(255) NOT NULL,
        price int(20) unsigned NOT NULL,
        publisher varchar(255) NOT NULL,
        primary_isbn13 int(20) NOT NULL,
        primary_isbn10 int(20) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'nyt_activate');
 