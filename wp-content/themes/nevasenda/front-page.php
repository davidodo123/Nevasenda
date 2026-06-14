<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

// Datos pa la banda de estadísticas del hero
$rutas_count_obj = wp_count_posts( 'ruta' );
$rutas_count     = isset( $rutas_count_obj->publish ) ? (int) $rutas_count_obj->publish : 0;

$km_total   = 0;
$todas_rutas = new WP_Query( array(
	'post_type'      => 'ruta',
	'posts_per_page' => -1,
	'fields'         => 'ids',
) );
if ( $todas_rutas->have_posts() ) {
	foreach ( $todas_rutas->posts as $ruta_id ) {
		$km_total += (float) nevasenda_ruta_meta( $ruta_id, 'distancia' );
	}
}
wp_reset_postdata();
?>

<section class="hero">
	<img class="hero-bg-img" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/hero-bg.jpg' ); ?>" alt="">
	<div class="container">
		<h1>Senderismo sin límites, <span>vive la montaña</span></h1>
		<p>Descubre rutas, consejos y reportajes pa explorar la naturaleza a tu ritmo. Comunidad de senderismo en blanco, negro y azul.</p>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'ruta' ) ); ?>" class="btn">Ver rutas</a>

		<div class="hero-stats">
			<div class="hero-stat">
				<strong data-counter data-target="<?php echo esc_attr( max( $rutas_count, 5 ) ); ?>">0</strong>
				<span>Rutas publicadas</span>
			</div>
			<div class="hero-stat">
				<strong data-counter data-target="<?php echo esc_attr( max( (int) round( $km_total ), 80 ) ); ?>">0</strong>
				<span>Km mapeados</span>
			</div>
			<div class="hero-stat">
				<strong data-counter data-target="850">0</strong>
				<span>Senderistas en la comunidad</span>
			</div>
			<div class="hero-stat">
				<strong data-counter data-target="5">0</strong>
				<span>Zonas de montaña</span>
			</div>
		</div>
	</div>
	<a href="#rutas-destacadas" class="hero-scroll" aria-label="Bajar a contenido"><span></span></a>
</section>

<div class="marquee">
	<div class="marquee-track">
		<span>Picos de Europa</span>
		<span>Pirineos</span>
		<span>Sierra Nevada</span>
		<span>Sierra de Gredos</span>
		<span>Sierra de Francia</span>
		<span>Picos de Europa</span>
		<span>Pirineos</span>
		<span>Sierra Nevada</span>
		<span>Sierra de Gredos</span>
		<span>Sierra de Francia</span>
	</div>
</div>

<section class="section" id="rutas-destacadas">
	<div class="container">
		<h2 class="section-title">Rutas destacadas</h2>
		<p class="section-subtitle">Las últimas rutas añadidas a nuestro catálogo</p>

		<div class="cards-grid">
			<?php
			$rutas = new WP_Query( array(
				'post_type'      => 'ruta',
				'posts_per_page' => 3,
			) );

			if ( $rutas->have_posts() ) :
				while ( $rutas->have_posts() ) :
					$rutas->the_post();
					$distancia  = nevasenda_ruta_meta( get_the_ID(), 'distancia' );
					$desnivel   = nevasenda_ruta_meta( get_the_ID(), 'desnivel' );
					$duracion   = nevasenda_ruta_meta( get_the_ID(), 'duracion' );
					$dificultad = get_the_terms( get_the_ID(), 'dificultad' );
					?>
					<article class="card">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="card-img">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
								<?php if ( $desnivel || $duracion ) : ?>
									<div class="card-overlay">
										<?php if ( $desnivel ) : ?>
											<span><strong><?php echo esc_html( $desnivel ); ?> m</strong>Desnivel</span>
										<?php endif; ?>
										<?php if ( $duracion ) : ?>
											<span><strong><?php echo esc_html( $duracion ); ?></strong>Duración</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<div class="card-body">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<div class="card-meta">
								<?php if ( $distancia ) : ?><span class="badge"><?php echo esc_html( $distancia ); ?> km</span><?php endif; ?>
								<?php if ( ! empty( $dificultad ) && ! is_wp_error( $dificultad ) ) : ?>
									<span class="badge badge-blue"><?php echo esc_html( $dificultad[0]->name ); ?></span>
								<?php endif; ?>
							</div>
							<div class="card-excerpt"><?php the_excerpt(); ?></div>
							<a class="card-link" href="<?php the_permalink(); ?>">Ver ruta &rarr;</a>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<p>Todavía no hay rutas publicadas.</p>
				<?php
			endif;
			?>
		</div>
	</div>
</section>

