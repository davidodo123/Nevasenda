<?php
/**
 * Plugin Name: Nevasenda - Activar foro
 * Description: Activa el plugin Asgaros Forum. Visita /wp-admin/?nevasenda_activar_foro=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['nevasenda_activar_foro'] ) ) {
		return;
	}

	$plugin = 'asgaros-forum/asgaros-forum.php';

	if ( is_plugin_active( $plugin ) ) {
		wp_die( 'Asgaros Forum ya está activo. <a href="' . esc_url( admin_url( 'admin.php?page=asgarosforum' ) ) . '">Configurarlo</a>.' );
	}

	$result = activate_plugin( $plugin );

	if ( is_wp_error( $result ) ) {
		wp_die( 'Error activando Asgaros Forum: ' . esc_html( $result->get_error_message() ) );
	}

	wp_die( 'Asgaros Forum activado. <a href="' . esc_url( admin_url( 'admin.php?page=asgarosforum' ) ) . '">Configurar foro</a> o <a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">ver plugins</a>.' );
} );
