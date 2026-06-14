<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Foro</h1>
		<p class="section-subtitle">Dudas, estado de senderos, equipo y quedadas de la comunidad Nevasenda</p>

		<div class="forum-wrapper">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
