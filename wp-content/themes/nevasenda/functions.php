<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NEVASENDA_VERSION', '1.0.0' );

/**
 * Theme setup
 */
function nevasenda_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus( array(
		'primary' => __( 'Menú principal', 'nevasenda' ),
		'footer'  => __( 'Menú pie de página', 'nevasenda' ),
	) );
}
add_action( 'after_setup_theme', 'nevasenda_setup' );

/**
 * Estilos y scripts
 */
function nevasenda_assets() {
	wp_enqueue_style( 'nevasenda-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;700&display=swap', array(), null );
	wp_enqueue_style( 'nevasenda-style', get_stylesheet_uri(), array( 'nevasenda-fonts' ), NEVASENDA_VERSION );
	wp_enqueue_script( 'nevasenda-nav', get_template_directory_uri() . '/assets/nav.js', array(), NEVASENDA_VERSION, true );
	wp_enqueue_script( 'nevasenda-animations', get_template_directory_uri() . '/assets/animations.js', array(), NEVASENDA_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'nevasenda_assets' );

/**
 * CPT Rutas + taxonomías Dificultad y Zona
 */
function nevasenda_register_rutas() {
	register_post_type( 'ruta', array(
		'labels' => array(
			'name'          => 'Rutas',
			'singular_name' => 'Ruta',
			'add_new_item'  => 'Añadir nueva ruta',
			'edit_item'     => 'Editar ruta',
			'all_items'     => 'Todas las rutas',
			'menu_name'     => 'Rutas',
		),
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'rutas' ),
		'menu_icon'    => 'dashicons-palmtree',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'show_in_rest' => true,
	) );

	register_taxonomy( 'dificultad', 'ruta', array(
		'labels'       => array( 'name' => 'Dificultad', 'singular_name' => 'Dificultad' ),
		'hierarchical' => true,
		'public'       => true,
		'rewrite'      => array( 'slug' => 'dificultad' ),
		'show_in_rest' => true,
	) );

	register_taxonomy( 'zona', 'ruta', array(
		'labels'       => array( 'name' => 'Zona', 'singular_name' => 'Zona' ),
		'hierarchical' => true,
		'public'       => true,
		'rewrite'      => array( 'slug' => 'zona' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'nevasenda_register_rutas' );

/**
 * Metabox: datos técnicos de la ruta (distancia, desnivel, duración)
 */
function nevasenda_ruta_metabox() {
	add_meta_box( 'nevasenda_ruta_datos', 'Datos técnicos', 'nevasenda_ruta_metabox_html', 'ruta', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'nevasenda_ruta_metabox' );

function nevasenda_ruta_metabox_html( $post ) {
	wp_nonce_field( 'nevasenda_ruta_save', 'nevasenda_ruta_nonce' );
	$distancia   = get_post_meta( $post->ID, '_ruta_distancia', true );
	$desnivel    = get_post_meta( $post->ID, '_ruta_desnivel', true );
	$duracion    = get_post_meta( $post->ID, '_ruta_duracion', true );
	$wikiloc     = get_post_meta( $post->ID, '_ruta_wikiloc', true );
	$requisitos  = get_post_meta( $post->ID, '_ruta_requisitos', true );
	$encuentro   = get_post_meta( $post->ID, '_ruta_punto_encuentro', true );
	$hora_salida = get_post_meta( $post->ID, '_ruta_hora_salida', true );
	?>
	<p>
		<label for="ruta_distancia">Distancia (km)</label><br>
		<input type="text" id="ruta_distancia" name="ruta_distancia" value="<?php echo esc_attr( $distancia ); ?>" class="widefat" />
	</p>
	<p>
		<label for="ruta_desnivel">Desnivel (m)</label><br>
		<input type="text" id="ruta_desnivel" name="ruta_desnivel" value="<?php echo esc_attr( $desnivel ); ?>" class="widefat" />
	</p>
	<p>
		<label for="ruta_duracion">Duración estimada</label><br>
		<input type="text" id="ruta_duracion" name="ruta_duracion" value="<?php echo esc_attr( $duracion ); ?>" class="widefat" placeholder="ej. 3h 30min" />
	</p>
	<p>
		<label for="ruta_punto_encuentro">Punto de encuentro</label><br>
		<input type="text" id="ruta_punto_encuentro" name="ruta_punto_encuentro" value="<?php echo esc_attr( $encuentro ); ?>" class="widefat" placeholder="ej. Aparcamiento de Pradollano" />
	</p>
	<p>
		<label for="ruta_hora_salida">Hora de salida</label><br>
		<input type="text" id="ruta_hora_salida" name="ruta_hora_salida" value="<?php echo esc_attr( $hora_salida ); ?>" class="widefat" placeholder="ej. 8:00" />
	</p>
	<p>
		<label for="ruta_wikiloc">Enlace a Wikiloc</label><br>
		<input type="url" id="ruta_wikiloc" name="ruta_wikiloc" value="<?php echo esc_attr( $wikiloc ); ?>" class="widefat" placeholder="https://es.wikiloc.com/rutas-senderismo/..." />
	</p>
	<p>
		<label for="ruta_requisitos">Requisitos / material recomendado</label><br>
		<textarea id="ruta_requisitos" name="ruta_requisitos" class="widefat" rows="5" placeholder="Un punto por línea, ej.&#10;Botas de montaña&#10;Bastones&#10;Crampones si hay nieve"><?php echo esc_textarea( $requisitos ); ?></textarea>
	</p>
	<?php
}

function nevasenda_save_ruta_meta( $post_id ) {
	if ( ! isset( $_POST['nevasenda_ruta_nonce'] ) || ! wp_verify_nonce( $_POST['nevasenda_ruta_nonce'], 'nevasenda_ruta_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( 'ruta_distancia', 'ruta_desnivel', 'ruta_duracion', 'ruta_punto_encuentro', 'ruta_hora_salida' ) as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
	if ( isset( $_POST['ruta_wikiloc'] ) ) {
		update_post_meta( $post_id, '_ruta_wikiloc', sanitize_url( wp_unslash( $_POST['ruta_wikiloc'] ) ) );
	}
	if ( isset( $_POST['ruta_requisitos'] ) ) {
		update_post_meta( $post_id, '_ruta_requisitos', sanitize_textarea_field( wp_unslash( $_POST['ruta_requisitos'] ) ) );
	}
}
add_action( 'save_post_ruta', 'nevasenda_save_ruta_meta' );

/**
 * Helper: leer dato técnico de una ruta
 */
function nevasenda_ruta_meta( $post_id, $key ) {
	return get_post_meta( $post_id, '_ruta_' . $key, true );
}

/**
 * Tiempo de lectura estimado de una entrada, en minutos.
 */
function nevasenda_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Iconos SVG inline pa las tarjetas de ruta (distancia, desnivel, duración, zona, flecha, check).
 */
function nevasenda_icon( $name ) {
	$icons = array(
		'ruler'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H3V8h2v2h2V8h2v4h2V8h2v2h2V8h2v4h2V8h2v8z"/></svg>',
		'terrain' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 6l-3.75 5 2.85 3.8-1.6 1.2C9.81 13.75 7 10 7 10l-6 8h22L14 6z"/></svg>',
		'clock'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>',
		'pin'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
		'arrow'   => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
		'check'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
	);
	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Menú de respaldo si no hay un menú asignado en Apariencia > Menús,
 * pa que el nav nunca se vea vacío.
 */
function nevasenda_fallback_menu() {
	$blog_link = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );

	$links = array(
		home_url( '/' )                      => 'Inicio',
		get_post_type_archive_link( 'ruta' )  => 'Rutas',
		$blog_link                           => 'Blog',
	);

	echo '<ul class="primary-menu">';
	foreach ( $links as $url => $label ) {
		if ( ! $url ) {
			continue;
		}
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}
