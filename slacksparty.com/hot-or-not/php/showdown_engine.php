<?php
// Create a class for Competitors
class ArenaEngine {
	
	public function ArenaEngine() {
		
       add_shortcode('wphotornot', array($this, 'shortcode_hotornot'));  
       add_shortcode('wphotornotstats', array($this, 'shortcode_HONstats'));  
       
       // enable shortcode support in widgets
       add_filter('widget_text', 'do_shortcode'); 
	}

    public function shortcode_HONstats( $atts ) {
        // extract the attributes into variables  
        extract(shortcode_atts(array(  
          'group' => '',
          'reporttype' => 'top_winner',
          'number' => 10,
          'image' => "true",  
          'desc' => "true",  
        ), $atts));  

          switch ($reporttype) {
            case 'top_winner' : // stats on top winners

              // if not tag specified, then use complete set
              $loop = new WP_Query( array( 'post_type' => 'competitor', 'order' => 'DESC', 'orderby' => 'meta_value_num', 'meta_key' => 'points', 'groups' => $group, 'posts_per_page' => $number  ) ); 
              break;
      
            case 'top_loser' :
              $loop = new WP_Query( array( 'post_type' => 'competitor', 'order' => 'ASC', 'orderby' => 'meta_value_num', 'meta_key' => 'points', 'groups' => $group, 'posts_per_page' => $number  ) ); 
              break;

          }

          $return = "";
          while ( $loop->have_posts() ) : $loop->the_post();
            $return .= '<div class="wp-showdown-stat">';
            $return .= the_title( '<h3 class="wp-showdown-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h3>', false ); 

            if ( $image == "true" ) { // if image is selected - show featured image for competitor
              $return .= '<div class="wp-showdown-stat-content"><a href="'. get_permalink() .'">';
              $return .= get_the_post_thumbnail( get_the_ID() ,array ($HONwidth,$HONheight) );
              $return .= '</a></div>';
            }
			
			$return .= '<p>Points: ' . (get_post_meta( get_the_ID(), 'points', true) + 0) . '</a></p>';
			
            if ( $desc == "true" ) {  // if description is selected - show description/content for competitor
              $return .= '<div class="wp-showdown-content"><p>';
              
              $content = get_the_content();
              $content = apply_filters('the_content', $content);
              $content = str_replace(']]>', ']]&gt;', $content);
              
              $return .= $content;              
              
              $return .= '</p></div>';
            }

            $return .= '</div>'; 
             
          endwhile;

        // Reset Query Data
        wp_reset_query(); 

        return $return;
    }


