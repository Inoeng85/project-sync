<?php
/*
Plugin Name: Nucleus E-Cart
Plugin URI: http://www.nucleuslogic.com/
Version: 1.22
Author: Nucleus Logic
Description: This is Nucleus E-Cart plugin for wordpress
changes:- add shortcode
        - add button for home at end of transaction
        - add email cc setting
        - change html format of categories table
        - add attribute shortcode
        - add validation for sync sales process
        - fix number format for google analytic, add inactive setting to product details
*/

global $wpdb;
//$table_prefix = $wpdb->prefix.'ecart_';
$table_prefix = "wp_ecart_";

define('ECART_TBL_PREFIX', $table_prefix);
define('ECART_DIR', plugin_dir_path(__FILE__));
define('ECART_URL', plugin_dir_url(__FILE__));
define('ECART_FORM_PATH', '?page=ecart');
include_once dirname(__FILE__) . '/functions/functions.php';

$https_url = show_ecart_config("https_url");
$site_url = get_site_url();

$HTTPS_ECART_DIR = str_replace($site_url,$https_url,ECART_DIR);
define('HTTPS_ECART_DIR',$HTTPS_ECART_DIR);
$HTTPS_ECART_URL = str_replace($site_url,$https_url,ECART_URL);
define('HTTPS_ECART_URL',$HTTPS_ECART_URL);

add_action('admin_menu', 'ecart_add_menu_page');

register_activation_hook(__FILE__,'ecart_install');
register_deactivation_hook(__FILE__ , 'ecart_uninstall' );

define('ECART_FRONTEND_PATH', ECART_DIR."frontend/");

require_once( ECART_DIR . 'functions/frontend.functions.php');

require_once( ECART_DIR . 'functions/widget.functions.php');

require_once( ECART_DIR . 'library/nucleus_style_ajax.php');

?>