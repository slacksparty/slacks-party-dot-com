<?php
// Create a class for Competitors
class TypeCompetitors {

	//public $meta_fields = array( 'title', 'description', 'groups', 'image', 'won', 'lost', 'drawn', 'shown', 'points', 'link' );
	// some fields we just don't edit
	public $meta_fields = array( 'title', 'description', 'groups', 'image', 'link' );
	
	public function TypeCompetitors() {
		
        $labels = array(
            'name'                          => 'Groups',
            'singular_name'                 => 'Group',
            'search_items'                  => 'Search Group',
            'popular_items'                 => 'Popular Group',
            'all_items'                     => 'All Groups',
            'parent_item'                   => 'Parent Group',
            'edit_item'                     => 'Edit Group',
            'update_item'                   => 'Update Group',
            'add_new_item'                  => 'Add New Group',
            'new_item_name'                 => 'New Group',
            'separate_items_with_commas'    => 'Separate Groups with commas',
            'add_or_remove_items'           => 'Add or remove Groups',
            'choose_from_most_used'         => 'Choose from most used Groups'
            );

        $args = array(
            'label'                         => 'groups',
            'labels'                        => $labels,
            'public'                        => true,
            'hierarchical'                  => true,
            'show_ui'                       => true,
            'show_in_nav_menus'             => true,
            'args'                          => array( 'orderby' => 'term_order' ),
            'rewrite'                       => array( 'slug' => 'groups', 'with_front' => false ),
            'query_var'                     => true
        );

        register_taxonomy( 'groups', 'competitors', $args );
		
		
        $competitorArgs = array(
                'labels' => array(
                'name' => __( 'Competitors', 'post type general name' ),
                'singular_name' => __( 'Competitor', 'post type singular name' ),
                'add_new' => __( 'Add New', 'competitor' ),
                'add_new_item' => __( 'Add New Competitor' ),
                'edit_item' => __( 'Edit Competitor' ),
                'new_item' => __( 'New Competitor' ),
                'view_item' => __( 'View Competitor' ),
                'search_items' => __( 'Search Competitors' ),
                'not_found' =>  __( 'No competitors found in search' ),
                'not_found_in_trash' => __( 'No competitors found in Trash' ),
			),
			'public' => true, 
			'show_ui' => true,
			'_builtin' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array('slug' => 'competitors', 'with_front' => false),
			'has_archive' => true,
			'query_var' => 'competitor',
			'taxonomies' =>  array( 'groups' ),
			'supports' => array('title','editor','author','thumbnail','comments')
        );

        register_post_type( 'competitor', $competitorArgs );

        add_action( 'admin_init', array(&$this, 'admin_init') ); // this must be first
        add_action( 'wp_insert_post', array(&$this, 'wp_insert_post'), 10, 2 );
        
        // add custom columns
        add_filter( 'manage_posts_custom_column', array( &$this, 'competitor_custom_columns' ));
        add_action( 'manage_edit-competitor_columns', array ( &$this, 'competitor_edit_columns' )); // manage_edit-{post_type}_columns used for custom post types
       
        // append competitor stats to the post describing it
        add_filter( 'the_content', array( &$this, 'post_append' ));
                
	}
		
	// Create the columns and heading title text
	public function competitor_edit_columns($columns) {

      $columns = array(
        'cb' 		=> '<input type="checkbox" />',
        'title' 	=> __('Competitor Name','showdownplugin'),
        'thumbnail'	=> __('Image','showdownplugin'),
        'shown'	=> __('HotOrNot Views','showdownplugin'),
        'points'	=> __('HotOrNot Points','showdownplugin'),
        'hot'	=> __('HotOrNot Rating','showdownplugin'),
      );
    
		return $columns;
	}
	// switching cases based on which $column we show the content of it
	public function competitor_custom_columns($column) {
		global $post;
		switch ($column) {
			case "title" : the_title();
			break;
			case "shown" : 
				$custom = get_post_custom();
				echo isset($custom["shown"]) ? $custom["shown"][0] : 0;
			break;
			case "points" : 
				$custom = get_post_custom();
				echo isset($custom["points"]) ? $custom["points"][0] : 0;
			break;
			case "hot" : 
				$custom = get_post_custom();
				if (((isset($custom["shown"]) && $custom["shown"] > 0) ? $custom["shown"] : 0) > 0 )  {
				   echo number_format(($custom["points"][0] + 0) / ($custom["shown"][0] + 0), 2, '.', '');
				} else {
				   echo "Not ranked";
				}
			break;
			case "thumbnail" : 
			   if (has_post_thumbnail()) {
			   	$image = wp_get_attachment_image_src(get_post_thumbnail_id());
			   	echo "<img src='".$image[0]."'>"; 
			   } else {
			   	echo "no thumbnail";
			   }
			break;		
		}
	}
	
	// Template redirect for custom templates
	public function post_append($content) {
		global $wp_query;
		global $post;
		
    global $wp_showdown;
    $arenaOptions = $wp_showdown->getOptions();
    $mode = $arenaOptions['mode'];
    $showIP = $arenaOptions['showIP'];

		if ($wp_query->query_vars['post_type'] == 'competitor') {
	  
      echo '<link type="text/css" rel="stylesheet" href="' . WPSDOWN_URL . '/css/wp-showdown.css" />' . "\n\n";
	  
        $custom = get_post_custom($post->ID);

        $shown = $custom["shown"][0] + 0;
        $points = $custom["points"][0] + 0;

        if ( $shown > 0 ) {
          $hot = number_format($points / $shown,  2, '.', '');
        } else {
          $hot = __("Not Ranked", "showdownplugin");
        }

        $content.= '<div class="wp-showdown-stats">';
        $content.= '<h3>'. __("ShowDown stats", "showdownplugin") .'</h3>';
        $content.= '<p><strong>'. __("Points:", "showdownplugin") .'</strong> '. $points .'</p>';
        $content.= '<p><strong>'. __("Times Shown:", "showdownplugin") .'</strong> '. $shown .'</p>';
        $content.= '<p><strong>'. __("Ranked:", "showdownplugin") .'</strong> '. $hot .'</p>';
        $content.= '</div>';      

        if ($showIP == "Yes") {
          $lastIP = $custom["lastIP"][0];
          if ($lastIP == "") $lastIP = "N/A";

          $content.= '<div class="wp-showdown-tracking">';
          $content.= '<h3>'. __("Tracking", "showdownplugin") .'</h3>';
          $content.= '<p><strong>'. __("Last IP:", "showdownplugin") .'</strong> '. $lastIP .'</p>';
          $content.= '</div>';   
        }   

		}
		
		return $content;
	}
	
