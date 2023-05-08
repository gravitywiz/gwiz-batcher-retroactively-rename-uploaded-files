<?php
/*
 * Plugin Name:  Gravity Wiz Batcher: File Renamer Retroactively Rename Uploaded Files
 * Plugin URI:   http://gravitywiz.com
 * Description:  Batcher to rename all files uploaded before the GP File Renamer Perk was activated.
 * Author:       Gravity Wiz
 * Version:      0.1
 * Author URI:   http://gravitywiz.com
 */

add_action( 'init', 'gwiz_batcher' );

function gwiz_batcher() {

	if ( ! is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	require_once( plugin_dir_path( __FILE__ ) . 'class-gwiz-batcher.php' );

	new Gwiz_Batcher( array(
		'title'              => 'GPFR Batcher',
		'id'                 => 'gpfr-batcher',
		'size'               => 100,
		'show_form_selector' => true,
		'get_items'          => function ( $size, $offset, $form_id = null ) {

			$paging  = array(
				'offset'    => $offset,
				'page_size' => $size,
			);
			$search_criteria = array(
				'status' => 'active',
			);

			$entries = GFAPI::get_entries( $form_id, $search_criteria, null, $paging, $total );

			return array(
				'items' => $entries,
				'total' => $total,
			);
		},
		'process_item'       => function ( $entry ) {
			$form = GFAPI::get_form( $entry['form_id'] );
			gp_file_renamer()->rename_uploaded_files( $entry, $form );
		},
	) );

}
