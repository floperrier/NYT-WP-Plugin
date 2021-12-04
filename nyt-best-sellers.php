 <?php

/**
 * Plugin Name: New York Times - Best Sellers
 * Description: This plugin will provides you all newest bestsellers.
 * Version: 1.0
 * Author: The best team ever
 */


function nyt_register_shortcode($params) {
    extract(shortcode_atts(
        array(
    	    'cat' => 'FR'
    ), $params));
    if (isset($params['cat'])) {
        $category = $params['cat'];
        $apikey = get_option('nyt_apikey');
        $response = wp_remote_get("https://api.nytimes.com/svc/books/v3/lists/current/" . $category . ".json?api-key=" . $apikey);

        if (is_array($response) && !is_wp_error($response)) {
            $headers = $response['headers']; // array of http header lines
            $body    = $response['body']; // use the content
            $results = json_decode($body)->results;
        }
        //$return = "<pre>" . var_dump($results) . "</pre>";
        $html = null;
        $html .= "<div class='container'>";
        $html .= "<div class='row row-cols-3'>";
        foreach($results->books as $book) {
            $html .= "<div class='col-lg-4'>";
            $html .= "<div class='card'>";
            $html .= "<a href='{$book->amazon_product_url}'><img src='{$book->book_image}' class='card-img-top' alt='Image livre'></a>";
            $html .= "<div class='card-body'>";
            $html .= "<h4>{$book->title}</h4>";
            $html .= "</div></div>";
            $html .= "</div>";
            
        }
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }
}
add_shortcode('best_sellers', 'nyt_register_shortcode');


function nyt_register_assets()
{
    // Déclarer style
    wp_enqueue_style('dataTablesCSS', "https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css", array(), '1.0');

    // Declarer js
    wp_enqueue_script('dataTables', "https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js", array('jquery'), '1.0');
    var_dump("coucou");
}
add_action('admin_enqueue_scripts', 'nyt_register_assets');

add_action('init', function() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), false);
});

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

    $tableName1 =  $wpdb->prefix . 'books_categories';

    $sql = "CREATE TABLE $tableName1  (
        id bigint(20) unsigned NOT NULL auto_increment,
        name varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'nyt_activate');

function nyt_uninstall()
{
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}books_categories;");
}

register_uninstall_hook(__FILE__, 'nyt_uninstall');

function nyt_options_page_html()
{
    global $wpdb;

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

            //add_user_meta(get_current_user_id(), 'api_key', '5NFkwspUBXa5GUpZxoh2aR5b4ATitquB');
            // output save settings button
            submit_button(__('Save Settings', 'textdomain'));
            ?>
        </form>
    </div>
<?php
}

function nyt_books_categories_html()
{
    global $wpdb;
    if (isset($_POST['sync'])) {
        $apikey = get_option('nyt_apikey');
        $response = wp_remote_get("https://api.nytimes.com/svc/books/v3/lists/names.json?api-key=" . $apikey);
        if (is_array($response) && !is_wp_error($response)) {
            $headers = $response['headers']; // array of http header lines
            $body    = $response['body']; // use the content
            $results = json_decode($body)->results;

            foreach ($results as $result) {
                /* echo "<pre>";
                var_dump($result);
                echo "</pre>";
                die(); */
                $wpdb->query("INSERT INTO wp_books_categories(name) VALUES('$result->list_name');");
            }
        }
    }
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}books_categories;");
    echo "<div id='container'";
    echo '<form action="" method="post">';
    echo '<input type="hidden" value="true" name="sync" />';
    submit_button('Sync categories');
    echo '</form>';
?>
    <script>
        jQuery(document).ready(function($) {
            $('#myTable').DataTable();
        });
    </script>

    <style>
        #container {
            width: 80%;
            margin: 2em auto;
        }
    </style>
    <table id="myTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result) : ?>
                <tr>
                    <td><?= $result->id ?></td>
                    <td><a href="admin.php?page=nyt-books"><?= $result->name ?></a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
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
    add_submenu_page('nyt', 'Books Categories', "Books Categories", "manage_options", "nyt-books-categories", "nyt_books_categories_html", 1);
    add_submenu_page('nyt', 'Settings', "Settings", "manage_options", "nyt-settings", "nyt_options_page_html", 20);
    remove_submenu_page('nyt', 'nyt');
}

function nyt_register_settings()
{
    register_setting('nyt', 'nyt_apikey');
    add_settings_section('nyt_options_section', 'Paramètres', function () {
    }, 'nyt');
    add_settings_field('nyt_options_apikey', 'Clé API New York Times', function () {
    ?>
        <input type="text" name="nyt_apikey" value="<?= get_option('nyt_apikey') ?>">
<?php
    }, 'nyt', 'nyt_options_section');
}
