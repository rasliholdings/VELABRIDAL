<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}
/**
 *	After theme Setup Hook
 */
function blossom_mommy_blog_theme_setup() {
	/**
	* Make child theme available for translation.
    * Translations can be filed in the /languages/ directory.
	*/
	load_child_theme_textdomain( 'blossom-mommy-blog', get_stylesheet_directory() . '/languages' );

    /**
    * Slider Two Image Size
    */
    add_image_size( 'blossom-mommy-blog-slider-big', 920, 650,true );
    add_image_size( 'blossom-mommy-blog-slider', 460, 310,true );
    add_image_size( 'blossom-mommy-blog-two', 768, 480, true );

}
add_action( 'after_setup_theme', 'blossom_mommy_blog_theme_setup', 11 );

/**
 * Enqueue scripts and styles.
 */
if( ! function_exists( 'blossom_mommy_blog_scripts' ) ):
	function blossom_mommy_blog_scripts() {
		$my_theme = wp_get_theme();
    	$version = $my_theme['Version'];

    	 wp_enqueue_style( 'blossom-feminine-style', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'animate' ) );

		wp_enqueue_style( 'blossom-mommy-blog-style', get_stylesheet_directory_uri(). '/style.css' , array( 'blossom-feminine-style' ) , $version );

		wp_enqueue_script( 'blossom-mommy-blog', get_stylesheet_directory_uri(). '/js/custom.js', array( 'jquery' ), $version, true );

        $array = array( 
            'rtl'       => is_rtl(),
            'auto' => get_theme_mod( 'slider_auto', true ),
        ); 
        wp_localize_script( 'blossom-mommy-blog', 'blossom_mommy_blog_data', $array );

	}
endif;
add_action( 'wp_enqueue_scripts', 'blossom_mommy_blog_scripts', 10 );

//Remove a function from the parent theme
function remove_parent_filters(){ //Have to do it after theme setup, because child theme functions are loaded first
    remove_action( 'customize_register', 'blossom_feminine_customizer_theme_info' );
}
add_action( 'init', 'remove_parent_filters' );

/**
*	Blossom Mommy Blog Body Classes
*/
function blossom_feminine_body_classes( $classes ){
	global $wp_query;
    $home_layout_option = get_theme_mod( 'home_layout_option', 'two' );

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }
    
    if ( $wp_query->found_posts == 0 ) {
        $classes[] = 'no-post';
    }

    // Adds a class of custom-background-image to sites with a custom background image.
    if ( get_background_image() ) {
        $classes[] = 'custom-background-image custom-background';
    }
    
    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
        $classes[] = 'custom-background-color custom-background';
    }
    
    if( is_search() && ! is_post_type_archive( 'product' ) ){
        $classes[] = 'search-result-page';   
    }
    
    $classes[] = blossom_feminine_sidebar_layout();

	if( $home_layout_option == 'two' ) {
		$classes[] = 'blog-layout-two';
	}
   
	return $classes;
}