<section class="section section-alt scrolly">
	<div class="container scrolly-grid">
		<div class="scrolly-media">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/scrolly.jpg' ); ?>" alt="Senderista contemplando un valle de montaña" loading="lazy">
		</div>
		<div class="scrolly-items">
			<div class="scrolly-item">
				<span class="scrolly-num">01</span>
				<h3>Rutas para todos los niveles</h3>
				<p>Filtra por dificultad (fácil, media o difícil) y zona pa encontrar la salida que mejor se adapta a tu forma física y a tu grupo.</p>
			</div>
			<div class="scrolly-item">
				<span class="scrolly-num">02</span>
				<h3>Datos técnicos reales</h3>
				<p>Cada ficha incluye distancia, desnivel acumulado y duración estimada, pa que sepas exactamente a qué te enfrentas antes de salir.</p>
			</div>
			<div class="scrolly-item">
				<span class="scrolly-num">03</span>
				<h3>Comunidad activa</h3>
				<p>Comparte tu experiencia, haz preguntas sobre el estado de un sendero y descubre recomendaciones de otros senderistas.</p>
			</div>
			<div class="scrolly-item">
				<span class="scrolly-num">04</span>
				<h3>Fotografía de cada ruta</h3>
				<p>Paisajes reales de cada recorrido, pa que veas con qué te vas a encontrar antes de calzarte las botas.</p>
			</div>
		</div>
	</div>
</section>

<section class="photo-section photo-section--left">
	<img class="photo-section-bg" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/section-1.jpg' ); ?>" alt="">
	<div class="container">
		<div class="photo-section-content">
			<h2>Más que rutas, experiencias</h2>
			<p>Cada sendero esconde su propio paisaje: bosques, gargantas, lagos de alta montaña y cumbres con vistas infinitas. Te ayudamos a planificar tu próxima salida con fichas técnicas claras: distancia, desnivel y duración real.</p>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'ruta' ) ); ?>" class="btn">Explorar rutas</a>
		</div>
	</div>
</section>

<section class="section" id="comunidad">
	<div class="container">
		<h2 class="section-title">Comunidad y noticias</h2>
		<p class="section-subtitle">Lo último del blog y lo que se cuece en el foro</p>

		<div class="community-grid">
			<div class="news-cards">
				<?php
				$posts = new WP_Query( array(
					'post_type'      => 'post',
					'posts_per_page' => 2,
				) );

				if ( $posts->have_posts() ) :
					while ( $posts->have_posts() ) :
						$posts->the_post();
						?>
						<article class="card">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="card-img">
									<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
								</div>
							<?php endif; ?>
							<div class="card-body">
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="card-excerpt"><?php the_excerpt(); ?></div>
								<a class="card-link" href="<?php the_permalink(); ?>">Leer más &rarr;</a>
							</div>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p>Todavía no hay entradas publicadas.</p>
					<?php
				endif;
				?>
			</div>

			<aside class="forum-preview">
				<h3>Desde el foro</h3>
				<ul class="forum-threads">
					<li class="forum-thread">
						<span class="avatar">M</span>
						<div>
							<a href="<?php echo esc_url( home_url( '/foro/#hilo-gredos' ) ); ?>">¿Cuándo es la mejor época pa el Circo de Gredos?</a>
							<span class="forum-meta">12 respuestas · Marta</span>
						</div>
					</li>
					<li class="forum-thread">
						<span class="avatar">J</span>
						<div>
							<a href="<?php echo esc_url( home_url( '/foro/#hilo-organos' ) ); ?>">Estado del sendero a los Órganos tras las lluvias</a>
							<span class="forum-meta">8 respuestas · Javi</span>
						</div>
					</li>
					<li class="forum-thread">
						<span class="avatar">L</span>
						<div>
							<a href="<?php echo esc_url( home_url( '/foro/#hilo-veleta' ) ); ?>">Recomendaciones de equipo pa el Veleta en otoño</a>
							<span class="forum-meta">15 respuestas · Laura</span>
						</div>
					</li>
				</ul>
				<a href="<?php echo esc_url( home_url( '/foro/' ) ); ?>" class="btn-outline-dark">Ir al foro</a>
			</aside>
		</div>
	</div>
</section>

<section class="photo-section photo-section--right">
	<img class="photo-section-bg" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/section-2.jpg' ); ?>" alt="">
	<div class="container">
		<div class="photo-section-content">
			<h2>Comparte tu aventura</h2>
			<p>Sube tus fotos, cuenta cómo fue tu ruta y descubre las experiencias de otros senderistas. Una comunidad pa quienes disfrutan caminar, sea cual sea su nivel.</p>
			<a href="<?php echo esc_url( home_url( '/galeria/' ) ); ?>" class="btn btn-outline">Ver galería</a>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="section-title">Galería</h2>
		<p class="section-subtitle">Un vistazo a los paisajes que te esperan</p>

		<div class="gallery-grid">
			<?php for ( $i = 1; $i <= 14; $i++ ) : ?>
				<a href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gallery-' . $i . '.jpg' ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gallery-' . $i . '.jpg' ); ?>" alt="Foto de senderismo <?php echo esc_attr( $i ); ?>" loading="lazy">
				</a>
			<?php endfor; ?>
		</div>

		<div class="gallery-cta">
			<a href="<?php echo esc_url( home_url( '/galeria/' ) ); ?>" class="btn">Ver galería completa</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
