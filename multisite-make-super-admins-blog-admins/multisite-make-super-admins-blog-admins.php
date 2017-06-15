<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jackmathis.com
 * @since             1.0.0
 * @package           Multisite_Make_Super_Admins_Blog_Admins
 *
 * @wordpress-plugin
 * Plugin Name:       Multisite Make Super Admins Blog Admins
 * Plugin URI:        https://github.com/jdabs/multisite-make-super-admins-blog-admins
 * Description:       A plugin that makes super admins blog/site admins on all child sites on the fly. It thus makes it easier to navigate across sites using the “My Sites” navigation.
 * Version:           1.0.0
 * Author:            Jack Mathis
 * Author URI:        https://jackmathis.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       multisite-make-super-admins-blog-admins
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) || ! is_multisite() ) {
	die;
}

// make all super admins automatically local admins of all child sites
function put_supers_as_admins() {
	global $wp_roles, $user, $current_user;
	$man_user_id = $current_user->ID;
	$man_sites   = get_sites();
	if ( is_super_admin( $man_user_id ) && is_admin() ) {
		foreach ( $man_sites as $man_site ) {
			switch_to_blog( $man_site->blog_id );
			$admins_list = get_site_option( 'site_admins', array( 'admin' ) );
			$u           = new WP_User( $man_user_id );
			if ( ! in_array( $user->user_login, $super_admins ) ) {
				$u->add_role( 'administrator' );
			}
			if ( is_wp_error( $u ) ) {
				restore_current_blog(); // bail out if there's an error so it doesn't get stuck on a child site
			} else {
				restore_current_blog();
			}
		}

	} else {
		return;
	}
}
add_action( 'admin_init', 'put_supers_as_admins' );