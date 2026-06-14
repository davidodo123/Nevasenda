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
	$distancia = get_post_meta( $post->ID, '_ruta_distancia', true );
	$desnivel  = get_post_meta( $post->ID, '_ruta_desnivel', true );
	$duracion  = get_post_meta( $post->ID, '_ruta_duracion', true );
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

	foreach ( array( 'ruta_distancia', 'ruta_desnivel', 'ruta_duracion' ) as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
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
