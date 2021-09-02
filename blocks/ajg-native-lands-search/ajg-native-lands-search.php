<?php
function ajgnl_ajg_native_lands_search_block_init() {
	register_block_type_from_metadata( __DIR__ );
}
add_action( 'init', 'ajgnl_ajg_native_lands_search_block_init' );
