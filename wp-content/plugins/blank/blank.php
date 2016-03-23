<?php
/*
Plugin Name: Blank Plugin
Plugin URI: http://graemewilson.co.nz/wordpress-plugins/wordpress-blank-plugin/
Description: A blank plugin that simply adds a new admin menu item called 'blank', with a single submenu item that displays a blank page.
             Documented with links to WordPress Codex
Author: Graeme Wilson
Author URI:http://graemewilson.co.nz/
Version: 0.1
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

/*
GENERAL NOTES

 * PHP short tags ( e.g. <?= ?> ) are not used as per the advice from PHP.net
 * No database implementation
 * IMPORTANT: Menu is visible to anyone who has 'read' capability, so that means subscribers
              See: http://codex.wordpress.org/Roles_and_Capabilities for information on appropriate settings for different users

*/

// Make sure that no info is exposed if file is called directly -- Idea taken from Akismet plugin
if ( !function_exists( 'add_action' ) ) {
	echo "This page cannot be called directly.";
	exit;
}

// Define some useful constants that can be used by functions
if ( ! defined( 'WP_CONTENT_URL' ) ) {	
	if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
	define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
}
if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
if ( ! defined( 'WP_CONTENT_DIR' ) ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) ) define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if ( basename(dirname(__FILE__)) == 'plugins' )
	define("BLANK_DIR",'');
else define("BLANK_DIR" , basename(dirname(__FILE__)) . '/');
define("BLANK_PATH", WP_PLUGIN_URL . "/" . BLANK_DIR);

/* Add new menu */
add_action('admin_menu', 'blank_add_pages');
// http://codex.wordpress.org/Function_Reference/add_action

/*

******** BEGIN PLUGIN FUNCTIONS ********

*/


// function for: 
function blank_add_pages() {

  // anyone can see the menu for the Blank Plugin
  add_menu_page('Blank Overview','Blank Plugin', 'read', 'blank_overview', 'blank_overview', BLANK_PATH.'images/b_status.png');
  // http://codex.wordpress.org/Function_Reference/add_menu_page

  // this is just a brief introduction
  add_submenu_page('blank_overview', 'Overview for the Blank Plugin', 'Overview', 'read', 'blank_overview', 'blank_intro');
  // http://codex.wordpress.org/Function_Reference/add_submenu_page

}

function blank_overview() {
?>
<div class="wrap"><h2>Blank Plugin Overview</h2>
<p>An overview of the blank plugin.</p>
</div>
<?php
exit;
}

?>