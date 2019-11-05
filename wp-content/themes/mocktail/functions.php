<?php
/**
 * Display all Mocktail functions and definitions
 *
 * @package Theme Freesia
 * @subpackage Mocktail
 * @since Mocktail 1.0
 */

add_action( 'wp_enqueue_scripts', 'mocktail_enqueue_styles' );

function mocktail_enqueue_styles() {
	$mocktail_color_styles = get_theme_mod('mocktail_colors','red-color');

	wp_enqueue_style( 'mocktail-parent-style', trailingslashit(get_template_directory_uri() ) . 'style.css');

	if($mocktail_color_styles == 'red-color'){
		wp_enqueue_style( 'mocktail-red', trailingslashit(get_stylesheet_directory_uri() ) . 'css/red-color-style.css');
	} elseif($mocktail_color_styles == 'blue-color'){
		wp_enqueue_style( 'mocktail-blue', trailingslashit(get_stylesheet_directory_uri() ) . 'css/blue-color-style.css');
	} elseif($mocktail_color_styles == 'green-color'){
		wp_enqueue_style( 'mocktail-green', trailingslashit(get_stylesheet_directory_uri() ) . 'css/green-color-style.css');
	} else {
		wp_enqueue_style( 'mocktail-purple', trailingslashit(get_stylesheet_directory_uri() ) . 'css/purple-color-style.css');
	}

}


function mocktail_customize_register( $wp_customize ) {
	if(!class_exists('Cocktail_Plus_Features')){
		class Mocktail_Customize_upgrade extends WP_Customize_Control {
			public function render_content() { ?>
				<a title="<?php esc_attr_e( 'Review Mocktail', 'mocktail' ); ?>" href="<?php echo esc_url( 'https://wordpress.org/support/view/theme-reviews/mocktail/' ); ?>" target="_blank" id="about-mocktail">
				<?php esc_html_e( 'Review Mocktail', 'mocktail' ); ?>
				</a><br/>
				<a href="<?php echo esc_url( 'https://themefreesia.com/theme-instruction/mocktail/' ); ?>" title="<?php esc_attr_e( 'Theme Instructions', 'mocktail' ); ?>" target="_blank" id="about-mocktail">
				<?php esc_html_e( 'Theme Instructions', 'mocktail' ); ?>
				</a><br/>
				<a href="<?php echo esc_url( 'https://tickets.themefreesia.com' ); ?>" title="<?php esc_attr_e( 'Support Ticket', 'mocktail' ); ?>" target="_blank" id="about-mocktail">
				<?php esc_html_e( 'Tickets', 'mocktail' ); ?>
				</a><br/>
			<?php
			}
		}

		$wp_customize->add_section('mocktail_upgrade_links', array(
			'title'					=> __('About Mocktail', 'mocktail'),
			'priority'				=> 1000,
		));
		$wp_customize->add_setting( 'mocktail_upgrade_links', array(
			'default'				=> false,
			'capability'			=> 'edit_theme_options',
			'sanitize_callback'	=> 'wp_filter_nohtml_kses',
		));
		$wp_customize->add_control(
			new Mocktail_Customize_upgrade(
			$wp_customize,
			'mocktail_upgrade_links',
				array(
					'section'				=> 'mocktail_upgrade_links',
					'settings'				=> 'mocktail_upgrade_links',
				)
			)
		);
	}

	$wp_customize->add_setting( 'mocktail_title', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'capability'            => 'manage_options'
		)
	);
	$wp_customize->add_control( 'mocktail_title', array(
		'label' => __('Title','mocktail'),
		'section' => 'cocktail_frontpage_features',
		'type'     => 'text',
		'priority'	=> 20,
		)
	);

	$wp_customize->add_setting( 'mocktail_description', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'capability'            => 'manage_options'
		)
	);
	$wp_customize->add_control( 'mocktail_description', array(
		'label' => __('Description','mocktail'),
		'section' => 'cocktail_frontpage_features',
		'type'     => 'text',
		'priority'	=> 30,
		)
	);
}

add_action( 'customize_register', 'mocktail_customize_register' );

