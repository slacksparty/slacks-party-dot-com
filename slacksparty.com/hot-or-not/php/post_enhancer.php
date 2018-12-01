<?php
if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );

class showdownPostEnhancer
{

	function __construct() {
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		add_action( 'add_meta_boxes', array( $this, 'showdown_add_custom_box')  );
	}
	
	function action_admin_init() {
		
		// only hook up these filters if we're in the admin panel, and the current user has permission
		// to edit posts and pages
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {

      // find out if it's ok to show buttons (we don't want to show them on the competitor screen)
      $post_type = null;
      $post_id = 0;
      $showbutton = true;
      
      if ( isset($_GET['post_type']) ) {
        $post_type = $_GET['post_type'];
      } else if ( isset ( $_POST_['post_type']) ) {
        $post_type = $_POST_['post_type'];
      }
      
      if ($post_type) {
        if ( $post_type == "competitor" ) {
          $showbutton = false;
        }
      } else {
        if ( isset ( $_GET['post'])) {
          $post_id = $_GET['post'];
        } else if ( isset ( $_POST['post']) ) {
          $post_id = $_POST['post'];
        }
        
        if ($post_id) {
          $post = get_post($post_id);
          
          if ( $post->post_type == "competitor" ) {
            $showbutton = false;
          }
        }
      }
      
      if ( $showbutton ) {
          add_filter( 'mce_buttons', array( $this, 'showdown_mce_button' ) );
          add_filter( 'mce_external_plugins', array( $this, 'showdown_mce_plugin' ) );
      }
		}
	}
	
	function showdown_mce_button( $buttons ) {
		array_push( $buttons, 'showdown_button' );
		return $buttons;
	}
	
	function showdown_mce_plugin( $plugins ) {
		// this plugin file will work the magic of our button
		$plugins['showdown_button'] = plugin_dir_url( __FILE__ ) . 'postenhancer_plugin.js';
		return $plugins;
	}
	
	function showdown_add_custom_box() {
    add_meta_box( 
        'showdown_sectionid',
        __( 'ShowDown Help', 'showdownplugin' ),
        array( $this, 'showdown_inner_custom_box' ),
        'post' 
    );
    add_meta_box(
        'showdown_sectionid',
        __( 'ShowDown Help', 'showdownplugin' ),
        array( $this, 'showdown_inner_custom_box' ),
        'page'
    );
  }
  
  function showdown_inner_custom_box( $post ) {
    _e("<p><strong>Shortcodes</strong></p>
	<p>To enter shortcodes into your Post, use the shortcode generator provided in the Visual mode.</p>
	<p><strong>Basic Shortcodes</strong></p>
	<p>[showdown] - This will show one Competitor at a time for voting. The shortcode generator (lightning bolt icon) can help you enter customized shortcodes.</p>
	", "showdownplugin");
  }
}

$postenhancer = new showdownPostEnhancer();