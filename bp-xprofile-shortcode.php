<?php 
/*
Plugin Name: BP XProfile Shortcode
Plugin URI: http://tylerdigital.com/labs/bp-xprofile-shortcode
Description: Adds Shortcode for BuddyPress XProfile data
Version: 1.0.1
Author: Tyler Digital
Author URI: http://tylerdigital.com/
*/

/**
 * Copyright (c) 2012 Tyler Digital. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

add_action('bp_init', 'td_bpxps_init');
function td_bpxps_init() {
	add_shortcode('xprofile', 'td_bpxps_xprofile_shortcode');
}

function td_bpxps_xprofile_shortcode($attributes) {
	if(empty($attributes['field'])) return false;
	extract($attributes);
	$user_id = (isset($user)) ? $user : false;
	global $bp;
	global $post;

	if (isset($user) && !in_array($user, array('displayed', 'author', 'current')) ) {
		// hardcoded username/id
		$user_object = get_user_by( 'slug', $user);
		if ( empty( $user_object->ID ) ) {
			$user_object = get_user_by( 'id', $user );
		}
		if ( empty( $user_object->ID ) ) {
			$user_object = get_user_by( 'login', $user );
		}
	}
	if ( !empty( $user_object->ID ) ) {
		return xprofile_get_field_data($field, $user_object->ID, 'comma');
	}

	if ((isset($user) && $user=='displayed') ||
		(!isset($user) && isset($bp->displayed_user->id))) { 
		// On profile page, show the displayed user's information
		$user_id = $bp->displayed_user->id;
	} elseif ((isset($user) && $user=='author') ||
		(!isset($user) && !empty($post->post_author))) {
		// On author or single post page, show the author's information
		$user_id = $post->post_author;
	} elseif ((isset($user) && $user=='current') ||
		(!isset($user) && is_user_logged_in())) {
		// Show the currently logged in user's information
		$user_id = get_current_user_id();
	}

	if (empty($user_id)) return false;

	return xprofile_get_field_data($field, $user_id, 'comma');
}

?>