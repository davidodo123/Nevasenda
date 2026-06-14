<?php
/**
 * Plugin Name: Nevasenda - Shortcode del foro
 * Description: Pone el shortcode [asgarosforum] como contenido de la página "Foro". Visita /wp-admin/?nevasenda_foro_shortcode=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['nevasenda_foro_shortcode'] ) ) {
		return;
	}

	$page = get_page_by_path( 'foro' );

	if ( ! $page ) {
		wp_die( 'No existe la página "Foro" (slug foro).' );
	}

	wp_update_post( array(
		'ID'           => $page->ID,
		'post_content' => '[asgarosforum]',
	) );

	wp_die( 'Hecho. <a href="' . esc_url( home_url( '/foro/' ) ) . '">Ver foro</a>.' );
} );
