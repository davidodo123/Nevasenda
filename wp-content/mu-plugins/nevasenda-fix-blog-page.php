<?php
/**
 * Plugin Name: Nevasenda - Arreglar enlace del Blog
 * Description: Crea la página "Blog", la asigna como página de entradas (Ajustes > Lectura) y arregla el enlace del menú. Visita /wp-admin/?nevasenda_fix_blog=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['nevasenda_fix_blog'] ) ) {
		return;
	}

	$page = get_page_by_path( 'blog' );

	if ( ! $page ) {
		$page_id = wp_insert_post( array(
			'post_title'   => 'Blog',
			'post_name'    => 'blog',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		) );
		$page = get_post( $page_id );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_for_posts', $page->ID );

	$blog_url = get_permalink( $page->ID );

	// Arreglar el ítem "Blog" en el menú principal.
	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['primary'] ) ? $locations['primary'] : 0;

	$updated_menu = false;
	if ( $menu_id ) {
		$existing = wp_get_nav_menu_items( $menu_id );
		if ( $existing ) {
			foreach ( $existing as $item ) {
				if ( mb_strtolower( $item->title ) === 'blog' ) {
					wp_update_nav_menu_item( $menu_id, $item->ID, array(
						'menu-item-title'    => $item->title,
						'menu-item-url'      => $blog_url,
						'menu-item-status'   => 'publish',
						'menu-item-position' => $item->menu_order,
					) );
					$updated_menu = true;
				}
			}
		}
	}

	$msg = 'Página "Blog" lista en ' . esc_url( $blog_url ) . '. ';
	$msg .= $updated_menu ? 'Enlace del menú actualizado.' : 'No se encontró el ítem "Blog" en el menú, revísalo en Apariencia &gt; Menús.';

	wp_die( $msg . ' <a href="' . esc_url( $blog_url ) . '">Ver blog</a>' );
} );
