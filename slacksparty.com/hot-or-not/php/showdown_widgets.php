<?php
if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );

class Top10Widget extends WP_Widget
{
  function Top10Widget()
  {
    $widget_ops = array('classname' => 'Top10Widget', 'description' => 'Shows the top 10 competitors on your site' );
    $this->WP_Widget('Top10Widget', 'ShowDown Top 10', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '','type' => 'hotornot', 'number' => 5, 'group' => '' ) );
    $title = $instance['title'];
    $type = $instance['type'];
    $number = $instance['number'];
    $group = $instance['group'];

?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:", "showdownplugin") ?> 
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label></p>

  <p><label for="<?php echo $this->get_field_id('type'); ?>"><?php _e("Type:", "showdownplugin") ?> 
    <select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
      <option value="hotornot"<?php if ($type=='hotornot') echo " selected"; ?>><?php _e("ShowDown", "showdownplugin") ?></option>
    </select> 
  </label></p>

  <p><label for="<?php echo $this->get_field_id('group'); ?>"><?php _e("Group:", "showdownplugin") ?> 
    <?php $args = array(
            'taxonomy'          => 'groups',
            'show_option_none'  => __('Show from all competitors', "showdownplugin"),
            'class'             => 'widefat',
            'show_count'        => 1,
            'hierarchical'      => 1,
            'name'              => $this->get_field_name('group'),
            'id'                => $this->get_field_id('group'),
            'selected'          => $group
          );
    wp_dropdown_categories($args); ?>
  </label></p>

  <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e("Number:", "showdownplugin") ?>
    <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
  </label></p>


<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['number'] = intval($new_instance['number']);
    $instance['type'] = $new_instance['type'];
    $instance['group'] = $new_instance['group'];

    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $type = empty($instance['type']) ? 'hotornot' : $instance['type'];
    $number = intval($instance['number'])==0 ? '5' : $instance['number'];
    $group = empty($instance['group']) ? '0' : $instance['group'];
    
    // resolve group id to group name
    $mygroup = get_term_by('id', $group, 'groups');
    $group_name = $mygroup->name;

    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    // WIDGET CODE GOES HERE
    switch ($type) {
      case 'hotornot' : // stats on top winners
        $loop = new WP_Query( array( 'post_type' => 'competitor', 'order' => 'DESC', 'orderby' => 'meta_value_num', 'meta_key' => 'points', 'groups' => $group_name, 'posts_per_page' => $number  ) ); 
        break;

      default :
        $loop = new WP_Query( array( 'post_type' => 'competitor', 'order' => 'DESC', 'orderby' => 'meta_value_num', 'meta_key' => 'won', 'groups' => $group_name, 'posts_per_page' => $number  ) ); 
    }

    while ( $loop->have_posts() ) : $loop->the_post();
      echo '<div class="wp-showdown-widget">';
      echo the_title( '<h3 class="wp-showdown-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h3>', false ); 

      echo '<div class="wp-showdown-widget-content"><a href="'. get_permalink() .'">';
      echo get_the_post_thumbnail( get_the_ID() ,array (100,100) );
      echo '</a></div>';
          
      switch ($type) {
        case 'hotornot' : // stats on top winners
          echo '<p>'.__('Points', "showdownplugin").': ' . (get_post_meta( get_the_ID(), 'points', true) + 0) . '</a></p>';
          break;

        default:
          echo '<p>'.__('Wins', "showdownplugin").': ' . (get_post_meta( get_the_ID(), 'won', true) + 0) . '</a></p>';
     }

      echo '</div>'; 
       
    endwhile;

    // Reset Query Data
    wp_reset_query(); 
    
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("Top10Widget");') );?>