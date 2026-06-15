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

	if ( is_singular( 'ruta' ) && nevasenda_ruta_etapas( get_queried_object_id() ) ) {
		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
		wp_enqueue_script( 'leaflet-gpx', 'https://unpkg.com/leaflet-gpx@2.1.2/gpx.js', array( 'leaflet' ), '2.1.2', true );
		wp_add_inline_script( 'leaflet-gpx', 'document.addEventListener("DOMContentLoaded",function(){var el=document.getElementById("ruta-leaflet-map");if(!el||typeof L==="undefined")return;var imgBase="https://unpkg.com/leaflet@1.9.4/dist/images/";L.Icon.Default.imagePath=imgBase;var map=L.map(el);L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{maxZoom:18,attribution:"&copy; OpenStreetMap contributors"}).addTo(map);var markerOpts={startIconUrl:imgBase+"marker-icon.png",endIconUrl:imgBase+"marker-icon.png",shadowUrl:imgBase+"marker-shadow.png",wptIconUrls:{"":imgBase+"marker-icon.png"},iconSize:[25,41],shadowSize:[41,41],iconAnchor:[12,41],shadowAnchor:[12,41],popupAnchor:[1,-34]};var etapas=JSON.parse(el.dataset.etapas||"[]");var bounds=null;var pending=etapas.length;etapas.forEach(function(etapa){new L.GPX(etapa.gpx,{async:true,marker_options:markerOpts,polyline_options:{color:etapa.color,weight:4,opacity:.85}}).on("loaded",function(e){bounds=bounds?bounds.extend(e.target.getBounds()):e.target.getBounds();pending--;if(pending===0&&bounds)map.fitBounds(bounds);}).addTo(map);});});' );
	}
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
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
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
 * CPT Galería: fotos gestionadas desde wp-admin (título + imagen destacada).
 */
