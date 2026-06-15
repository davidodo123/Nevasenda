<?php
/**
 * Plugin Name: Nevasenda - Testimonios de ejemplo
 * Description: Crea 4 testimonios de ejemplo pa la sección "Lo que dice nuestra comunidad" de la portada. Visita /wp-admin/?nevasenda_testimonios_import=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// /wp-admin/?nevasenda_testimonios_reset=1 -> borra los testimonios importados pa poder re-importar
	if ( isset( $_GET['nevasenda_testimonios_reset'] ) ) {
		$ids = get_option( 'nevasenda_testimonios_post_ids', array() );
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
		delete_option( 'nevasenda_testimonios_post_ids' );
		delete_option( 'nevasenda_testimonios_imported' );
		wp_die( 'Testimonios eliminados (' . count( $ids ) . '). Ahora visita <a href="' . esc_url( admin_url( '?nevasenda_testimonios_import=1' ) ) . '">?nevasenda_testimonios_import=1</a> pa volver a importar.' );
	}

	if ( ! isset( $_GET['nevasenda_testimonios_import'] ) ) {
		return;
	}

	if ( get_option( 'nevasenda_testimonios_imported' ) ) {
		wp_die( 'Los testimonios ya se importaron antes. Pa reimportar, visita primero <a href="' . esc_url( admin_url( '?nevasenda_testimonios_reset=1' ) ) . '">?nevasenda_testimonios_reset=1</a>.' );
	}

	$testimonios = array(
		array(
			'nombre'  => 'Marta Gil',
			'rol'     => 'Senderista, Sierra de Gredos',
			'rating'  => 5,
			'content' => 'Las fichas de ruta son una pasada: la distancia, el desnivel y el mapa real me ayudan a saber exactamente a qué me enfrento antes de salir. Repito cada fin de semana.',
		),
		array(
			'nombre'  => 'Javier Soto',
			'rol'     => 'Senderista, Picos de Europa',
			'rating'  => 5,
			'content' => 'Encontré rutas que no aparecían en otras webs y con datos técnicos muy fiables. El foro también es útil pa preguntar el estado del sendero antes de ir.',
		),
		array(
			'nombre'  => 'Lucía Fernández',
			'rol'     => 'Senderista, Sierra Nevada',
			'rating'  => 4,
			'content' => 'Me gusta mucho la galería de fotos, da una idea real del paisaje. Sería genial tener más rutas de la zona, pero lo que hay está muy bien explicado.',
		),
		array(
			'nombre'  => 'Pablo Ramírez',
			'rol'     => 'Senderista, Pirineos',
			'rating'  => 5,
			'content' => 'La comunidad es muy activa y la gente responde rápido en el foro. Las rutas con varias etapas y el track GPX descargable son justo lo que necesitaba.',
		),
	);

	$imported_ids = array();

	foreach ( $testimonios as $t ) {
		$post_id = wp_insert_post( array(
			'post_title'   => $t['nombre'],
			'post_content' => $t['content'],
			'post_type'    => 'testimonio',
			'post_status'  => 'publish',
		) );
		if ( ! $post_id ) {
			continue;
		}
		$imported_ids[] = $post_id;

		update_post_meta( $post_id, '_testimonio_rol', $t['rol'] );
		update_post_meta( $post_id, '_testimonio_rating', $t['rating'] );
	}

	update_option( 'nevasenda_testimonios_post_ids', $imported_ids );
	update_option( 'nevasenda_testimonios_imported', 1 );

	wp_die( 'Importados ' . count( $imported_ids ) . ' testimonios. <a href="' . esc_url( admin_url( 'edit.php?post_type=testimonio' ) ) . '">Ver en wp-admin</a> · <a href="' . esc_url( home_url( '/' ) ) . '">Ver portada</a>' );
} );