	// For inserting posts
	public function wp_insert_post($post_id, $post = null) {
	
    // we don't want this on Quick Edit
    if ( defined('DOING_AJAX') )
      return;
	
		if ($post->post_type == "competitor") {
			
			$data_valid  = true;
			
			foreach ($this->meta_fields as $key) {
				$value = @$_POST[$key];
				if (empty($value)) {
					delete_post_meta($post_id, $key);
					continue;
				}
				if (!is_array($value)) {
					if (!update_post_meta($post_id, $key, $value)) {
						add_post_meta($post_id, $key, $value);
					}
				} else {
					delete_post_meta($post_id, $key);
					foreach ($value as $entry) add_post_meta($post_id, $key, $entry);
				}
			}
			
		}
	}
	
	// Add meta box
	function admin_init() {
     add_meta_box("competitor-meta", "Competitor Stats", array(&$this, "meta_options"), "competitor", "side", "high");
  	 add_meta_box("competitorB-meta", "Competitor Options", array(&$this, "meta_options2"), "competitor", "normal", "high");
	}
	
	public function meta_options2() {
		global $post;
				
		$custom = get_post_custom($post->ID);
		$link = $custom["link"][0];
		?>
    <p><?php _e("In the <a href=\"http://showdownplugin.com/download/\">premium version</a>, you can specify an external link for the featured image of this competitor. Useful if you want to embed an affiliate link.", "showdownplugin") ?></p>
		<?php
	} // end meta options
	
	
	// Side Admin post meta contents
	public function meta_options() {
		global $post;

    global $wp_showdown;
    $arenaOptions = $wp_showdown->getOptions();
    $mode = $arenaOptions['mode'];
				
		$custom = get_post_custom($post->ID);


    ?>
        
      <div id="showdown_stats">
    <?php

    $points = $custom["points"][0] + 0;
    $shown = $custom["shown"][0] + 0;
    if ( $shown > 0 ) {
      $hot = number_format($points / $shown , 2, '.', '');
    } else {
      $hot = "Not Ranked";
    }

    ?>
      <h4>== ShowDown Stats ==</h4>
      <p><strong><?php _e("Points:", "showdownplugin");?> </strong><?php echo $points; ?></p>
      <p><strong><?php _e("Times Shown:", "showdownplugin");?> </strong><?php echo $shown; ?></p>
      <p><strong><?php _e("Ranked:", "showdownplugin");?> </strong><?php echo $hot; ?></p>
      </div>
    <?php    

    $lastIP = $custom["lastIP"][0];
    if ($lastIP == "") $lastIP = "N/A";

    ?>
      <h4>== Tracking Stats ==</h4>
      <p><strong><?php _e("Last IP:", "showdownplugin");?> </strong><?php echo $lastIP; ?></p>

	  <style>
		.btn {
  display: inline-block;
  *display: inline;
  /* IE7 inline-block hack */

  *zoom: 1;
  padding: 4px 12px;
  margin-bottom: 0;
  font-size: 14px;
  line-height: 20px;
  *line-height: 20px;
  text-align: center;
  vertical-align: middle;
  cursor: pointer;
  color: #333333;
  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
  background-color: #f5f5f5;
  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);
  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  *background-color: #e6e6e6;
  /* Darken IE7 buttons by default so they stand out more given they won't have borders */

  filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
  border: 1px solid #bbbbbb;
  *border: 0;
  border-bottom-color: #a2a2a2;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  *margin-left: .3em;
  -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
  -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
  box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
}
.btn-danger {
  color: #ffffff;
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
text-decoration: none;
  background-color: #da4f49;
  background-image: -moz-linear-gradient(top, #ee5f5b, #bd362f);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#bd362f));
  background-image: -webkit-linear-gradient(top, #ee5f5b, #bd362f);
  background-image: -o-linear-gradient(top, #ee5f5b, #bd362f);
  background-image: linear-gradient(to bottom, #ee5f5b, #bd362f);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffbd362f', GradientType=0);
  border-color: #bd362f #bd362f #802420;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  *background-color: #bd362f;
  /* Darken IE7 buttons by default so they stand out more given they won't have borders */

  filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
}
a.btn-danger:hover,
a.btn-danger:active,
a.btn-danger.active,
a.btn-danger.disabled,
a.btn-danger[disabled] {
  color: #ffffff;
  background: #bd362f;
  *background-color: #a9302a;
}
	  </style>
	  <p style="margin-bottom: 0;">Get more features:</p>
	  <p style="margin-top: 0;"><a href="http://showdownplugin.com/download/" class="btn btn-danger">Buy ShowDown</a></p>
    <?php
	} // end meta options

	
} // end of Competitor class

/* Initialize Post Types */
add_action('init', 'pCompetitorInit');
function pCompetitorInit() {
	global $competitors;
	$competitors = new TypeCompetitors();
}

?>