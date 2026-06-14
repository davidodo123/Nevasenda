<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="hero">
	<img class="hero-bg-img" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/hero-bg.jpg' ); ?>" alt="">
	<div class="container">
		<h1>Senderismo sin límites, <span>vive la montaña</span></h1>
		<p>Descubre rutas, consejos y reportajes pa explorar la naturaleza a tu ritmo. Comunidad de senderismo en blanco, negro y azul.</p>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'ruta' ) ); ?>" class="btn">Ver rutas</a>
	</div>
</section>

<section class="section">
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
					$distancia = nevasenda_ruta_meta( get_the_ID(), 'distancia' );
					$dificultad = get_the_terms( get_the_ID(), 'dificultad' );
					?>
					<article class="card">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="card-img">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
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

<section class="section section-alt">
	<div class="container">
		<h2 class="section-title">Últimas noticias</h2>
		<p class="section-subtitle">Reportajes y novedades del blog</p>

		<div class="cards-grid">
			<?php
			$posts = new WP_Query( array(
				'post_type'      => 'post',
				'posts_per_page' => 3,
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
			<?php for ( $i = 1; $i <= 8; $i++ ) : ?>
				<a href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gallery-' . $i . '.jpg' ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gallery-' . $i . '.jpg' ); ?>" alt="Foto de senderismo <?php echo esc_attr( $i ); ?>" loading="lazy">
				</a>
			<?php endfor; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
