<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Rutas</h1>
		<p class="section-subtitle">Explora todas las rutas de senderismo de nuestro catálogo</p>

		<div class="cards-grid">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					$distancia  = nevasenda_ruta_meta( get_the_ID(), 'distancia' );
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
			else :
				?>
				<p>Todavía no hay rutas publicadas.</p>
				<?php
			endif;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	</div>
</section>

<?php get_footer(); ?>
