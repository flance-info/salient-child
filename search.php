<?php
/**
 * The template for search results.
 *
 * @package Salient WordPress Theme
 * @version 13.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $nectar_options;
$header_format = ( ! empty( $nectar_options['header_format'] ) ) ? $nectar_options['header_format'] : 'default';
$theme_skin    = NectarThemeManager::$skin;

$search_results_layout           = ( ! empty( $nectar_options['search-results-layout'] ) ) ? $nectar_options['search-results-layout'] : 'default';
$search_results_header_bg_image  = ( ! empty( $nectar_options['search-results-header-bg-image'] ) && isset( $nectar_options['search-results-header-bg-image'] ) ) ? nectar_options_img( $nectar_options['search-results-header-bg-image'] ) : null;

$using_sidebar = ( $search_results_layout === 'default' || $search_results_layout === 'list-with-sidebar' ) ? true : false;
$using_excerpt = ( $search_results_layout === 'list-no-sidebar' || $search_results_layout === 'list-with-sidebar' ) ? true : false;

?>

<div id="page-header-bg"  data-height="250">

	<?php if ( $search_results_header_bg_image ) { ?>
		<div class="page-header-bg-image-wrap" id="nectar-page-header-p-wrap">
			<div class="page-header-bg-image" style="background-image: url(<?php echo esc_url( $search_results_header_bg_image ); ?>);"></div>
		</div>

		<div class="page-header-overlay-color"></div>
	<?php } ?>

	<div class="container">
    <div class="row">
			<div class="col span_12">
				<div class="inner-wrap search-header">
					<h1><?php _e( 'Результаты поиска', 'salient' ); ?></h1>
					<div class="search-bar-wrapper">
						<div id="search-2" class="widget widget_search">
							<form role="search" method="get" class="search-form" action="https://nai.test/">
								<input type="text" class="search-field" placeholder="Search..." value="" name="s" title="Search for:">
								<button type="submit" class="search-widget-btn">
									<span class="normal icon-salient-search" aria-hidden="true"></span>
									<span class="text">Search</span>
								</button>
							</form>
						</div>
					</div>
					<?php if ( isset($wp_query) && $wp_query->found_posts !== null ) : ?>
						<div class="search-result-count">
							<?php
								printf(
									/* translators: %d: number of results */
									__( 'Найдено %d материалов', 'salient' ),
									$wp_query->found_posts
								);
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>


<div class="container-wrap" data-layout="<?php echo esc_attr( $search_results_layout ); ?>">

	<div class="container main-content">

		<div class="row">

			<?php $search_col_span = ( $using_sidebar === true ) ? '9' : '12'; ?>
			<div class="col span_<?php echo esc_attr( $search_col_span ); // WPCS: XSS ok. ?>">

				<div id="search-results" data-layout="<?php echo esc_attr( $search_results_layout ); ?>">

					<?php

					add_filter( 'wp_get_attachment_image_attributes', 'nectar_remove_lazy_load_functionality' );

					if ( have_posts() ) :
						while ( have_posts() ) :

							the_post();

							$using_post_thumb = has_post_thumbnail( $post->ID );

							if ( get_post_type( $post->ID ) === 'post' ) {
								?>
								<article class="nai-search-card">
									<div class="nai-search-card-inner">
										<div class="nai-search-card-date">
											<?php echo get_the_date('d.m.Y'); ?>
										</div>
										<div class="nai-search-card-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</div>
										<div class="nai-search-card-desc">
											<?php the_excerpt(); ?>
										</div>
									</div>
									<div class="nai-search-card-divider"></div>
								</article>

								<?php
							} elseif ( get_post_type( $post->ID ) === 'page' ) {
								?>
								<article class="nai-search-card">
									<div class="nai-search-card-inner">
										<div class="nai-search-card-date">
											<?php echo get_the_date('d.m.Y'); ?>
										</div>
										<div class="nai-search-card-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</div>
										<div class="nai-search-card-desc">
											<?php the_excerpt(); ?>
										</div>
									</div>
									<div class="nai-search-card-divider"></div>
								</article>

								<?php
							} elseif ( get_post_type( $post->ID ) === 'portfolio' ) {
								?>
								<article class="nai-search-card">
									<div class="nai-search-card-inner">
										<div class="nai-search-card-date">
											<?php echo get_the_date('d.m.Y'); ?>
										</div>
										<div class="nai-search-card-title">
											<a href="<?php echo esc_url( $nectar_portfolio_project_url ); ?>"><?php the_title(); ?></a>
										</div>
										<div class="nai-search-card-desc">
											<?php echo esc_html__( 'Portfolio Item', 'salient' ); ?>
										</div>
									</div>
									<div class="nai-search-card-divider"></div>
								</article>

								<?php
							} elseif ( get_post_type( $post->ID ) === 'product' ) {
								?>
								<article class="nai-search-card">
									<div class="nai-search-card-inner">
										<div class="nai-search-card-date">
											<?php echo get_the_date('d.m.Y'); ?>
										</div>
										<div class="nai-search-card-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</div>
										<div class="nai-search-card-desc">
											<?php the_excerpt(); ?>
										</div>
									</div>
									<div class="nai-search-card-divider"></div>
								</article>

							<?php } else { ?>
								<article class="nai-search-card">
									<div class="nai-search-card-inner">
										<div class="nai-search-card-date">
											<?php echo get_the_date('d.m.Y'); ?>
										</div>
										<div class="nai-search-card-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</div>
										<div class="nai-search-card-desc">
											<?php the_excerpt(); ?>
										</div>
									</div>
									<div class="nai-search-card-divider"></div>
								</article>

							<?php }

					endwhile;

					else :

						echo '<h3>' . esc_html__( 'Sorry, no results were found.', 'salient' ) . '</h3>';
						echo '<p>' . esc_html__( 'Please try again with different keywords.', 'salient' ) . '</p>';
						get_search_form();

				  endif;

					?>

				</div><!--/search-results-->

				<div class="search-result-pagination" data-layout="<?php echo esc_attr( $search_results_layout ); ?>">
					<?php nectar_pagination(); ?>
				</div>

			</div><!--/span_9-->

			<?php if ( $using_sidebar === true ) { ?>

				<div id="sidebar" class="col span_3 col_last">
					<?php 
						nectar_hook_sidebar_top();
						get_sidebar(); 
						nectar_hook_sidebar_bottom();
					?>
				</div><!--/span_3-->

			<?php } ?>

		</div><!--/row-->

	</div><!--/container-->
	<?php nectar_hook_before_container_wrap_close(); ?>
</div><!--/container-wrap-->

</div>

<?php get_footer(); ?>
