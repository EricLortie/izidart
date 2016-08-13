<?php  
    define('_LIMIT_' , 10 );
    define('_AUTL_' , 7 );
    define('BLOCK_TITLE_LEN' , 50 );
    
    /* google maps defines */
    define('MAP_LAT'    , 48.85680934671159 );
    define('MAP_LNG'    , 2.353348731994629 );
    define('MAP_CLAT'   , 48.85700699730661 );
    define('MAP_CLNG'   , 2.354121208190918 );
    define('MAP_ZOOM'   , 15 );
	define('DEFAULT_AVATAR'   , get_template_directory_uri()."/images/default_avatar.jpg" );
	define('DEFAULT_AVATAR_100'   , get_template_directory_uri()."/images/default_avatar_100.jpg" );
	define('DEFAULT_AVATAR_LOGIN'   , get_template_directory_uri()."/images/default_avatar_login.png" );

    define( '_TN_'      , wp_get_theme() );

	define('BRAND'      , '' );
	define('ZIP_NAME'   , 'megusta' );

    add_action('admin_bar_menu', 'de_cosmotheme');
    
	include 'lib/php/main.php';

    
    
    include 'lib/php/actions.register.php';
    include 'lib/php/menu.register.php';

    $content_width = 600;
  
    if( function_exists( 'add_theme_support' ) ){
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'post-thumbnails' );
    }

    if( function_exists( 'add_image_size' ) ){
        add_image_size( '60x60'     		, 60    , 60    , true );
        add_image_size( '170x100'           , 170   , 100   , true );   /* similar post 1/3 */
        add_image_size( '210x100'           , 210   , 100   , true );   /* similar post 1/4 */
        add_image_size( '290x150'           , 290   , 150   , true );   /* category grid view  1/3 from content */
        add_image_size( '285x150'           , 285   , 150   , true );   /* category grid view  1/2 from content */
        add_image_size( '600xXXX'           , 600   , 9999  );
        add_image_size( '920xXXX'           , 920   , 9999  );
		add_image_size( '200x100'           , 200   , 100   , true ); /* gallery size */
    }
    
    add_theme_support( 'custom-background' ); /*requires WP v >= 3.4  */

	add_theme_support( 'post-formats' , array( 'image' , 'video' ,'link',  'audio') );
	add_editor_style('editor-style.css');
	
		
	
	if(isset( $_GET['post_id'] ) &&  $_GET['post_id'] == -1 ){ 
		/*disable flash uploader, we need that to avoid uploader failure on front end*/
		add_filter('flash_uploader', '__return_false', 5);

	}
    
	/* Localization */
    load_theme_textdomain( 'cosmotheme' );
    load_theme_textdomain( 'cosmotheme' , get_template_directory() . '/languages' );
    
    if ( function_exists( 'load_child_theme_textdomain' ) ){
        load_child_theme_textdomain( 'cosmotheme' );
    }

	$pg = get_pages();
	$do_post_item_page = true;

	foreach( $pg as $p ){
        if( $p -> post_title == 'Post Item' ){
            $do_post_item_page = false;
            break;
        }
    }

	/*allow subscribers to upload files*/
	if ( current_user_can('subscriber') && !current_user_can('upload_files') )
	add_action('admin_init', 'allow_subscriber_uploads');

	function allow_subscriber_uploads() {
		$subscriber = get_role('subscriber');
		$subscriber->add_cap('upload_files');
	}
	
	/*create Post Item page*/	
    if( $do_post_item_page ){
        $pages = array(
            'post_title' => 'Post Item',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page'
        );

        wp_insert_post($pages);
    }

    /* Cosmothemes Backend link */
    function de_cosmotheme() {
        global $wp_admin_bar;    
        if ( !is_super_admin() || !is_admin_bar_showing() ){
            return;
        }
        $wp_admin_bar -> add_menu( array(
            'id' => 'cosmothemes',
            'parent' => '',
            'title' => _TN_,
            'href' => admin_url( 'admin.php?page=cosmothemes__general' )
            ) );   
    }

    

	if( !options::logic( 'general' , 'show_admin_bar' ) ){
		add_filter( 'show_admin_bar', '__return_false' );
	}

	add_editor_style('editor-style.css');

   

    function cosmo_enqueue_scripts() {

        if ( is_singular() ) { 
            wp_enqueue_script( "comment-reply" ); 
        } 
        
        wp_enqueue_script( 'actions', get_template_directory_uri().'/lib/js/actions.js' , array( 'jquery' , 'media-upload' , 'thickbox' ) );
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox'); 
        wp_enqueue_style( 'ui-lightness');
        wp_enqueue_style('thickbox');
        wp_enqueue_script( 'pageslide' , get_template_directory_uri() . '/js/jquery.pageslide.min.js' , array( 'jquery' ), false, true ); /*for showing/hide mobile menu*/
        
    }

    add_action('wp_enqueue_scripts', 'cosmo_enqueue_scripts');

    /**
     * As WP 4.0 added wp_texturize, we'll need the next function to disable texturizing the shortcodes.
     */

    function disable_texturize_for_shortcodes( $shortcodes ) {
        global $shortcode_tags;

        foreach ($shortcode_tags as $key => $value) {
            $shortcodes[] = $key;
        }


        return $shortcodes;
    }
    add_filter( 'no_texturize_shortcodes', 'disable_texturize_for_shortcodes' );


    
/**
 * For debug
 */
function deb( $code = '' ){
    echo '<pre style="text-align: left; background: rgba(255, 255, 255, 0.57); padding: 10px;">';
        echo 'DEB PRINTED: ' . PHP_EOL;
        echo '--------------------------------------------------------------------' . PHP_EOL;
        print_r( $code );
        echo '<br>';
        echo '----------------------------------END OF CODE-----------------------' . PHP_EOL;
    echo '</pre>';
}