function nevasenda_register_galeria() {
	register_post_type( 'foto_galeria', array(
		'labels' => array(
			'name'          => 'Galería',
			'singular_name' => 'Foto',
			'add_new_item'  => 'Añadir nueva foto',
			'edit_item'     => 'Editar foto',
			'all_items'     => 'Galería',
			'menu_name'     => 'Galería',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-format-image',
		'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'nevasenda_register_galeria' );

/**
 * Fotos publicadas de la galería, ordenadas por "Orden" (Atributos de página) y fecha.
 * Devuelve solo las que tienen imagen destacada.
 */
function nevasenda_galeria_fotos( $limit = -1 ) {
	$query = new WP_Query( array(
		'post_type'      => 'foto_galeria',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	) );

	$fotos = array();
	foreach ( $query->posts as $post ) {
		if ( ! has_post_thumbnail( $post ) ) {
			continue;
		}
		$thumb_id = get_post_thumbnail_id( $post );
		$fotos[]  = array(
			'thumb' => wp_get_attachment_image_url( $thumb_id, 'medium_large' ),
			'full'  => wp_get_attachment_image_url( $thumb_id, 'full' ),
			'alt'   => get_the_title( $post ),
		);
	}
	wp_reset_postdata();

	return $fotos;
}

/**
 * CPT Testimonios: opiniones sobre la web gestionadas desde wp-admin
 * (título = nombre, contenido = texto, imagen destacada = avatar).
 */
function nevasenda_register_testimonios() {
	register_post_type( 'testimonio', array(
		'labels' => array(
			'name'          => 'Testimonios',
			'singular_name' => 'Testimonio',
			'add_new_item'  => 'Añadir nuevo testimonio',
			'edit_item'     => 'Editar testimonio',
			'all_items'     => 'Testimonios',
			'menu_name'     => 'Testimonios',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-star-filled',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'nevasenda_register_testimonios' );

/**
 * Testimonios publicados, ordenados por "Orden" (Atributos de página) y fecha.
 */
function nevasenda_testimonios( $limit = -1 ) {
	$query = new WP_Query( array(
		'post_type'      => 'testimonio',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	) );

	$items = array();
	foreach ( $query->posts as $post ) {
		$items[] = array(
			'nombre' => get_the_title( $post ),
			'rol'    => get_post_meta( $post->ID, '_testimonio_rol', true ),
			'texto'  => $post->post_content,
			'rating' => (int) get_post_meta( $post->ID, '_testimonio_rating', true ),
			'avatar' => has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'thumbnail' ) : '',
		);
	}
	wp_reset_postdata();

	return $items;
}

/**
 * Metabox: rol/descripción y valoración del testimonio.
 */
function nevasenda_testimonio_metabox() {
	add_meta_box( 'nevasenda_testimonio_datos', 'Datos del testimonio', 'nevasenda_testimonio_metabox_html', 'testimonio', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'nevasenda_testimonio_metabox' );

function nevasenda_testimonio_metabox_html( $post ) {
	wp_nonce_field( 'nevasenda_testimonio_save', 'nevasenda_testimonio_nonce' );
	$rol    = get_post_meta( $post->ID, '_testimonio_rol', true );
	$rating = (int) get_post_meta( $post->ID, '_testimonio_rating', true );
	if ( ! $rating ) {
		$rating = 5;
	}
	?>
	<p>
		<label for="testimonio_rol">Rol / descripción</label><br>
		<input type="text" id="testimonio_rol" name="testimonio_rol" value="<?php echo esc_attr( $rol ); ?>" class="widefat" placeholder="Senderista, Sierra Nevada" />
	</p>
	<p>
		<label for="testimonio_rating">Valoración</label><br>
		<select id="testimonio_rating" name="testimonio_rating" class="widefat">
			<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $rating, $i ); ?>><?php echo esc_html( $i ); ?> estrellas</option>
			<?php endfor; ?>
		</select>
	</p>
	<p class="description">El título es el nombre de la persona. El contenido (editor) es el texto de la opinión. La imagen destacada es el avatar (opcional).</p>
	<?php
}

function nevasenda_save_testimonio_meta( $post_id ) {
	if ( ! isset( $_POST['nevasenda_testimonio_nonce'] ) || ! wp_verify_nonce( $_POST['nevasenda_testimonio_nonce'], 'nevasenda_testimonio_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['testimonio_rol'] ) ) {
		update_post_meta( $post_id, '_testimonio_rol', sanitize_text_field( wp_unslash( $_POST['testimonio_rol'] ) ) );
	}
	if ( isset( $_POST['testimonio_rating'] ) ) {
		update_post_meta( $post_id, '_testimonio_rating', max( 1, min( 5, (int) $_POST['testimonio_rating'] ) ) );
	}
}
add_action( 'save_post_testimonio', 'nevasenda_save_testimonio_meta' );

/**
 * Permitir subir archivos .gpx (track de la ruta) a la mediateca.
 */
function nevasenda_allow_gpx_upload( $mimes ) {
	$mimes['gpx'] = 'application/gpx+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'nevasenda_allow_gpx_upload' );

/**
 * WP comprueba el tipo real del archivo (finfo) además de la extensión; los
 * .gpx se detectan como application/xml y no coinciden con application/gpx+xml,
 * así que sin esto la subida se rechaza ("tipo de archivo no permitido").
 */
function nevasenda_fix_gpx_filetype( $data, $file, $filename, $mimes ) {
	if ( empty( $data['ext'] ) && empty( $data['type'] ) ) {
		$filetype = wp_check_filetype( $filename, $mimes );
		if ( 'gpx' === $filetype['ext'] ) {
			$data['ext']  = 'gpx';
			$data['type'] = 'application/gpx+xml';
		}
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'nevasenda_fix_gpx_filetype', 10, 4 );

/**
 * Metabox: datos técnicos de la ruta (distancia, desnivel, duración)
 */
function nevasenda_ruta_metabox() {
	add_meta_box( 'nevasenda_ruta_datos', 'Datos técnicos', 'nevasenda_ruta_metabox_html', 'ruta', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'nevasenda_ruta_metabox' );

function nevasenda_ruta_editor_assets( $hook ) {
	if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'ruta' === get_post_type() ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'nevasenda_ruta_editor_assets' );

function nevasenda_ruta_metabox_html( $post ) {
	wp_nonce_field( 'nevasenda_ruta_save', 'nevasenda_ruta_nonce' );
	$distancia   = get_post_meta( $post->ID, '_ruta_distancia', true );
	$desnivel    = get_post_meta( $post->ID, '_ruta_desnivel', true );
	$duracion    = get_post_meta( $post->ID, '_ruta_duracion', true );
	$wikiloc     = get_post_meta( $post->ID, '_ruta_wikiloc', true );
	$requisitos  = get_post_meta( $post->ID, '_ruta_requisitos', true );
	$encuentro   = get_post_meta( $post->ID, '_ruta_punto_encuentro', true );
	$hora_salida = get_post_meta( $post->ID, '_ruta_hora_salida', true );
	$etapas      = get_post_meta( $post->ID, '_ruta_etapas', true );
	if ( ! is_array( $etapas ) ) {
		$etapas = array();
	}
	// Migración: si había un único track GPX guardado con el sistema antiguo, conviértelo en "Etapa 1".
	if ( ! $etapas ) {
		$gpx_legacy = get_post_meta( $post->ID, '_ruta_gpx', true );
		if ( $gpx_legacy && preg_match( '/\.gpx$/i', $gpx_legacy ) ) {
			$etapas = array( array( 'nombre' => 'Etapa 1', 'gpx' => $gpx_legacy ) );
		}
	}
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
		<label>Etapas de la ruta (track GPX por etapa)</label>
		<div id="ruta-etapas-wrap">
			<?php foreach ( $etapas as $i => $etapa ) : ?>
				<div class="ruta-etapa-row">
					<input type="text" name="ruta_etapas[<?php echo esc_attr( $i ); ?>][nombre]" value="<?php echo esc_attr( $etapa['nombre'] ); ?>" placeholder="Nombre de la etapa (ej. Etapa 1: Refugio - Cumbre)" class="widefat" />
					<input type="hidden" class="ruta-etapa-gpx" name="ruta_etapas[<?php echo esc_attr( $i ); ?>][gpx]" value="<?php echo esc_attr( $etapa['gpx'] ); ?>" />
					<span class="ruta-etapa-filename"><?php echo $etapa['gpx'] ? esc_html( basename( $etapa['gpx'] ) ) : 'Sin archivo'; ?></span>
					<button type="button" class="button ruta-etapa-select">Seleccionar GPX</button>
					<button type="button" class="button ruta-etapa-remove">Eliminar etapa</button>
				</div>
			<?php endforeach; ?>
		</div>
		<button type="button" id="ruta-etapa-add" class="button">+ Añadir etapa</button>
	</p>
	<style>
		.ruta-etapa-row { padding: 10px 0; border-bottom: 1px solid #ddd; margin-bottom: 8px; }
		.ruta-etapa-row input[type="text"] { margin-bottom: 6px; }
		.ruta-etapa-filename { display: inline-block; margin-right: 8px; font-size: 12px; color: #666; }
	</style>
	<script>
	( function() {
		var wrap   = document.getElementById( 'ruta-etapas-wrap' );
		var addBtn = document.getElementById( 'ruta-etapa-add' );
		if ( ! wrap || ! addBtn ) {
			return;
		}
		var index = wrap.querySelectorAll( '.ruta-etapa-row' ).length;

		function bindRow( row ) {
			var selectBtn = row.querySelector( '.ruta-etapa-select' );
			var removeBtn = row.querySelector( '.ruta-etapa-remove' );
			var input     = row.querySelector( '.ruta-etapa-gpx' );
			var filename  = row.querySelector( '.ruta-etapa-filename' );
			var frame;
			selectBtn.addEventListener( 'click', function() {
				if ( frame ) {
					frame.open();
					return;
				}
				frame = wp.media( {
					title: 'Seleccionar archivo GPX',
					button: { text: 'Usar este archivo' },
					library: { type: '' },
					multiple: false
				} );
				frame.on( 'select', function() {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					input.value = attachment.url;
					filename.textContent = attachment.filename || attachment.url;
				} );
				frame.open();
			} );
			removeBtn.addEventListener( 'click', function() {
				row.remove();
			} );
		}

		Array.prototype.forEach.call( wrap.querySelectorAll( '.ruta-etapa-row' ), bindRow );

		addBtn.addEventListener( 'click', function() {
			var row = document.createElement( 'div' );
			row.className = 'ruta-etapa-row';
			row.innerHTML =
				'<input type="text" name="ruta_etapas[' + index + '][nombre]" placeholder="Nombre de la etapa (ej. Etapa 1: Refugio - Cumbre)" class="widefat" />' +
				'<input type="hidden" class="ruta-etapa-gpx" name="ruta_etapas[' + index + '][gpx]" value="" />' +
				'<span class="ruta-etapa-filename">Sin archivo</span> ' +
				'<button type="button" class="button ruta-etapa-select">Seleccionar GPX</button> ' +
				'<button type="button" class="button ruta-etapa-remove">Eliminar etapa</button>';
			wrap.appendChild( row );
			bindRow( row );
			index++;
		} );
	} )();
	</script>
	<p>
		<label for="ruta_requisitos">Requisitos / material recomendado</label><br>
		<textarea id="ruta_requisitos" name="ruta_requisitos" class="widefat" rows="5" placeholder="Un punto por línea, ej.&#10;Botas de montaña&#10;Bastones&#10;Crampones si hay nieve"><?php echo esc_textarea( $requisitos ); ?></textarea>
	</p>
	<p>
		<button type="button" id="nevasenda-random-fill" class="button">Rellenar con datos de ejemplo</button>
	</p>
	<script>
	( function() {
		var btn = document.getElementById( 'nevasenda-random-fill' );
		if ( ! btn ) {
			return;
		}
		var encuentros = [
			'Aparcamiento de Pradollano',
			'Plaza Mayor del pueblo',
			'Área recreativa del río',
			'Refugio base',
			'Ermita de la Virgen',
			'Estación de autobuses'
		];
		var horas = [ '7:00', '7:30', '8:00', '8:30', '9:00', '9:30' ];
		var requisitos = [
			'Botas de montaña',
			'Bastones de trekking',
			'Mochila de 20-30L',
			'Agua (mínimo 2L)',
			'Ropa de abrigo por capas',
			'Chubasquero o cortavientos',
			'Gorra y crema solar',
			'Frontal o linterna',
			'Mapa o GPS',
			'Botiquín básico',
			'Crampones si hay nieve',
			'Comida y snacks energéticos'
		];
		btn.addEventListener( 'click', function() {
			document.getElementById( 'ruta_punto_encuentro' ).value = encuentros[ Math.floor( Math.random() * encuentros.length ) ];
			document.getElementById( 'ruta_hora_salida' ).value = horas[ Math.floor( Math.random() * horas.length ) ];

			var shuffled = requisitos.slice().sort( function() { return Math.random() - 0.5; } );
			var n = 4 + Math.floor( Math.random() * 3 );
			document.getElementById( 'ruta_requisitos' ).value = shuffled.slice( 0, n ).join( '\n' );
		} );
	} )();
	</script>
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
	if ( isset( $_POST['ruta_etapas'] ) && is_array( $_POST['ruta_etapas'] ) ) {
		$etapas = array();
		foreach ( $_POST['ruta_etapas'] as $etapa ) {
			$gpx = isset( $etapa['gpx'] ) ? sanitize_url( wp_unslash( $etapa['gpx'] ) ) : '';
			if ( ! $gpx ) {
				continue;
			}
			$etapas[] = array(
				'nombre' => isset( $etapa['nombre'] ) ? sanitize_text_field( wp_unslash( $etapa['nombre'] ) ) : '',
				'gpx'    => $gpx,
			);
		}
		update_post_meta( $post_id, '_ruta_etapas', $etapas );
	} else {
		delete_post_meta( $post_id, '_ruta_etapas' );
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
 * Etapas (tramos) de una ruta, cada una con su nombre y su track GPX.
 * Devuelve solo las etapas que tienen un GPX asociado.
 */
function nevasenda_ruta_etapas( $post_id ) {
	$etapas = get_post_meta( $post_id, '_ruta_etapas', true );
	if ( ! is_array( $etapas ) ) {
		return array();
	}
	return array_values(
		array_filter(
			$etapas,
			function ( $etapa ) {
				return ! empty( $etapa['gpx'] );
			}
		)
	);
}

/**
 * Colores asignados por orden a las etapas de una ruta en el mapa.
 */
function nevasenda_etapa_colors() {
	return array( '#1565c0', '#f0a93c', '#2e7d32', '#c62828', '#6a1b9a', '#00838f', '#ad1457', '#5d4037' );
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
		'users'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',
		'camera'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3L7.17 5H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2h-3.17L15 3H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>',
		'star'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>',
	);
	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Pinta la barra de estadísticas de una ruta (distancia, desnivel, duración,
 * punto de encuentro, hora de salida). $items es un array de [ icono, valor, etiqueta ].
 */
function nevasenda_render_ruta_stats( $items, $extra_class = '' ) {
	if ( ! $items ) {
		return;
	}
	?>
	<div class="ruta-stats <?php echo esc_attr( $extra_class ); ?>">
		<?php foreach ( $items as $item ) : ?>
			<div class="ruta-stat">
				<?php echo nevasenda_icon( $item[0] ); ?>
				<div>
					<strong><?php echo $item[1]; ?></strong>
					<span><?php echo $item[2]; ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Pinta $rating (0-5) como fila de estrellas (icono "star" lleno/vacío).
 */
function nevasenda_render_stars( $rating, $max = 5 ) {
	$rating = (int) round( $rating );
	$html   = '<span class="stars" aria-label="' . esc_attr( $rating . ' de ' . $max . ' estrellas' ) . '">';
	for ( $i = 1; $i <= $max; $i++ ) {
		$class = ( $i <= $rating ) ? 'star-icon is-filled' : 'star-icon';
		$html .= '<span class="' . $class . '">' . nevasenda_icon( 'star' ) . '</span>';
	}
	$html .= '</span>';
	return $html;
}

/**
 * Valoración media (1-5) y nº de opiniones de una ruta, a partir de los
 * comentarios aprobados que llevan meta "rating".
 */
function nevasenda_ruta_rating_stats( $post_id ) {
	$comments = get_comments( array(
		'post_id' => $post_id,
		'status'  => 'approve',
	) );

	$total = 0;
	$count = 0;
	foreach ( $comments as $comment ) {
		$rating = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
		if ( $rating < 1 || $rating > 5 ) {
			continue;
		}
		$total += $rating;
		$count++;
	}

	return array(
		'avg'   => $count ? round( $total / $count, 1 ) : 0,
		'count' => $count,
	);
}

/**
 * Guarda la valoración (1-5) enviada junto a un comentario de ruta como meta "rating".
 */
function nevasenda_save_comment_rating( $comment_id ) {
	if ( ! isset( $_POST['rating'] ) ) {
		return;
	}
	$comment = get_comment( $comment_id );
	if ( ! $comment || 'ruta' !== get_post_type( $comment->comment_post_ID ) ) {
		return;
	}
	$rating = (int) $_POST['rating'];
	if ( $rating >= 1 && $rating <= 5 ) {
		update_comment_meta( $comment_id, 'rating', $rating );
	}
}
add_action( 'comment_post', 'nevasenda_save_comment_rating' );

/**
 * Las opiniones de una ruta solo están abiertas pa usuarios registrados
 * (aunque la ruta se creara antes de añadir soporte de comentarios al CPT,
 * cuyo "comment_status" quedó "closed").
 */
add_filter( 'comments_open', function ( $open, $post_id ) {
	return 'ruta' === get_post_type( $post_id ) ? is_user_logged_in() : $open;
}, 10, 2 );

/**
 * Campo de valoración (1-5 estrellas) + comentario pa el formulario de opiniones de una ruta.
 */
function nevasenda_rating_field_html() {
	$html = '<p class="comment-form-rating"><label>Tu valoración</label><div class="rating-input">';
	for ( $i = 5; $i >= 1; $i-- ) {
		$checked = ( 5 === $i ) ? ' checked' : '';
		$html   .= '<input type="radio" id="rating-' . $i . '" name="rating" value="' . $i . '"' . $checked . '><label for="rating-' . $i . '">' . nevasenda_icon( 'star' ) . '</label>';
	}
	$html .= '</div></p><p class="comment-form-comment"><label for="comment">Tu opinión</label><textarea id="comment" name="comment" cols="45" rows="4" required></textarea></p>';
	return $html;
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
	echo nevasenda_auth_menu_item();
	echo '</ul>';
}

/**
 * Login y registro
 */

// Cualquiera puede registrarse (rol "Suscriptor"), sin tocar Ajustes > Generales.
add_filter( 'pre_option_users_can_register', '__return_true' );

/**
 * Enlace de "Iniciar sesión" / "Cerrar sesión" pa el menú principal.
 */
function nevasenda_auth_menu_item() {
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		return '<li class="menu-item-auth"><a href="' . esc_url( wp_logout_url( home_url( '/' ) ) ) . '">Cerrar sesión (' . esc_html( $user->display_name ) . ')</a></li>';
	}
	return '<li class="menu-item-auth"><a href="' . esc_url( home_url( '/cuenta/' ) ) . '">Iniciar sesión</a></li>';
}
add_filter( 'wp_nav_menu_items', function ( $items, $args ) {
	return 'primary' === $args->theme_location ? $items . nevasenda_auth_menu_item() : $items;
}, 10, 2 );

/**
 * Procesa el registro desde la página "Mi cuenta" (rol "Suscriptor").
 */
function nevasenda_handle_register() {
	$cuenta_url = home_url( '/cuenta/' );

	if ( ! isset( $_POST['nevasenda_register_nonce'] ) || ! wp_verify_nonce( $_POST['nevasenda_register_nonce'], 'nevasenda_register' ) ) {
		wp_safe_redirect( $cuenta_url );
		exit;
	}

	$nombre   = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
	$email    = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$password = (string) ( $_POST['password'] ?? '' );

	$error = '';
	if ( ! $nombre ) {
		$error = 'Indica tu nombre.';
	} elseif ( ! is_email( $email ) ) {
		$error = 'Ese email no es válido.';
	} elseif ( email_exists( $email ) ) {
		$error = 'Ya hay una cuenta registrada con ese email.';
	} elseif ( strlen( $password ) < 6 ) {
		$error = 'La contraseña debe tener al menos 6 caracteres.';
	}

	if ( $error ) {
		wp_safe_redirect( add_query_arg( array( 'panel' => 'register', 'error' => rawurlencode( $error ) ), $cuenta_url ) );
		exit;
	}

	$username = sanitize_user( current( explode( '@', $email ) ) . wp_generate_password( 4, false ), true );
	$user_id  = wp_insert_user( array(
		'user_login'   => $username,
		'user_email'   => $email,
		'user_pass'    => $password,
		'display_name' => $nombre,
		'nickname'     => $nombre,
		'role'         => 'subscriber',
	) );

	if ( is_wp_error( $user_id ) ) {
		wp_safe_redirect( add_query_arg( array( 'panel' => 'register', 'error' => rawurlencode( $user_id->get_error_message() ) ), $cuenta_url ) );
		exit;
	}

	wp_set_auth_cookie( $user_id, true );
	wp_safe_redirect( ! empty( $_POST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), home_url( '/' ) ) : home_url( '/' ) );
	exit;
}
add_action( 'admin_post_nopriv_nevasenda_register', 'nevasenda_handle_register' );
add_action( 'admin_post_nevasenda_register', 'nevasenda_handle_register' );

/**
 * Procesa el inicio de sesión desde la página "Mi cuenta".
 */
function nevasenda_handle_login() {
	$cuenta_url = home_url( '/cuenta/' );

	if ( ! isset( $_POST['nevasenda_login_nonce'] ) || ! wp_verify_nonce( $_POST['nevasenda_login_nonce'], 'nevasenda_login' ) ) {
		wp_safe_redirect( $cuenta_url );
		exit;
	}

	$user = wp_signon( array(
		'user_login'    => sanitize_text_field( wp_unslash( $_POST['email'] ?? '' ) ),
		'user_password' => (string) ( $_POST['password'] ?? '' ),
		'remember'      => true,
	) );

	if ( is_wp_error( $user ) ) {
		wp_safe_redirect( add_query_arg( array( 'panel' => 'login', 'error' => rawurlencode( 'Email o contraseña incorrectos.' ) ), $cuenta_url ) );
		exit;
	}

	wp_safe_redirect( ! empty( $_POST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), home_url( '/' ) ) : home_url( '/' ) );
	exit;
}
add_action( 'admin_post_nopriv_nevasenda_login', 'nevasenda_handle_login' );
add_action( 'admin_post_nevasenda_login', 'nevasenda_handle_login' );
