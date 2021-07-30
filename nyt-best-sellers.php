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

<<<<<<< HEAD
    $charset_collate = $wpdb->get_charset_collate();

    $tableName = $wpdb->prefix . 'books';

=======
function nyt_activate()
{
    global $wpdb;
 
    $charset_collate = $wpdb->get_charset_collate();
 
    $tableName = $wpdb->prefix . 'books';
 
>>>>>>> c8a91fa0f26f904edccc68fc00512ee6384501cf
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
<<<<<<< HEAD

=======
 
>>>>>>> c8a91fa0f26f904edccc68fc00512ee6384501cf
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'nyt_activate');
<<<<<<< HEAD

function nyt_options_page_html()
{
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "wporg_options"
            settings_fields('nyt');
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections('nyt');
            // output save settings button
            submit_button(__('Save Settings', 'textdomain'));
            ?>
        </form>
    </div>
<?php
}

add_action('admin_menu', 'nyt_options_page');
add_action('admin_init', 'nyt_register_settings');

function nyt_options_page()
{
    add_menu_page(
        'NYT Best Sellers',
        'NYT Best Sellers',
        'manage_options',
        'nyt',
        'nyt_options_page_html',
        'dashicons-book-alt',
        20
    );
    add_submenu_page('nyt','Settings',"Settings","manage_options","nyt-settings","nyt_options_page_html",20);
}

function nyt_register_settings()
{
    register_setting('nyt','nyt_apikey');
    add_settings_section('nyt_options_section','Paramètres',function() {
    }, 'nyt');
    add_settings_field('nyt_options_apikey','Clé API New York Times',function() {
        ?>
        <input type="text" name="nyt_apikey" value="<?= get_option('nyt_apikey') ?>">
        <?php
    },'nyt', 'nyt_options_section');
}
=======
 
>>>>>>> c8a91fa0f26f904edccc68fc00512ee6384501cf