    public function shortcode_hotornot( $atts ) {
        // extract the attributes into variables  
        extract(shortcode_atts(array(  
          'group' => "",  
          'image' => "true",  
          'desc' => "false",  
        ), $atts));  
    
        // get settings
        global $wp_showdown;
        $arenaOptions = $wp_showdown->getOptions();
        $header = $arenaOptions['header'];
        $width = $arenaOptions['HONwidth'];
        $height = $arenaOptions['HONheight'];
        $resultdrawn = $arenaOptions['resultdrawn'];
        $resultwin1 = $arenaOptions['resultwin1'];
        $resultwin2 = $arenaOptions['resultwin2'];
        $assigndraw = $arenaOptions['assigndraw'];
        $haswon = $arenaOptions['haswon'];
        $average = $arenaOptions['average'];
        $hasvoted = $arenaOptions['hasvoted'];
        $hotcaption = $arenaOptions['hotcaption'];
        $notcaption = $arenaOptions['notcaption'];
        $IPrestrictHON = $arenaOptions['IPrestrictHON'];
        $showTwitter = false;
        $showFB = $arenaOptions['showFB'];
		$showGP = $arenaOptions['showGP'];
        $groupsname = $arenaOptions['groupsname'];
        $txtTransition = $arenaOptions['txtTransition'];

        $return = "";

        // make sure we handle lack of options gracefully
        if ( $width + 0 == 0 ) $width = 250;
        if ( $height + 0 == 0 ) $height = 250;

        // READY FOR DISPLAY
        // This bit we may move to an include
        ?>    
        <script type="text/javascript"> 
        function AssignWinner( winner ) {

          <?php if ($txtTransition != "") { ?>
          if (winner != 0) {
          jQuery.blockUI({ 
             message: '<?php echo $txtTransition ?>', 
             overlayCSS:  {
                backgroundColor: '#000',
                opacity:         0.6,
                cursor:          'wait'
            },           
            blockMsgClass: 'blockMsg',            
              css: { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff'                  
              } }); 
           }
           <?php } ?>

           jQuery('input[name=arenawinner]').val(winner);
           jQuery('#form_arena').submit();
        }
        </script>
        <?php    

          $return .= '<div id="hotornot" class="wp-showdown-hotornot">';

          if (isset($_POST['whoshot'])){
                    
            if (!wp_verify_nonce($_POST['SecCheck'],'SecCheck')) {
              echo "Something fishy is going on!";
              exit;
            }
          
            // we're being economical here and use ArenaWinner to store the score
            $whoshot = $_POST['whoshot'] + 0;
            $newpoints = $_POST['arenawinner'] + 0;
            $lastIP = $_SERVER['REMOTE_ADDR'];
          
            if ($IPrestrictHON == "activate") {
              if ($lastIP == get_post_meta($whoshot, 'lastIP', true)) {
                  $newpoints = 0;
                  $return .= '<div class="wp-showdown-youvoted">';
                  $return .= __('You have already voted on this Competitor', "showdownplugin");
                  $return .= '</div>';
              }
            }
          
            // let's update the record first
            if ( $newpoints > 0 ) {
                update_post_meta($whoshot, 'lastIP', $lastIP );

                $shown = get_post_meta($whoshot, 'shown', true);
                $shown = $shown + 1;
                update_post_meta($whoshot, 'shown', $shown );

                $points = get_post_meta($whoshot, 'points', true);
                $points = $points + $newpoints;
                update_post_meta($whoshot, 'points', $points);

                $return .= '<div class="wp-showdown-youvoted">';
                  
                $return .= '<p class="wp-showdown-youvotedvote">'.$hasvoted.': <span>'.$newpoints.'</span></p>';
                
                $return .= '<div class="wp-showdown-sharing">';                
                if ($showFB == "Yes") {
                   $return .= '<span class="wp-showdown-facebook"><a href="http://www.facebook.com/sharer.php?u='.get_permalink( $whoshot ).'&t=Just voted '.$newpoints.' on '.get_the_title($whoshot).'" title="Facebook share button" target="blank">Share on Facebook</a></span>';
                }
                $return .= " ";
                if ($showTwitter == "Yes") {
                   $return .= '<span class="wp-showdown-twitter"><a href="http://twitter.com/home?status=Just voted '.$newpoints.' on '.get_the_title($whoshot).' at '.get_permalink( $whoshot ).' #hotornot" title="Share on Twitter" target="_blank">Share on Twitter</a></span>'; 
                }
                $return .= '</div>';
                
                $return .= '<div class="wp-showdown-youvotedimg">';
                
                $link = get_post_meta($whoshot, 'link', true);
                
                if ( strlen ( $link ) < 1 ) {
                  $return .= '<a href="' . get_permalink( $whoshot ) .'">';
                 } else {
                  $return .= '<a href="' . $link .'">';                 
                 }
                $return .= get_the_post_thumbnail($whoshot, array(100,100));            
                $return .= '</a>';
                
                $return .= '</div>';
                $return .= '<p class="wp-showdown-youvotedavg">'.$average.': '.number_format($points/$shown,2,".","").'</p>';
                    
                $return .= '</div><!- You Voted Area Ends -->';
             } else {
                // user decided not to vote
             }
             
             // in both cases, we want to scroll down to result area
             $return .= "<script type='text/javascript'>";
             $return .= "jQuery('html,body').animate({scrollTop: jQuery('#hotornot').offset().top},'slow');";
             $return .= "</script>";
          } else {
                // nothing registered yet
                //$return .= '<div class="wp-showdown-youvoted">';                  
                //$return .= '<div class="wp-showdown-youvotedimg">';
                //$return .= '<img src="' . WPSDOWN_CSS."/default.png" . '" />';
                //$return .= '</div>';                    
                //$return .= '</div><!- You Voted Area Ends -->';          
          }


          $loop = new WP_Query( array( 'post_type' => 'competitor', 'orderby' => 'rand', 'groups' => $group, 'posts_per_page' => 1  ) ); 

		  if ($showGP == "Yes") {
			if ($group != "") {
              $return .= "<div class='wp-showdown-groupname'><h3>".$groupsname . ": ". $group."</h3></div>"; 
			}
          }

          $return .= "<form action= '' id='form_arena' name='form_arena' method='POST'>";

          while ( $loop->have_posts() ) : $loop->the_post();

            $return .= '<div class="wp-showdown-votehere">';
                
            $return .= '<div class="wp-showdown-votenumbers"><span class="wp-showdown-captionhot">'.$hotcaption.'</span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(10);">10</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(9);">9</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(8);">8</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(7);">7</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(6);">6</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(5);">5</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(4);">4</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(3);">3</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(2);">2</a></span>';
            $return .= '<span class="wp-showdown-voteclass"><a href="javascript:AssignWinner(1);">1</a></span>';
            $return .= '<span class="wp-showdown-captionnot">'.$notcaption.'</span></div>';

            if ( $image == "true" ) { // if image is selected - show featured image for competitor              
              $return .= '<div class="wp-showdown-votehereimg">';
              $return .= get_the_post_thumbnail( get_the_ID() ,array ($width,$height) );
              $return .= '</div>';
            }
                
            $return .= the_title( '<p class="wp-showdown-voteherecaption">', '</p>', false ); 

                
            if ( $desc == "true") {  // if description is selected - show description/content for competitor
              $return .= '<div class="wp-showdown-content">';
              
              $content = get_the_content();
              $content = apply_filters('the_content', $content);
              $content = str_replace(']]>', ']]&gt;', $content);
              
              $return .= $content;              
              
              $return .= '</div>';
            }                
                
			$return .= '<div class="wp-showdown-votedraw"><a href="javascript:AssignWinner(0);">'.$assigndraw.'</a></div>';
            $return .= '</div><!-- Vote Here Area Ends -->';
            $return .= '<input type="hidden" id="whoshot" name="whoshot" value="'.get_the_ID().'">';

          endwhile;
          $return .= '<input type="hidden" id="arenawinner" name="arenawinner" value="">';
          $return .= wp_nonce_field('SecCheck','SecCheck' , false);
          $return .= '</form>';
          $return .= '</div><!-- ShowDown Ends -->';

        
        // Reset Query Data
        wp_reset_query(); 
        
        return $return;
    }

	
} // end of ArenaEngine{} class

/* Initialize Post Types */
add_action('init', 'pArenaEngineInit');
function pArenaEngineInit() {
	global $arenaengine;
	$arenaengine = new ArenaEngine();
	
	// make sure jQuery gets loaded
	wp_enqueue_script("jquery");
	wp_enqueue_script( "blockUI", WPSDOWN_JS."/jquery.blockUI.js",array('jquery'));
}

?>