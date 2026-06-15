<?php
/**
 * Plugin Name: Nevasenda - Importar galería
 * Description: Crea las 14 fotos iniciales del CPT "Foto" a partir de las imágenes estáticas del tema. Visita /wp-admin/?nevasenda_galeria_import=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// /wp-admin/?nevasenda_galeria_reset=1 -> borra las fotos importadas pa poder re-importar
	if ( isset( $_GET['nevasenda_galeria_reset'] ) ) {
		$ids = get_option( 'nevasenda_galeria_post_ids', array() );
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
		delete_option( 'nevasenda_galeria_post_ids' );
		delete_option( 'nevasenda_galeria_imported' );
		wp_die( 'Fotos de galería eliminadas (' . count( $ids ) . '). Ahora visita <a href="' . esc_url( admin_url( '?nevasenda_galeria_import=1' ) ) . '">?nevasenda_galeria_import=1</a> pa volver a importar.' );
	}

	if ( ! isset( $_GET['nevasenda_galeria_import'] ) ) {
		return;
	}

	if ( get_option( 'nevasenda_galeria_imported' ) ) {
		wp_die( 'La galería ya se importó antes. Pa reimportar, visita primero <a href="' . esc_url( admin_url( '?nevasenda_galeria_reset=1' ) ) . '">?nevasenda_galeria_reset=1</a>.' );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$imported_ids = array();

	for ( $i = 1; $i <= 14; $i++ ) {
		$filename = 'gallery-' . $i . '.jpg';
		$filepath = get_template_directory() . '/assets/images/' . $filename;
		if ( ! file_exists( $filepath ) ) {
			continue;
		}

		$post_id = wp_insert_post( array(
			'post_title'  => 'Foto ' . $i,
			'post_type'   => 'foto_galeria',
			'post_status' => 'publish',
			'menu_order'  => $i,
		) );
		if ( ! $post_id ) {
			continue;
		}
		$imported_ids[] = $post_id;

		$contents = file_get_contents( $filepath );
		$upload   = wp_upload_bits( $filename, null, $contents );
		if ( $upload['error'] ) {
			continue;
		}

		$filetype  = wp_check_filetype( $upload['file'], null );
		$attach_id = wp_insert_attachment( array(
			'guid'           => $upload['url'],
			'post_mime_type' => $filetype['type'],
			'post_title'     => 'Foto ' . $i,
			'post_status'    => 'inherit',
		), $upload['file'], $post_id );

		if ( $attach_id ) {
			$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			set_post_thumbnail( $post_id, $attach_id );
		}
	}

	update_option( 'nevasenda_galeria_post_ids', $imported_ids );
	update_option( 'nevasenda_galeria_imported', 1 );

	wp_die( 'Importadas ' . count( $imported_ids ) . ' fotos a la galería. <a href="' . esc_url( admin_url( 'edit.php?post_type=foto_galeria' ) ) . '">Ver en wp-admin</a> · <a href="' . esc_url( home_url( '/galeria/' ) ) . '">Ver galería</a>' );
} );
