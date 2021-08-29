<?php
/**
 * Plugin Name:       Native Lands Search
 * Description:       Allow readers to search for a location and see the native lands that encompass it.
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           1.1.0
 * Author:            Alex J. Gustafson Tech
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajg-native-lands-search
 *
 * @package           ajgnl
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */
function ajgnl_ajg_native_lands_search_block_init() {
	register_block_type_from_metadata( __DIR__ );
}
add_action( 'init', 'ajgnl_ajg_native_lands_search_block_init' );
