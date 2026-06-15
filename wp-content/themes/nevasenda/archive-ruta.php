<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Rutas</h1>
		<p class="section-subtitle">Explora todas las rutas de senderismo de nuestro catálogo</p>

		<div class="rutas-grid">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					$distancia  = nevasenda_ruta_meta( get_the_ID(), 'distancia' );
					$desnivel   = nevasenda_ruta_meta( get_the_ID(), 'desnivel' );
					$duracion   = nevasenda_ruta_meta( get_the_ID(), 'duracion' );
					$dificultad = get_the_terms( get_the_ID(), 'dificultad' );
					$zona       = get_the_terms( get_the_ID(), 'zona' );
					?>
					<article class="ruta-card">
						<a href="<?php the_permalink(); ?>" class="ruta-card__media">
							<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium_large' ); ?>
							<div class="ruta-card__top">
								<?php if ( ! empty( $dificultad ) && ! is_wp_error( $dificultad ) ) : ?>
									<span class="ruta-card__pill ruta-card__pill--<?php echo esc_attr( $dificultad[0]->slug ); ?>"><?php echo esc_html( $dificultad[0]->name ); ?></span>
								<?php endif; ?>
								<?php if ( $distancia ) : ?>
									<span class="ruta-card__pill"><?php echo nevasenda_icon( 'ruler' ); ?><?php echo esc_html( $distancia ); ?> km</span>
								<?php endif; ?>
							</div>
							<div class="ruta-card__info">
								<h3><?php the_title(); ?></h3>
								<?php if ( ! empty( $zona ) && ! is_wp_error( $zona ) ) : ?>
									<span class="ruta-card__zona"><?php echo nevasenda_icon( 'pin' ); ?><?php echo esc_html( $zona[0]->name ); ?></span>
								<?php endif; ?>
							</div>
						</a>
						<div class="ruta-card__stats">
							<div class="ruta-card__stat"><?php echo nevasenda_icon( 'ruler' ); ?><strong><?php echo $distancia ? esc_html( $distancia ) . ' km' : '–'; ?></strong>Distancia</div>
							<div class="ruta-card__stat"><?php echo nevasenda_icon( 'terrain' ); ?><strong><?php echo $desnivel ? esc_html( $desnivel ) . ' m' : '–'; ?></strong>Desnivel</div>
							<div class="ruta-card__stat"><?php echo nevasenda_icon( 'clock' ); ?><strong><?php echo $duracion ? esc_html( $duracion ) : '–'; ?></strong>Duración</div>
						</div>
						<a class="ruta-card__link" href="<?php the_permalink(); ?>">Ver ruta <?php echo nevasenda_icon( 'arrow' ); ?></a>
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