function blossom_mommy_blog_customize_register( $wp_customize ) {

    $wp_customize->get_control( 'slider_animation' )->active_callback   = 'blossom_mommy_blog_slider_active_cb';

    $wp_customize->add_section( 
        'theme_info', 
        array(
            'title'     => __( 'Demo & Documentation ' , 'blossom-mommy-blog' ),
            'priority'  => 6,
        )
    );

    /** Important Links */
    $wp_customize->add_setting(
        'theme_info_link',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $theme_info = '<p>';
    $theme_info .= sprintf( __( '%1$sDemo Link:%2$s %3$sClick here.%4$s', 'blossom-mommy-blog' ), '<strong>', '</strong>' , '<a href="' . esc_url( 'https://blossomthemes.com/theme-demo/?theme=blossom-mommy-blog' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p><p>';
    $theme_info .= sprintf( __( '%1$sDocumentation Link:%2$s %3$sClick here.%4$s', 'blossom-mommy-blog' ), '<strong>', '</strong>' , '<a href="' . esc_url( 'https://docs.blossomthemes.com/docs/blossom-mommy-blog//' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p>';

    $wp_customize->add_control( new Blossom_Feminine_Note_Control( $wp_customize,
            'theme_info_link',
            array(
                'section'       => 'theme_info',
                'description'   => $theme_info,
            )
        )        
    );

    /** Typography */
    $wp_customize->add_section(
        'typography_settings',
        array(
            'title'    => __( 'Typography', 'blossom-mommy-blog' ),
            'priority' => 10,
            'panel'    => 'appearance_settings',
        )
    );
    
    /** Primary Font */
    $wp_customize->add_setting(
        'primary_font',
        array(
            'default'           => 'Cabin',
            'sanitize_callback' => 'blossom_feminine_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Feminine_Select_Control(
            $wp_customize,
            'primary_font',
            array(
                'label'       => __( 'Primary Font', 'blossom-mommy-blog' ),
                'description' => __( 'Primary font of the site.', 'blossom-mommy-blog' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_feminine_get_all_fonts(),  
            )
        )
    );
    
    /** Secondary Font */
    $wp_customize->add_setting(
        'secondary_font',
        array(
            'default'           => 'EB Garamond',
            'sanitize_callback' => 'blossom_feminine_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Feminine_Select_Control(
            $wp_customize,
            'secondary_font',
            array(
                'label'       => __( 'Secondary Font', 'blossom-mommy-blog' ),
                'description' => __( 'Secondary font of the site.', 'blossom-mommy-blog' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_feminine_get_all_fonts(),  
            )
        )
    );
    /** Font Size*/
    $wp_customize->add_setting( 
        'font_size', 
        array(
            'default'           => 18,
            'sanitize_callback' => 'blossom_feminine_sanitize_number_absint'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Feminine_Slider_Control( 
            $wp_customize,
            'font_size',
            array(
                'section'     => 'typography_settings',
                'label'       => __( 'Font Size', 'blossom-mommy-blog' ),
                'description' => __( 'Change the font size of your site.', 'blossom-mommy-blog' ),
                'choices'     => array(
                    'min'   => 10,
                    'max'   => 50,
                    'step'  => 1,
                )                 
            )
        )
    );

    /** Primary Color*/
    $wp_customize->add_setting( 
        'primary_color', array(
            'default'           => '#78c0a8',
            'sanitize_callback' => 'sanitize_hex_color'
        ) 
    );

    $wp_customize->add_control( 
        new WP_Customize_Color_Control( 
            $wp_customize, 
            'primary_color', 
            array(
                'label'       => __( 'Primary Color', 'blossom-mommy-blog' ),
                'description' => __( 'Primary color of the theme.', 'blossom-mommy-blog' ),
                'section'     => 'colors',
                'priority'    => 5,                
            )
        )
    );
    

    /** Layout Settings */
    $wp_customize->add_panel(
        'layout_settings',
        array(
            'title'     => 'Layout Settings',
            'priority'  => 45,
        )
    );

    /** Slider Layout **/
    $wp_customize->add_section(
        'slider_layout_settings',
        array(
            'title'     => __( 'Slider Layout', 'blossom-mommy-blog' ),
            'panel'     => 'layout_settings',
            'priority'  => 15,

        )
    );

    $wp_customize->add_setting(
        'slider_layout_option',
        array(
            'default'           => 'two',
            'sanitize_callback' => 'esc_attr',
        )
    );

    $wp_customize->add_control(
        new Blossom_Feminine_Radio_Image_Control(
            $wp_customize,
            'slider_layout_option',
            array(
                'section'       => 'slider_layout_settings',
                'label'         => __( 'Slider Layouts', 'blossom-mommy-blog' ),
                'description'   => __( 'This is the slider layouts for your blog.', 'blossom-mommy-blog' ),
                'choices'       => array(
                    'one'   => get_stylesheet_directory_uri() . '/images/slider/slider-one.jpg',
                    'two'   => get_stylesheet_directory_uri() . '/images/slider/slider-two.jpg',
                )
            )
        )
    );

    /** Home Page Layout **/
    $wp_customize->add_section(
        'homepage_layout',
        array(
            'title'     => __( 'Home Page Layout', 'blossom-mommy-blog' ),
            'panel'     => 'layout_settings',
            'priority'  => 20,

        )
    );

    $wp_customize->add_setting(
        'home_layout_option',
        array(
            'default'           => 'two',
            'sanitize_callback' => 'esc_attr',
        )
    );

    $wp_customize->add_control(
        new Blossom_Feminine_Radio_Image_Control(
            $wp_customize,
            'home_layout_option',
            array(
                'section'       => 'homepage_layout',
                'label'         => __( 'Home Page Layouts', 'blossom-mommy-blog' ),
                'description'   => __( 'This is the layout for blog index page.', 'blossom-mommy-blog' ),
                'choices'       => array(
                    'one'  => get_stylesheet_directory_uri() . '/images/home/home-one.jpg',
                    'two'  => get_stylesheet_directory_uri() . '/images/home/home-two.jpg',
                )
            )
        )
    );
}
add_action( 'customize_register', 'blossom_mommy_blog_customize_register', 40 );

/**
*	Blossom Mommy Blog Banner Section
*/
function blossom_feminine_banner(){ 
    $ed_slider = get_theme_mod( 'ed_slider', true );
    $slider_layout = get_theme_mod( 'slider_layout_option', 'two' );

    $text = ( $slider_layout == 'two' ) ? 'text-holder' : 'banner-text';
    $item = ( $slider_layout == 'two' ) ? 'grid-item' : 'item';        

        
    if( ( is_front_page() || is_home() ) && $ed_slider ){ 
        $slider_type    = get_theme_mod( 'slider_type', 'latest_posts' );
        $slider_cat     = get_theme_mod( 'slider_cat' );
        $posts_per_page = get_theme_mod( 'no_of_slides', 3 );
    
        $args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',            
            'ignore_sticky_posts' => true
        );        
        if( $slider_type === 'cat' && $slider_cat ){
            $args['cat']            = $slider_cat; 
            $args['posts_per_page'] = -1;  
        }else{
            $args['posts_per_page'] = $posts_per_page;
        }
            
        $qry = new WP_Query( $args );
        
        if( $qry->have_posts() ){ ?> 
             <div class="banner banner-layout-<?php echo esc_attr( $slider_layout ); ?>">
                <?php if( $slider_layout == 'two' ) echo '<div class="container">'; ?>
                <div id="banner-slider"  class="owl-carousel slider-layout-<?php echo esc_attr( $slider_layout ); ?>">     
                    <?php if( $slider_layout == 'two' ) echo '<div class="item"><div class="grid-holder">'; ?>
                    <?php while( $qry->have_posts() ){ $qry->the_post(); ?>             
                        <div class="<?php echo esc_attr( $item ); if( $qry->current_post % 3 == 0 ) echo ' image-size' ;?>">
                            <?php 
                            if( $slider_layout == 'two' ){
                                $image_size = ( $qry->current_post % 3 == 0 ) ? 'blossom-mommy-blog-slider-big' : 'blossom-mommy-blog-slider';
                            }else{
                                $image_size = 'blossom-feminine-slider';
                            }
                            
                            if( has_post_thumbnail() ){
                                the_post_thumbnail( $image_size );    
                            }else{ 
                                blossom_feminine_get_fallback_svg( $image_size );
                            }
                            ?> 
                            <div class="<?php echo esc_attr( $text ); ?>">
                                <?php
                                    blossom_feminine_categories();
                                    the_title( '<h2 class="title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
                                ?>
                            </div>
                        </div>
                    <?php if( $slider_layout == 'two' && ( $qry->current_post % 3 == 2 ) && ( $qry->current_post + 1 < $qry->post_count ) ) echo '</div></div><div class="item"><div class="grid-holder">'; ?>
                    <?php } ?>
                    <?php if( $slider_layout == 'two' ) echo '</div></div>'; ?>
                </div>
                <?php if( $slider_layout == 'two' ) echo '</div>'; ?>
            </div>             
            <?php
            wp_reset_postdata();
        }        
    }
}
/** Blossom Feminine Category */
function blossom_feminine_categories() {
    $ed_cat_single = get_theme_mod( 'ed_category', false );
    // Hide category and tag text for pages.
    if ( 'post' === get_post_type() && !$ed_cat_single ) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list( ' ' );
        if ( $categories_list ) {
            echo '<span class="cat-links" itemprop="about">' . $categories_list . '</span>';
        }
    }       
}

/** Blossom Feminine Post Thumbnail */
function blossom_feminine_post_thumbnail(){ 
    $image_size     = 'thumbnail';
    $ed_featured    = get_theme_mod( 'ed_featured_image', true );
    $home_layout_option = get_theme_mod( 'home_layout_option', 'two' );
    $sidebar_layout = blossom_feminine_sidebar_layout();
    
    if( is_home() ){        
        echo '<div class="img-holder"><a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
        if( has_post_thumbnail() ){
            if( is_sticky() ){                
                $image_size = ( $sidebar_layout == 'full-width' ) ? 'blossom-feminine-featured' : 'blossom-feminine-with-sidebar';
            }elseif( $home_layout_option == 'two' ){
                $image_size = 'blossom-mommy-blog-two';
            }else{
                $image_size = 'blossom-feminine-blog';    
            }
            the_post_thumbnail( $image_size );    
        }else{
            $image_size = is_sticky() ? 'blossom-feminine-featured' : 'blossom-feminine-blog';
            blossom_feminine_get_fallback_svg( $image_size );   
        }        
        echo '</a></div>';
    }elseif( is_archive() || is_search() ){
        echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
        if( has_post_thumbnail() ){
            the_post_thumbnail( 'blossom-feminine-cat' );    
        }else{
            blossom_feminine_get_fallback_svg( 'blossom-feminine-cat' ); 
        }
        echo '</a>';
    }elseif( is_singular() ){
        echo '<div class="post-thumbnail">';
        $image_size = ( $sidebar_layout == 'full-width' ) ? 'blossom-feminine-featured' : 'blossom-feminine-with-sidebar';
        if( is_single() ){
            if( $ed_featured ) the_post_thumbnail( $image_size );
        }else{
            the_post_thumbnail( $image_size );
        }
        echo '</div>';
    }
}

/** Blossom Feminine Entry Header */
function blossom_feminine_entry_header(){ ?>
    <header class="entry-header">
    <?php 
        $home_layout_option = get_theme_mod( 'home_layout_option', 'two' );
        
        if( is_archive() || ( is_search() && ( 'post' === get_post_type() ) ) ) echo '<div class="top">'; 
        blossom_feminine_categories();
        /**
         * Social sharing in archive.
        */
        if( is_archive() ) do_action( 'blossom_feminine_social_sharing' );
        
        if( is_archive() || ( is_search() && ( 'post' === get_post_type() ) ) ) echo '</div>';
        
        if( is_single() ){
            the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' );
        }else{
            the_title( '<h2 class="entry-title" itemprop="headline"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );    
        }
        
        if ( 'post' === get_post_type()){ 
            if( $home_layout_option == 'one' || is_sticky() || ( $home_layout_option == 'two' && is_single() )  || is_archive() || is_search() ) {
                echo '<div class="entry-meta">';
                blossom_feminine_posted_by();
                blossom_feminine_posted_on();                
                blossom_feminine_comment_count();   
                echo '</div><!-- .entry-meta -->'; 
            }
                 
        }
        ?>
    </header><!-- .entry-header home-->
    <?php
}
/** Blossom Feminine Entry Footer */
function blossom_feminine_entry_footer(){ 
    $readmore = get_theme_mod( 'read_more_text', __( 'Read More', 'blossom-mommy-blog' ) );
    $home_layout_option = get_theme_mod( 'home_layout_option', 'two' );
    ?>
    <footer class="entry-footer">
    <?php 
        if( is_home() && is_sticky() && $home_layout_option == 'one'){ 
            if( $readmore ){ ?>
                <a href="<?php the_permalink(); ?>" class="btn-readmore"><?php echo esc_html( $readmore ); ?></a>
                <?php 
            }
            /**
             * Social sharing in home page
            */
            do_action( 'blossom_feminine_social_sharing' );            
        } 
        if( ( is_home() && $home_layout_option == 'two' )  && !( is_sticky( ) || is_single() || is_archive() || is_search() ) ){
            echo '<div class="entry-meta">';
            blossom_feminine_posted_by();
            blossom_feminine_posted_on();
            blossom_feminine_comment_count(); 
            echo '</div><!-- .entry-meta -->';

        }
        //Tags in single page
        if( is_single() ) blossom_feminine_tags();
        //edit post link
        blossom_feminine_edit_post_link(); 
    ?>
    </footer><!-- .entry-footer home-->
    <?php
}
/** Blossom Feminine Fonts URL */
function blossom_feminine_fonts_url(){
    $fonts_url = '';
    
    $primary_font       = get_theme_mod( 'primary_font', 'Cabin' );
    $ig_primary_font    = blossom_feminine_is_google_font( $primary_font );    
    $secondary_font     = get_theme_mod( 'secondary_font', 'EB Garamond' );
    $ig_secondary_font  = blossom_feminine_is_google_font( $secondary_font );    
    $site_title_font    = get_theme_mod( 'site_title_font', array( 'font-family'=>'Playfair Display', 'variant'=>'700italic' ) );
    $ig_site_title_font = blossom_feminine_is_google_font( $site_title_font['font-family'] );
        
    /* Translators: If there are characters in your language that are not
    * supported by respective fonts, translate this to 'off'. Do not translate
    * into your own language.
    */
    $primary    = _x( 'on', 'Primary Font: on or off', 'blossom-mommy-blog' );
    $secondary  = _x( 'on', 'Secondary Font: on or off', 'blossom-mommy-blog' );
    $site_title = _x( 'on', 'Site Title Font: on or off', 'blossom-mommy-blog' );
    
    
    if ( 'off' !== $primary || 'off' !== $secondary || 'off' !== $site_title ) {
        
        $font_families = array();
     
        if ( 'off' !== $primary && $ig_primary_font ) {
            $primary_variant = blossom_feminine_check_varient( $primary_font, 'regular', true );
            if( $primary_variant ){
                $primary_var = ':' . $primary_variant;
            }else{
                $primary_var = '';    
            }            
            $font_families[] = $primary_font . $primary_var;
        }
         
        if ( 'off' !== $secondary && $ig_secondary_font ) {
            $secondary_variant = blossom_feminine_check_varient( $secondary_font, 'regular', true );
            if( $secondary_variant ){
                $secondary_var = ':' . $secondary_variant;    
            }else{
                $secondary_var = '';
            }
            $font_families[] = $secondary_font . $secondary_var;
        }
        
        if ( 'off' !== $site_title && $ig_site_title_font ) {
            
            if( ! empty( $site_title_font['variant'] ) ){
                $site_title_var = ':' . blossom_feminine_check_varient( $site_title_font['font-family'], $site_title_font['variant'] );    
            }else{
                $site_title_var = '';
            }
            $font_families[] = $site_title_font['font-family'] . $site_title_var;
        }
        
        $font_families = array_diff( array_unique( $font_families ), array('') );
        
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),            
        );
        
        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }
     
    return esc_url_raw( $fonts_url );
}

/** Blossom Feminine Dynamic CSS */
function blossom_feminine_dynamic_css(){
    
    $primary_font    = get_theme_mod( 'primary_font', 'Cabin' );
    $primary_fonts   = blossom_feminine_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'EB Garamond' );
    $secondary_fonts = blossom_feminine_get_fonts( $secondary_font, 'regular' );
    $font_size       = get_theme_mod( 'font_size', 18 );
    
    $site_title_font      = get_theme_mod( 'site_title_font', array( 'font-family'=>'Playfair Display', 'variant'=>'700italic' ) );
    $site_title_fonts     = blossom_feminine_get_fonts( $site_title_font['font-family'], $site_title_font['variant'] );
    $site_title_font_size = get_theme_mod( 'site_title_font_size', 60 );
    
    $primary_color = get_theme_mod( 'primary_color', '#78c0a8' );
    
    $rgb = blossom_feminine_hex2rgb( blossom_feminine_sanitize_hex_color( $primary_color ) );
     
    echo "<style type='text/css' media='all'>"; ?>
     
    .content-newsletter .blossomthemes-email-newsletter-wrapper.bg-img:after,
    .widget_blossomthemes_email_newsletter_widget .blossomthemes-email-newsletter-wrapper:after{
        <?php echo 'background: rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', 0.8);'; ?>
    }
    
    /* primary color */
    a{
        color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }
    
    a:hover,
    a:focus{
        color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }

    .secondary-nav ul li a:hover,
    .secondary-nav ul li a:focus,
    .secondary-nav ul li:hover > a,
    .secondary-nav ul li:focus > a,
    .secondary-nav .current_page_item > a,
    .secondary-nav .current-menu-item > a,
    .secondary-nav .current_page_ancestor > a,
    .secondary-nav .current-menu-ancestor > a,
    .header-t .social-networks li a:hover,
    .header-t .social-networks li a:focus,
    .main-navigation ul li a:hover,
    .main-navigation ul li a:focus,
    .main-navigation ul li:hover > a,
    .main-navigation ul li:focus > a,
    .main-navigation .current_page_item > a,
    .main-navigation .current-menu-item > a,
    .main-navigation .current_page_ancestor > a,
    .main-navigation .current-menu-ancestor > a,
    .banner .banner-text .title a:hover,
    .banner .banner-text .title a:focus,
    #primary .post .text-holder .entry-header .entry-title a:hover,
    #primary .post .text-holder .entry-header .entry-title a:focus,
    .widget ul li a:hover,
    .widget ul li a:focus,
    .site-footer .widget ul li a:hover,
    .site-footer .widget ul li a:focus,
    #crumbs a:hover,
    #crumbs a:focus,
    .related-post .post .text-holder .cat-links a:hover,
    .related-post .post .text-holder .cat-links a:focus,
    .related-post .post .text-holder .entry-title a:hover,
    .related-post .post .text-holder .entry-title a:focus,
    .comments-area .comment-body .comment-metadata a:hover,
    .comments-area .comment-body .comment-metadata a:focus,
    .search #primary .search-post .text-holder .entry-header .entry-title a:hover,
    .search #primary .search-post .text-holder .entry-header .entry-title a:focus,
    .site-title a:hover,
    .site-title a:focus,
    .widget_bttk_popular_post ul li .entry-header .entry-meta a:hover,
    .widget_bttk_popular_post ul li .entry-header .entry-meta a:focus,
    .widget_bttk_pro_recent_post ul li .entry-header .entry-meta a:hover,
    .widget_bttk_pro_recent_post ul li .entry-header .entry-meta a:focus,
    .widget_bttk_posts_category_slider_widget .carousel-title .title a:hover,
    .widget_bttk_posts_category_slider_widget .carousel-title .title a:focus,
    .site-footer .widget_bttk_posts_category_slider_widget .carousel-title .title a:hover,
    .site-footer .widget_bttk_posts_category_slider_widget .carousel-title .title a:focus,
    .portfolio-sorting .button:hover,
    .portfolio-sorting .button:focus,
    .portfolio-sorting .button.is-checked,
    .portfolio-item .portfolio-img-title a:hover,
    .portfolio-item .portfolio-img-title a:focus,
    .portfolio-item .portfolio-cat a:hover,
    .portfolio-item .portfolio-cat a:focus,
    .entry-header .portfolio-cat a:hover,
    .entry-header .portfolio-cat a:focus,
    .banner-layout-two .grid-item .text-holder .title a:hover,
    #primary .post .text-holder .entry-header .entry-meta a:hover,
    .blog.blog-layout-two #primary .post .text-holder .entry-footer .entry-meta a:hover
    {
        color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }

    .category-section .col .img-holder .text-holder:hover,
    .category-section .col .img-holder:hover .text-holder,
    .pagination a{
        border-color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }
    .category-section .col .img-holder:hover .text-holder span,
    #primary .post .text-holder .entry-footer .btn-readmore:hover,
    #primary .post .text-holder .entry-footer .btn-readmore:focus,
    .pagination a:hover,
    .pagination a:focus,
    .widget_calendar caption,
    .widget_calendar table tbody td a,
    .widget_tag_cloud .tagcloud a:hover,
    .widget_tag_cloud .tagcloud a:focus,
    #blossom-top,
    .single #primary .post .entry-footer .tags a:hover,
    .single #primary .post .entry-footer .tags a:focus,
    .error-holder .page-content a:hover,
    .error-holder .page-content a:focus,
    .widget_bttk_author_bio .readmore:hover,
    .widget_bttk_author_bio .readmore:focus,
    .widget_bttk_social_links ul li a:hover,
    .widget_bttk_social_links ul li a:focus,
    .widget_bttk_image_text_widget ul li .btn-readmore:hover,
    .widget_bttk_image_text_widget ul li .btn-readmore:focus,
    .widget_bttk_custom_categories ul li a:hover .post-count,
    .widget_bttk_custom_categories ul li a:hover:focus .post-count,
    .content-instagram ul li .instagram-meta .like,
    .content-instagram ul li .instagram-meta .comment,
    #secondary .widget_blossomtheme_featured_page_widget .text-holder .btn-readmore:hover,
    #secondary .widget_blossomtheme_featured_page_widget .text-holder .btn-readmore:focus,
    #secondary .widget_blossomtheme_companion_cta_widget .btn-cta:hover,
    #secondary .widget_blossomtheme_companion_cta_widget .btn-cta:focus,
    #secondary .widget_bttk_icon_text_widget .text-holder .btn-readmore:hover,
    #secondary .widget_bttk_icon_text_widget .text-holder .btn-readmore:focus,
    .site-footer .widget_blossomtheme_companion_cta_widget .btn-cta:hover,
    .site-footer .widget_blossomtheme_companion_cta_widget .btn-cta:focus,
    .site-footer .widget_blossomtheme_featured_page_widget .text-holder .btn-readmore:hover,
    .site-footer .widget_blossomtheme_featured_page_widget .text-holder .btn-readmore:focus,
    .site-footer .widget_bttk_icon_text_widget .text-holder .btn-readmore:hover,
    .site-footer .widget_bttk_icon_text_widget .text-holder .btn-readmore:focus,
    .slider-layout-two .text-holder .cat-links a:hover, 
    #primary .post .text-holder .entry-header .cat-links a:hover,
    .widget_bttk_posts_category_slider_widget .owl-theme .owl-prev:hover, 
    .widget_bttk_posts_category_slider_widget .owl-theme .owl-prev:focus, 
    .widget_bttk_posts_category_slider_widget .owl-theme .owl-next:hover, 
    .widget_bttk_posts_category_slider_widget .owl-theme .owl-next:focus,
    .widget_bttk_popular_post .style-two li .entry-header .cat-links a:hover, 
    .widget_bttk_pro_recent_post .style-two li .entry-header .cat-links a:hover, 
    .widget_bttk_popular_post .style-three li .entry-header .cat-links a:hover, 
    .widget_bttk_pro_recent_post .style-three li .entry-header .cat-links a:hover, .widget_bttk_posts_category_slider_widget .carousel-title .cat-links a:hover,
    .banner .owl-nav .owl-prev:hover, 
    .banner .owl-nav .owl-next:hover,
    button:hover, input[type="button"]:hover, 
    input[type="reset"]:hover, input[type="submit"]:hover, 
    button:focus, input[type="button"]:focus, input[type="reset"]:focus, 
    input[type="submit"]:focus,
    .banner .banner-text .cat-links a:hover,
    .header-t .tools .cart .count,
    #blossomthemes-email-newsletter-333 input.subscribe-submit-333:hover, 
    .widget_bttk_posts_category_slider_widget .owl-theme .owl-nav [class*="owl-"]:hover{
        background: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }

    .error-holder .page-content .number-404 {
        text-shadow: 6px 6px 0 <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }

    .pagination .current,
    .post-navigation .nav-links .nav-previous a:hover,
    .post-navigation .nav-links .nav-next a:hover,
    .post-navigation .nav-links .nav-previous a:focus,
    .post-navigation .nav-links .nav-next a:focus{
        background: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
        border-color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }

    #primary .post .entry-content blockquote,
    #primary .page .entry-content blockquote{
        border-bottom-color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
        border-top-color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }

    #primary .post .entry-content .pull-left,
    #primary .page .entry-content .pull-left,
    #primary .post .entry-content .pull-right,
    #primary .page .entry-content .pull-right{border-left-color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;}

    .error-holder .page-content h2{
        text-shadow: 6px 6px 0 <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
    }

    
    body,
    button,
    input,
    select,
    optgroup,
    textarea{
        font-family : <?php echo wp_kses_post( $primary_fonts['font'] ); ?>;
        font-size   : <?php echo absint( $font_size ); ?>px;
    }

    .banner .banner-text .title,
    #primary .sticky .text-holder .entry-header .entry-title,
    #primary .post .text-holder .entry-header .entry-title,
    .author-section .text-holder .title,
    .post-navigation .nav-links .nav-previous .post-title,
    .post-navigation .nav-links .nav-next .post-title,
    .related-post .post .text-holder .entry-title,
    .comments-area .comments-title,
    .comments-area .comment-body .fn,
    .comments-area .comment-reply-title,
    .page-header .page-title,
    #primary .post .entry-content blockquote,
    #primary .page .entry-content blockquote,
    #primary .post .entry-content .pull-left,
    #primary .page .entry-content .pull-left,
    #primary .post .entry-content .pull-right,
    #primary .page .entry-content .pull-right,
    #primary .post .entry-content h1,
    #primary .page .entry-content h1,
    #primary .post .entry-content h2,
    #primary .page .entry-content h2,
    #primary .post .entry-content h3,
    #primary .page .entry-content h3,
    #primary .post .entry-content h4,
    #primary .page .entry-content h4,
    #primary .post .entry-content h5,
    #primary .page .entry-content h5,
    #primary .post .entry-content h6,
    #primary .page .entry-content h6,
    .search #primary .search-post .text-holder .entry-header .entry-title,
    .error-holder .page-content h2,
    .widget_bttk_author_bio .title-holder,
    .widget_bttk_popular_post ul li .entry-header .entry-title,
    .widget_bttk_pro_recent_post ul li .entry-header .entry-title,
    .widget_bttk_posts_category_slider_widget .carousel-title .title,
    .content-newsletter .blossomthemes-email-newsletter-wrapper .text-holder h3,
    .widget_blossomthemes_email_newsletter_widget .blossomthemes-email-newsletter-wrapper .text-holder h3,
    #secondary .widget_bttk_testimonial_widget .text-holder .name,
    #secondary .widget_bttk_description_widget .text-holder .name,
    .site-footer .widget_bttk_description_widget .text-holder .name,
    .site-footer .widget_bttk_testimonial_widget .text-holder .name,
    .portfolio-text-holder .portfolio-img-title,
    .portfolio-holder .entry-header .entry-title,
    .single-blossom-portfolio .post-navigation .nav-previous a,
    .single-blossom-portfolio .post-navigation .nav-next a,
    .related-portfolio-title,
    .banner-layout-two .grid-item .text-holder .title,
    #primary .post .entry-content blockquote cite, 
    #primary .page .entry-content blockquote cite{
        font-family: <?php echo wp_kses_post( $secondary_fonts['font'] ); ?>;
    }

    .site-title{
        font-size   : <?php echo absint( $site_title_font_size ); ?>px;
        font-family : <?php echo wp_kses_post( $site_title_fonts['font'] ); ?>;
        font-weight : <?php echo esc_attr( $site_title_fonts['weight'] ); ?>;
        font-style  : <?php echo esc_attr( $site_title_fonts['style'] ); ?>;
    }
    
    <?php if( blossom_feminine_is_woocommerce_activated() ) { ?>
        .woocommerce ul.products li.product .add_to_cart_button:hover,
        .woocommerce ul.products li.product .add_to_cart_button:focus,
        .woocommerce ul.products li.product .product_type_external:hover,
        .woocommerce ul.products li.product .product_type_external:focus,
        .woocommerce nav.woocommerce-pagination ul li a:hover,
        .woocommerce nav.woocommerce-pagination ul li a:focus,
        .woocommerce #secondary .widget_shopping_cart .buttons .button:hover,
        .woocommerce #secondary .widget_shopping_cart .buttons .button:focus,
        .woocommerce #secondary .widget_price_filter .price_slider_amount .button:hover,
        .woocommerce #secondary .widget_price_filter .price_slider_amount .button:focus,
        .woocommerce #secondary .widget_price_filter .ui-slider .ui-slider-range,
        .woocommerce div.product form.cart .single_add_to_cart_button:hover,
        .woocommerce div.product form.cart .single_add_to_cart_button:focus,
        .woocommerce div.product .cart .single_add_to_cart_button.alt:hover,
        .woocommerce div.product .cart .single_add_to_cart_button.alt:focus,
        .woocommerce .woocommerce-message .button:hover,
        .woocommerce .woocommerce-message .button:focus,
        .woocommerce-cart #primary .page .entry-content .cart_totals .checkout-button:hover,
        .woocommerce-cart #primary .page .entry-content .cart_totals .checkout-button:focus,
        .woocommerce-checkout .woocommerce .woocommerce-info{
            background: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
        }

        .woocommerce nav.woocommerce-pagination ul li a{
            border-color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
        }

        .woocommerce nav.woocommerce-pagination ul li span.current{
            background: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
            border-color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
        }

        .woocommerce div.product .entry-summary .product_meta .posted_in a:hover,
        .woocommerce div.product .entry-summary .product_meta .posted_in a:focus,
        .woocommerce div.product .entry-summary .product_meta .tagged_as a:hover,
        .woocommerce div.product .entry-summary .product_meta .tagged_as a:focus{
            color: <?php echo blossom_feminine_sanitize_hex_color( $primary_color ); ?>;
        }
            
    <?php } ?>
           
    <?php echo "</style>";
}

function blossom_feminine_footer_bottom() { ?>
    <div class="site-info">
        <div class="container">
            <?php
                blossom_feminine_get_footer_copyright();
                
                esc_html_e( ' Blossom Mommy Blog | Developed By ', 'blossom-mommy-blog' );
                echo '<a href="' . esc_url( 'https://blossomthemes.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( ' Blossom Themes', 'blossom-mommy-blog' ) . '</a>.';
                
                printf( esc_html__( ' Powered by %s', 'blossom-mommy-blog' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'blossom-mommy-blog' ) ) .'" target="_blank">WordPress</a>.' );
                if ( function_exists( 'the_privacy_policy_link' ) ) {
                    the_privacy_policy_link();
                }
            ?>                    
        </div>
    </div>
<?php
}

/**
 * Active Callback for banner section
*/
function blossom_mommy_blog_slider_active_cb( $control ){
    
    $slider_layout  = get_theme_mod( 'slider_layout_option', 'two' );

    if( $slider_layout == 'one' ) return true;
    return false;
}