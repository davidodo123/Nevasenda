<?php
/**
 * Plugin Name: Nevasenda - Contenido demo
 * Description: Importa contenido de ejemplo (rutas + noticias) una sola vez. Visita /wp-admin/?nevasenda_import=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// /wp-admin/?nevasenda_reset=1 -> borra el contenido demo pa poder re-importar
	if ( isset( $_GET['nevasenda_reset'] ) ) {
		$ids = get_option( 'nevasenda_demo_post_ids', array() );
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
		delete_option( 'nevasenda_demo_post_ids' );
		delete_option( 'nevasenda_demo_imported' );
		wp_die( 'Contenido demo eliminado (' . count( $ids ) . ' elementos). Ahora visita <a href="' . esc_url( admin_url( '?nevasenda_import=1' ) ) . '">?nevasenda_import=1</a> pa volver a importar.' );
	}

	if ( ! isset( $_GET['nevasenda_import'] ) ) {
		return;
	}

	if ( get_option( 'nevasenda_demo_imported' ) ) {
		wp_die( 'El contenido demo ya fue importado anteriormente. Pa reimportar (ej. con fotos nuevas), visita primero <a href="' . esc_url( admin_url( '?nevasenda_reset=1' ) ) . '">?nevasenda_reset=1</a>.' );
	}

	$imported_ids = array();

	// --- Taxonomías ---
	$dificultades = array( 'Fácil', 'Media', 'Difícil' );
	foreach ( $dificultades as $d ) {
		if ( ! term_exists( $d, 'dificultad' ) ) {
			wp_insert_term( $d, 'dificultad' );
		}
	}

	$zonas = array( 'Picos de Europa', 'Sierra Nevada', 'Pirineos', 'Sierra de Gredos', 'Sierra de Francia' );
	foreach ( $zonas as $z ) {
		if ( ! term_exists( $z, 'zona' ) ) {
			wp_insert_term( $z, 'zona' );
		}
	}

	// --- Rutas demo ---
	$rutas = array(
		array(
			'title'      => 'Ruta del Cares',
			'content'    => 'La Ruta del Cares, conocida como la "Garganta Divina", recorre el desfiladero entre Caín y Poncebos en pleno corazón de Picos de Europa. Sendero excavado en la roca con vistas espectaculares al cañón, ideal pa iniciarse en rutas de montaña sin grandes desniveles.',
			'excerpt'    => 'La Garganta Divina: sendero tallado en roca entre Caín y Poncebos, con vistas al cañón del Cares.',
			'dificultad' => 'Fácil',
			'zona'       => 'Picos de Europa',
			'distancia'  => '12',
			'desnivel'   => '200',
			'duracion'   => '4h',
			'image'      => 'ruta-cares.jpg',
		),
		array(
			'title'      => 'Sendero de los Órganos',
			'content'    => 'Recorrido circular por formaciones rocosas singulares en la Sierra de Francia. Tramos de bosque, miradores y zonas de roca pulida que dan nombre a la ruta. Apta pa todos los niveles con algo de experiencia previa en montaña.',
			'excerpt'    => 'Recorrido circular entre formaciones rocosas y miradores de la Sierra de Francia.',
			'dificultad' => 'Media',
			'zona'       => 'Sierra de Francia',
			'distancia'  => '12',
			'desnivel'   => '600',
			'duracion'   => '4h',
			'image'      => 'ruta-organos.jpg',
		),
		array(
			'title'      => 'Vuelta al Circo de Gredos',
			'content'    => 'Clásica del Sistema Central: rodea las Lagunas Grande y de las Cinco Lagunas pasando bajo el Almanzor. Terreno de alta montaña, con tramos de pedrera y posibilidad de nieve hasta bien entrada la primavera.',
			'excerpt'    => 'Clásica de Gredos rodeando las lagunas de alta montaña bajo el Almanzor.',
			'dificultad' => 'Media',
			'zona'       => 'Sierra de Gredos',
			'distancia'  => '16',
			'desnivel'   => '900',
			'duracion'   => '6h',
			'image'      => 'ruta-gredos.jpg',
		),
		array(
			'title'      => 'Pico del Veleta',
			'content'    => 'Ascensión a una de las cumbres más altas de la península. Recorrido largo y exigente con fuerte desnivel acumulado, recomendado pa senderistas con experiencia y buena condición física. Mejor época: verano, evitando tormentas de tarde.',
			'excerpt'    => 'Ascensión exigente a una de las cumbres más altas de Sierra Nevada.',
			'dificultad' => 'Difícil',
			'zona'       => 'Sierra Nevada',
			'distancia'  => '18',
			'desnivel'   => '1500',
			'duracion'   => '8h',
			'image'      => 'ruta-veleta.jpg',
		),
		array(
			'title'      => 'Pico de Néouvielle',
			'content'    => 'Ascensión clásica del Pirineo francés, con paso por lagos de alta montaña y vistas a los tresmiles cercanos. Tramo final con algo de roca expuesta; se recomienda buen calzado y bastones.',
			'excerpt'    => 'Ascensión clásica al Néouvielle, entre lagos de alta montaña del Pirineo.',
			'dificultad' => 'Difícil',
			'zona'       => 'Pirineos',
			'distancia'  => '14',
			'desnivel'   => '1100',
			'duracion'   => '6h',
			'image'      => 'ruta-neouvielle.jpg',
		),
	);

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$upload_dir = wp_upload_dir();

	foreach ( $rutas as $r ) {
		$post_id = wp_insert_post( array(
			'post_title'   => $r['title'],
			'post_content' => $r['content'],
			'post_excerpt' => $r['excerpt'],
			'post_type'    => 'ruta',
			'post_status'  => 'publish',
		) );

		if ( ! $post_id ) {
			continue;
		}

		$imported_ids[] = $post_id;

		wp_set_object_terms( $post_id, $r['dificultad'], 'dificultad' );
		wp_set_object_terms( $post_id, $r['zona'], 'zona' );
		update_post_meta( $post_id, '_ruta_distancia', $r['distancia'] );
		update_post_meta( $post_id, '_ruta_desnivel', $r['desnivel'] );
		update_post_meta( $post_id, '_ruta_duracion', $r['duracion'] );

		// Imagen destacada (ya descargada en uploads/2026/06/)
		$filepath = $upload_dir['basedir'] . '/2026/06/' . $r['image'];
		if ( file_exists( $filepath ) ) {
			$filetype = wp_check_filetype( $r['image'], null );
			$attach_id = wp_insert_attachment( array(
				'guid'           => $upload_dir['baseurl'] . '/2026/06/' . $r['image'],
				'post_mime_type' => $filetype['type'],
				'post_title'     => $r['title'],
				'post_status'    => 'inherit',
			), $filepath, $post_id );

			if ( $attach_id ) {
				$attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				set_post_thumbnail( $post_id, $attach_id );
			}
		}
	}

	// --- Noticias / blog demo ---
	$noticias = array(
		array(
			'title'   => 'La nieve tardía deja paisajes espectaculares en los senderos de Picos de Europa',
			'content' => 'Las últimas nevadas de la temporada han dejado un manto blanco en las cotas altas de Picos de Europa, ofreciendo contrastes únicos pa quienes se animen a recorrer sus senderos en estas fechas. Se recomienda precaución y equipo adecuado en los tramos umbríos donde la nieve aún no se ha derretido.',
			'excerpt' => 'Las nevadas tardías regalan paisajes únicos en los senderos de alta montaña de Picos de Europa.',
		),
		array(
			'title'   => 'Nueva señalización en la red de senderos de Sierra Nevada',
			'content' => 'El parque natural ha renovado la señalización de varias rutas clásicas, incluyendo paneles informativos con perfiles de desnivel, tiempos estimados y puntos de agua. Una mejora que facilita la planificación pa senderistas de todos los niveles.',
			'excerpt' => 'Renovada la señalización de varias rutas clásicas de Sierra Nevada con paneles informativos.',
		),
		array(
			'title'   => 'Consejos de equipo pa rutas de alta montaña en verano',
			'content' => 'Con la llegada del buen tiempo aumentan las salidas a cotas altas. Repasamos el equipo básico recomendado: calzado de trekking con buena suela, protección solar, suficiente agua, capas de abrigo pa cambios de temperatura y un mapa o GPS siempre como respaldo.',
			'excerpt' => 'Repasamos el equipo básico recomendado pa salidas de senderismo en alta montaña durante el verano.',
		),
	);

	foreach ( $noticias as $n ) {
		$post_id = wp_insert_post( array(
			'post_title'   => $n['title'],
			'post_content' => $n['content'],
			'post_excerpt' => $n['excerpt'],
			'post_type'    => 'post',
			'post_status'  => 'publish',
		) );
		if ( $post_id ) {
			$imported_ids[] = $post_id;
		}
	}

	update_option( 'nevasenda_demo_post_ids', $imported_ids );
	update_option( 'nevasenda_demo_imported', 1 );

	wp_die( 'Contenido demo importado: ' . count( $rutas ) . ' rutas y ' . count( $noticias ) . ' noticias. <a href="' . esc_url( home_url( '/' ) ) . '">Ver web</a>' );
} );
