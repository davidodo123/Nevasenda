<?php
/**
 * Plugin Name: Nevasenda - Contenido del blog
 * Description: Importa 5 entradas adicionales pa la sección Blog (crónicas, equipo, estado de senderos, quedadas). Visita /wp-admin/?nevasenda_blog_import=1 logueado como admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// /wp-admin/?nevasenda_blog_reset=1 -> borra estas entradas pa poder re-importar
	if ( isset( $_GET['nevasenda_blog_reset'] ) ) {
		$ids = get_option( 'nevasenda_blog_post_ids', array() );
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
		delete_option( 'nevasenda_blog_post_ids' );
		delete_option( 'nevasenda_blog_imported' );
		wp_die( 'Entradas de blog eliminadas (' . count( $ids ) . '). Ahora visita <a href="' . esc_url( admin_url( '?nevasenda_blog_import=1' ) ) . '">?nevasenda_blog_import=1</a> pa volver a importar.' );
	}

	if ( ! isset( $_GET['nevasenda_blog_import'] ) ) {
		return;
	}

	if ( get_option( 'nevasenda_blog_imported' ) ) {
		wp_die( 'Las entradas de blog ya se importaron antes. Pa reimportar, visita primero <a href="' . esc_url( admin_url( '?nevasenda_blog_reset=1' ) ) . '">?nevasenda_blog_reset=1</a>.' );
	}

	$posts = array(
		array(
			'title'   => 'Apertura de refugios en los Pirineos: primeros partes de nieve en cotas altas',
			'content' => "Con la subida de los refugios de montaña a su horario de temporada, llegan también los primeros partes de nieve en los puertos por encima de los 2.400 metros. Los guardas de varios refugios del Pirineo aragonés y catalán reportan placas de nieve dura en orientación norte y neveros residuales en los pasos clásicos hacia los ibones de alta montaña.\n\nPa quienes planeen rutas de varios días, recomendamos confirmar el estado del sendero directamente con el refugio antes de salir, llevar bastones y, si hay duda, crampones ligeros pa los tramos de nevero. La previsión meteo pa las próximas semanas apunta a una mejora progresiva, pero las mañanas seguirán siendo frías en los collados.",
			'excerpt' => 'Los refugios pirenaicos abren temporada y reportan neveros en los pasos de alta montaña: consejos antes de salir.',
		),
		array(
			'title'   => 'Cómo elegir botas de trekking según el terreno: guía rápida',
			'content' => "No todas las botas valen pa todo. Pa senderos bien marcados y de un día, una bota baja o media con buena suela de agarre suele ser suficiente y aporta más ligereza. Pa rutas de alta montaña con pedrera, nieve o terreno irregular, conviene subir a una bota de caña alta con buen soporte de tobillo y suela rígida que reparta el peso de la mochila.\n\nOtros puntos a tener en cuenta: la membrana impermeable (útil en Picos de Europa o Pirineos, donde el tiempo cambia rápido), el peso total (cada gramo se nota en rutas largas) y, sobre todo, probarlas con los calcetines que vayas a usar de verdad antes de estrenarlas en una ruta larga. Una bota nueva sin rodaje previo es la receta perfecta pa una ampolla.",
			'excerpt' => 'Bota baja, media o alta: qué tener en cuenta según el tipo de ruta antes de comprar calzado de trekking.',
		),
		array(
			'title'   => 'Crónica: travesía de dos días por el Macizo Central de Picos de Europa',
			'content' => "Salimos de Fuente Dé temprano, con la idea de hacer noche en un refugio antes de seguir hacia los Lagos de Covadonga al día siguiente. El primer tramo, en subida constante hacia el Collado de Horcados Rojos, regala vistas que quitan el aliento sobre el Mirador del Cable y, en días claros, hasta el mar Cantábrico.\n\nLa segunda jornada fue más larga de lo previsto: el terreno kárstico de Picos exige ir con calma, leer bien las marcas y no fiarse de los \"atajos\". Llegamos a los lagos ya de tarde, con las piernas cansadas pero con la sensación de haber cruzado uno de los paisajes más bonitos de la península a pie. Si os animáis, calculad un día extra de margen por si el tiempo se complica.",
			'excerpt' => 'Dos días cruzando el Macizo Central de Picos de Europa: de Fuente Dé a los Lagos de Covadonga, paso a paso.',
		),
		array(
			'title'   => 'Estado de los senderos en Sierra Nevada este fin de semana',
			'content' => "Con el buen tiempo de los últimos días, los senderos de cota media en Sierra Nevada están en buenas condiciones, secos y bien transitables. En las zonas más altas, cercanas al Veleta y Mulhacén, todavía pueden encontrarse algunos neveros en barrancos orientados al norte, así que si la ruta pasa por ahí, llevad algo de tracción extra por si acaso.\n\nPa el fin de semana se espera viento moderado en las cumbres a partir del mediodía, por lo que recomendamos salir temprano y tener previsto un punto de retirada si el viento aprieta más de la cuenta. Como siempre, agua de sobra: en esta época las fuentes de cota alta pueden no estar activas todavía.",
			'excerpt' => 'Buenas condiciones en cota media y algún nevero residual en cota alta: lo que hay que saber antes de subir.',
		),
		array(
			'title'   => 'Quedadas de la comunidad este otoño: calendario y cómo apuntarte',
			'content' => "Desde la comunidad estamos organizando varias salidas conjuntas pa este otoño: una ruta circular de nivel fácil en Sierra de Francia, una travesía de un día en Sierra de Gredos pa nivel medio, y una jornada de \"limpieza de sendero\" en colaboración con voluntarios locales.\n\nSi te apetece apuntarte a alguna, el sitio pa coordinaros es el foro: cada salida tiene su propio hilo donde se confirma fecha, punto de encuentro y nivel exigido, y donde podéis preguntar dudas de última hora sobre el estado del camino o el material recomendado. Las plazas suelen ser limitadas, así que si te interesa, no lo dejes pa el último día.",
			'excerpt' => 'Rutas conjuntas en Sierra de Francia y Gredos, y jornada de limpieza de sendero: apúntate desde el foro.',
		),
	);

	$imported_ids = array();

	foreach ( $posts as $p ) {
		$post_id = wp_insert_post( array(
			'post_title'   => $p['title'],
			'post_content' => $p['content'],
			'post_excerpt' => $p['excerpt'],
			'post_type'    => 'post',
			'post_status'  => 'publish',
		) );
		if ( $post_id ) {
			$imported_ids[] = $post_id;
		}
	}

	update_option( 'nevasenda_blog_post_ids', $imported_ids );
	update_option( 'nevasenda_blog_imported', 1 );

	wp_die( 'Importadas ' . count( $imported_ids ) . ' entradas de blog. <a href="' . esc_url( home_url( '/' ) ) . '">Ver web</a>' );
} );
