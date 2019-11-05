<?php 
/*This file is part of X blog child theme.

All functions of this file will be loaded before of parent theme functions.
Learn more at https://codex.wordpress.org/Child_Themes.

Note: this function loads the parent stylesheet before, then child theme stylesheet
(leave it in place unless you know what you are doing.)
*/

function x_blog_plus_theme_setup(){
	register_nav_menus( array(
			'top-menu' => esc_html__( 'Top menu', 'x-blog-plus' ),
		) );
}
add_action('after_setup_theme','x_blog_plus_theme_setup');

function x_blog_plus_fonts_url() {
	$fonts_url = '';

		$font_families = array();

		$font_families[] = 'Roboto Slab:400,700';
		$font_families[] = 'Lato:400,400i,700,700i';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );


	return esc_url_raw( $fonts_url );
}


function x_blog_plus_enqueue_child_styles() {
	wp_enqueue_style( 'x-blog-plus-google-font', x_blog_plus_fonts_url(), array(), null );
	wp_enqueue_style( 'x-blog-plus-parent-style', get_template_directory_uri() . '/style.css',array('slicknav','xblog-google-font', 'xblog-style'), '', 'all');
   wp_enqueue_style( 'x-blog-plus-main',get_stylesheet_directory_uri() . '/assets/css/main.css',array(), '1.0.0', 'all');
   	wp_enqueue_script( 'x-blog-plus-main-js', get_stylesheet_directory_uri() . '/assets/js/xmain.js');

  
}
add_action( 'wp_enqueue_scripts', 'x_blog_plus_enqueue_child_styles');



if ( ! function_exists( 'x_blog_plus_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function x_blog_plus_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( '- %s', 'post date', 'x-blog-plus' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( '- %s', 'post author', 'x-blog-plus' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="posted-on"><i class="fa fa-clock-o"></i>' . wp_kses_post($posted_on) . '</span><span class="byline"> <i class="fa fa-user-circle"></i>' . wp_kses_post($byline) . '</span>'; 

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link"><i class="fa fa-comment"></i> ';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'comment <span class="screen-reader-text"> on %s</span>', 'x-blog-plus' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

	}
endif;

/**
 * Customizer additions.
 */
require get_stylesheet_directory() . '/inc/customizer.php';


if ( ! function_exists( 'xblog_pro_inline_css' ) ) :
function xblog_pro_inline_css() {
	 $xblog_plus_grid_height = get_theme_mod('xblog_plus_grid_height', 750 );
	 $style = '';
	 if( $xblog_plus_grid_height != 750 ){
	 	$style .= '.site-main article.xgrid-item{min-height:'.esc_attr($xblog_plus_grid_height).'px}';
	 }


        wp_add_inline_style( 'x-blog-plus-main', $style );
}
add_action( 'wp_enqueue_scripts', 'xblog_pro_inline_css' );
endif;