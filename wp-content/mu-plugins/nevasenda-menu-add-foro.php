<?php
/**
 * Plugin Name: Nevasenda - Añadir Foro al menú
 * Description: Añade el ítem "Foro" al menú principal (orden: Inicio, Rutas, Galería, Comunidad, Foro, Blog). Visita /wp-admin/?nevasenda_menu_add_foro=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['nevasenda_menu_add_foro'] ) ) {
		return;
	}

	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['primary'] ) ? $locations['primary'] : 0;

	if ( ! $menu_id ) {
		wp_die( 'No hay menú asignado a la ubicación "primary". Revisa Apariencia &gt; Menús.' );
	}

	$existing = wp_get_nav_menu_items( $menu_id );
	$by_title = array();
	if ( $existing ) {
		foreach ( $existing as $item ) {
			$by_title[ mb_strtolower( $item->title ) ] = $item;
		}
	}

	$wanted = array(
		'inicio'    => home_url( '/' ),
		'rutas'     => get_post_type_archive_link( 'ruta' ),
		'galería'   => home_url( '/galeria/' ),
		'comunidad' => home_url( '/#comunidad' ),
		'foro'      => home_url( '/foro/' ),
		'blog'      => isset( $by_title['blog'] ) ? $by_title['blog']->url : home_url( '/' ),
	);

	$added    = array();
	$position = 1;

	foreach ( $wanted as $title => $url ) {
		if ( isset( $by_title[ $title ] ) ) {
			wp_update_nav_menu_item( $menu_id, $by_title[ $title ]->ID, array(
				'menu-item-title'    => $by_title[ $title ]->title,
				'menu-item-url'      => $by_title[ $title ]->url,
				'menu-item-status'   => 'publish',
				'menu-item-position' => $position,
			) );
		} else {
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'    => ucfirst( $title ),
				'menu-item-url'      => $url,
				'menu-item-status'   => 'publish',
				'menu-item-position' => $position,
			) );
			$added[] = ucfirst( $title );
		}
		$position++;
	}

	$msg = $added ? ( 'Añadidos: ' . implode( ', ', $added ) . '. ' ) : 'No había nada nuevo que añadir. ';
	wp_die( $msg . 'Menú reordenado: Inicio, Rutas, Galería, Comunidad, Foro, Blog. <a href="' . esc_url( home_url( '/' ) ) . '">Ver web</a>.' );
} );
