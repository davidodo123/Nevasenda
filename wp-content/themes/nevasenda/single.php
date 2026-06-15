<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1 class="entry-title"><?php the_title(); ?></h1>

			<div class="entry-meta">
				<span><?php echo esc_html( get_the_date() ); ?></span>
				<span class="dot"></span>
				<span><?php echo esc_html( nevasenda_reading_time() ); ?> min de lectura</span>
			</div>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="ruta-hero"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<div class="entry-content">
					<?php comments_template(); ?>
				</div>
			<?php endif; ?>
			<?php
		endwhile;
		?>
	</div>
</section>

<?php get_footer(); ?>