add_action( 'customize_register', 'mocktail_customize_register_color_styles' );
function mocktail_customize_register_color_styles( $wp_customize ) {

	$wp_customize->add_section( 'custom_color_styles', array(
		'title' 						=> __('Custom Color Styles','mocktail'),
		'priority'					=> 100,
		'panel'					=>'colors'
	));


	$wp_customize->add_setting('mocktail_colors', array(
		'default' => 'red-color',
		'sanitize_callback' => 'cocktail_sanitize_select',
		));
	$wp_customize->add_control('mocktail_colors', array(
		'priority' =>10,
		'label' => __('Custom Color Styles', 'mocktail'),
		'description' => __('Change Color Styles into Red, Green and Purple Color. If Plus version used, this feature is Optional', 'mocktail'),
		'section' => 'custom_color_styles',
		'settings' => 'mocktail_colors',
		'type' => 'select',
		'checked' => 'checked',
		'choices' => array(
			'red-color' => __('Red ','mocktail'),
			'blue-color' => __('Blue','mocktail'),
			'green-color' => __('Green','mocktail'),
			'purple-color' => __('Purple','mocktail'),
		),
	));
}

if(!class_exists('Cocktail_Plus_Features')){
	// Add Upgrade to Plus Button.
	require_once( trailingslashit( get_stylesheet_directory() ) . 'inc/upgrade-plus/class-customize.php' );
}

function mocktail_frontpage_features(){ 
$cocktail_settings = cocktail_get_theme_options();
$cocktail_disable_frontpage_features = $cocktail_settings['cocktail_disable_frontpage_features'];
$cocktail_no_of_frontpage = $cocktail_settings['cocktail_no_of_frontpage'];
$mocktail_title = get_theme_mod('mocktail_title','');
$mocktail_description = get_theme_mod('mocktail_description','');
$query = new WP_Query(array(
			'posts_per_page' =>  intval($cocktail_settings['cocktail_no_of_frontpage']),
			'post_type'					=> 'post',
			'category_name' => esc_attr($cocktail_settings['cocktail_frontpage_features']),
	));
	if(($cocktail_disable_frontpage_features !=1) && ($cocktail_settings['cocktail_frontpage_features'] !='')){ ?>
	<div class="our-feature-box">
		<div class="wrap">
			<div class="inner-wrap">
				<?php if ($mocktail_title !='' || $mocktail_description !='') { ?>
					<div class="our-feature-box-header">
						<?php if ($mocktail_title !='') { ?>
							<h2 class="feature-box-title"><?php echo esc_html($mocktail_title);?></h2>
						<?php }
						if ($mocktail_title !='') { ?>
							<div class="feature-box-description"><?php echo esc_attr($mocktail_description);?></div>
					<?php } ?>
					</div>
				<?php } ?>
				<div class="column clearfix">
					<?php while ($query->have_posts()):$query->the_post(); ?>
					<div class="four-column">
						<div class="feature-content-wrap clearfix">
							<?php if(has_post_thumbnail() ){ ?>
								<a class="feature-icon" href="<?php the_permalink();?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
							<?php } ?>
							<div class="feature-content">
								<h2 class="feature-title"><a title="<?php the_title_attribute(); ?>" href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>	
							</div> <!-- end .feature-content -->
						</div> <!-- end .feature-content-wrap -->
					</div> <!-- end .four-column -->
					<?php endwhile;
					wp_reset_postdata(); ?>
				</div> <!-- end .column -->
			</div> <!-- end .inner-wrap -->
		</div> <!-- end .wrap -->
	</div> <!-- end .our-feature-box -->
	<?php } ?>

<?php }
add_action('mocktail_display_frontpage_features','mocktail_frontpage_features');

if(!class_exists('Cocktail_Plus_Features')){

	/**
	 * TGM plugin Activation
	 */
	require_once( trailingslashit( get_stylesheet_directory() ) . '/inc/tgm/tgm.php' );

}

/***************** USED CLASS FOR BODY ******************************/
function mocktail_body_class($mocktail_class) {

		$mocktail_class[] = 'mocktail-color';

	return $mocktail_class;
}
add_filter('body_class', 'mocktail_body_class');