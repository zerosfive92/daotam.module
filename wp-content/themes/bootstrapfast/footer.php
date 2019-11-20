<?php
/**
 * The template for displaying the footer.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BootstrapFast
 */

?>
			</div><!-- #contennt -->
		</div><!-- .row -->
	</div><!-- .container -->
	<footer id="colophon" class="site-footer container-fluid" role="contentinfo">
		<div class="row">
			<?php

			if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
				?>
				<div class="col-md-3">
					<?php dynamic_sidebar( 'footer-sidebar-1' ); ?>
				</div>
				<?php
			}

			if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
				?>
				<div class="col-md-3">
					<?php dynamic_sidebar( 'footer-sidebar-2' ); ?>
				</div>
				<?php
			}

			if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
				?>
				<div class="col-md-3">
					<?php dynamic_sidebar( 'footer-sidebar-3' ); ?>
				</div>
				<?php
			}

			if ( is_active_sidebar( 'footer-sidebar-4' ) ) {
				?>
				<div class="col-md-3">
					<?php dynamic_sidebar( 'footer-sidebar-4' ); ?>
				</div>
				<?php
			}

			?>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span>Đạo Tâm 2019</span>
			</div><!-- .col-12 -->
		</div>
	</footer><!-- #colophon -->
<?php wp_footer(); ?>
</body>
</html>
