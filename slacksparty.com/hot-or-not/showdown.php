<?php
/*
Plugin Name: ShowDown
Plugin URI: http://showdownplugin.com
Description: Engage your readers by creating Yay/Nay contests (formerly known as Hot or Not). Use the Competitor custom post type to host your images and your content.
Version: 1.2
Author: Owen Cutajar & Hyder Jaffari
Author URI: http://weborithm.com
*/
/*
	v1.0
	- Initial release
	    .1 - remove transition if user "skips" selection
		.2 - Fixed missing icons
	v1.1 - Name change to Showdown
	v1.2 - WordPress v4.5 compatibility
*/
 
$arena_version = "1.2";

define("WPSDOWN_DIR", dirname(__FILE__));
define("WPSDOWN_URL", plugins_url() . "/showdown");

define("WPSDOWN_JS", WPSDOWN_URL . "/js");
define("WPSDOWN_CSS", WPSDOWN_URL . "/css");
define("WPSDOWN_PHP", WPSDOWN_DIR . "/php");
define("WPSDOWN_IMG", WPSDOWN_URL . "/images");

require_once WPSDOWN_PHP."/competitor_class.php";
require_once WPSDOWN_PHP."/showdown_engine.php";
require_once WPSDOWN_PHP."/showdown_widgets.php";
require_once WPSDOWN_PHP."/post_enhancer.php";

//==========================================
// main Code
//==========================================

if (!class_exists("WPShowndown")) {
  class WPShowdown {
  
    var $adminOptionsName = "WPShowdownOptions"; 
      
		function init() {
			$this->getOptions();
		}
		
		//Returns an array of WP Arena options
		function getOptions() {
		
		  // default options
			$wpShowdownOptions = array('header' => 'Click On The Best Photo You Want To Win This Battle.',
				                          'height' => 350,
				                          'width' => 397,
				                          'HONheight' => 350,
				                          'HONwidth' => 397,
				                          'showFB' => 'Yes',
				                          'showTwitter' => 'Yes',
				                          'showIP' => 'Yes',
										  'showGP' => 'Yes',
				                          'resultdrawn' => 'Drawn',
				                          'resultwin1' => 'Competitor 1 wins',
				                          'resultwin2' => 'Competitor 2 wins',
				                          'headingresult' => 'Results',
				                          'headingwinner' => 'Winner',
				                          'headingloser' => 'Loser',
				                          'headingdrawn' => 'Drawn',
				                          'haswon' => 'wins',
				                          'hasvoted' => 'You voted',
				                          'hotcaption' => 'Hot',
				                          'notcaption' => 'Not',
				                          'groupsname' => 'Groups',
				                          'average' => 'Average',
                                  'notvoted' => 'You have not voted yet',
                                  'assigndraw' => 'Skip',
				                          'HONCapTopGradient'=>'#7d7e7d',
				                          'HONCapBottomGradient'=>'#0e0e0e',
                                  'HONBackTopGradient'=>'#ffff88',
				                          'HONBackBottomGradient'=>'#ffe772',
				                          'HONNumberHover'=>'#ffffff',
				                          'HONNumberStatic'=>'#000000'); 


			$showdownOptions = get_option($this->adminOptionsName);
			
			if (!empty($showdownOptions)) {
				foreach ($showdownOptions as $key => $option)
					$wpShowdownOptions[$key] = $option;
			}				
			
			update_option($this->adminOptionsName, $wpShowdownOptions);
			
			return $wpShowdownOptions;
		}    
		

    function printTitlePage() {

      global $arena_version;
	    $showdownOptions = $this->getOptions();

      $image = "hotornot";

      // generate stats:
      $number_of_competitors = 0;
      $total_HoN_votes = 0;
      
      global $post;
      $args = array( 'post_type' => 'competitor','posts_per_page' => -1 );
      $myposts = get_posts( $args );
      foreach( $myposts as $post ) :	setup_postdata($post); 
      
        $custom = get_post_custom(get_the_ID());

        $number_of_competitors++;
        $total_HoN_votes += $custom["shown"][0];
             
      endforeach; 

      
      // Use WordPress built-in RSS handling
      require_once (ABSPATH . WPINC . '/rss.php');
      $rss_feed = "http://showdownplugin.com/category/blog/feed/";
      $rss = @fetch_rss( $rss_feed );

	  echo '<div class="showdownplugin showdownpluginhome clearfix">';
      echo '<h2>ShowDown Plugin</h2>';
	  //echo '<div id="outer" class="showdownpluginbg" style="background-image: url(' . WPSDOWN_IMG . '/' . $image . '.jpg); background-repeat: no-repeat; background-position: center;" /></div>';
      echo "<div id='inner' class='innershowdown'>";
	  echo "<p class='nucompetitors'><strong>".__('Plugin version:','showdownplugin')."</strong><span class='thecompetitors'>" . $arena_version . "</span></p>";
      echo "<p class='nucompetitors'><strong>".__('Number of competitors:','showdownplugin')."</strong><span class='thecompetitors'>" . $number_of_competitors . "</span></p>";
	  echo "<p class='nucompetitors'><strong>".__('Total ShowDown votes:','showdownplugin')."</strong><span class='thecompetitors'>" . $total_HoN_votes . "</span></p>";
	  echo '</div>';
	  echo "<div id='inner' class='showdownrss buyshowdown'>";
	  echo '<p class="thefeedtitle">'.__('Upgrade to ShowDown Pro','showdownplugin').'</p>';
	  echo '<p>'.__('Get features like:','showdownplugin').'</p>';
	  echo "<ul>";
          echo '<li><strong>Battle Mode:</strong> Create contests to pit one Competitor against another. Highly addictive to visitors and increases your pageviews!</li>';
		  echo '<li><strong>Anti-cheat:</strong> Limit votes by IP address so your Contests and Battles are not spammed.</li>';
		  echo '<li><strong>Affiliate Link:</strong> Add an affilaite link to your featured image, a great way to potentially make some money.</li>';
		  echo '<li><strong>Advanced Widget:</strong> Show winners/losers from specific Groups.</li>';
		  echo '<li><strong>Scrub Data:</strong> Enables you to reset all your data.</li>';
		  echo '<li><strong>Other Notable Features:</strong> Tweet to twitter, show/hide IP address, turn ON/OFF Group name and more...!</li>';
        echo "</ul>";
		echo '<p><a href="http://showdownplugin.com/download/" class="btn btn-danger">'.__('Buy ShowDown Pro','showdownplugin').'</a></p>';
	  echo '</div>';
	  echo '<div class="showdownrss">';
	  echo '<p class="thefeedtitle">'.__('Plugin News','showdownplugin').'</p>';
	  
    if ( isset($rss->items) && 0 < count($rss->items) ) {
        $rss->items = array_slice($rss->items, 0, 3);
        echo "<ul>";
        foreach ($rss->items as $item ) {
          echo '<li><a href="'. wp_filter_kses($item['link']),'">'.wptexturize(wp_specialchars($item['title'])).'</a></li>';
        } 
        echo "</ul>";
        } else {
          _e('No news found ..','showdownplugin');
        }	  
	  
	  echo '</div>';
	  echo '<div class="showdownrss">';
	  echo '<p class="thefeedtitle">'.__('ShowDown Add Ons','showdownplugin').'</p>';
	  
		echo "<ul>";
          echo '<li><a href="http://showdownplugin.com/add-ons">'.__('User Submit','showdownplugin').'</a>: Allow your visitors and users to submit competitors</li>';
        echo "</ul>"; 
		
		echo '</div>';
		
	   echo '<div class="showdownrss">';
		 echo '<p class="thefeedtitle">'.__('Related Products','showdownplugin').'</p>';
	
        echo "<ul>";
          echo '<li><a href="http://www.wpauctions.com">'.__('WP Auctions','showdownplugin').'</a>: Auction plugin for WordPress</li>';
		  echo '<li><a href="http://www.weborithm.com/themespace">'.__('ThemeSpace','showdownplugin').'</a>: WordPress &amp; HTML themes club</li>';
        echo "</ul>"; 
	  
	  echo '</div></div>';
    }

		//Prints out the admin page
		function printAdminPage() {
					$wpShowdownOptions = $this->getOptions();
										
					if (isset($_POST['update_wpShowdownSettings'])) { 
						$wpShowdownOptions['height'] = $_POST['wpShowdownHeight'];
						$wpShowdownOptions['width'] = $_POST['wpShowdownWidth'];
						$wpShowdownOptions['HONheight'] = $_POST['wpHONHeight'];
						$wpShowdownOptions['HONwidth'] = $_POST['wpHONWidth'];
						$wpShowdownOptions['showFB'] = $_POST['wpShowFB'];
						$wpShowdownOptions['showIP'] = $_POST['wpShowIP'];
						$wpShowdownOptions['showGP'] = $_POST['wpShowGP'];
						$wpShowdownOptions['txtTransition'] = $_POST['wpTxtTransition'];

						update_option($this->adminOptionsName, $wpShowdownOptions);
						
						?>
            <div class="updated"><p><strong><?php _e("Settings Updated.", "showdownplugin");?></strong></p></div>
					<?php
					} ?>

          <div class="wrap showdownplugininner submitpadding imagehelp">
          <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

          <h2><?php _e("ShowDown Options", "showdownplugin");?></h2>
          
          <h3><?php _e("Image Height", "showdownplugin");?></h3>
          <p><?php _e("Set the maximum height for your ShowDown images", "showdownplugin");?></p>
          <p><input type="text" name="wpHONHeight" id="wpHONHeight" value="<?php echo $wpShowdownOptions['HONheight']; ?>"></p>

          <h3><?php _e("Image Width", "showdownplugin");?></h3>
          <p><?php _e("Set the maximum width for your ShowDown images", "showdownplugin");?></p>
          <p><input type="text" name="wpHONWidth" id="wpHONWidth" value="<?php echo $wpShowdownOptions['HONwidth']; ?>"></p>

          <h2><?php _e("Common Options", "showdownplugin");?></h2>

          <p><?php _e("Show the option to Share to Facebook", "showdownplugin");?></p>
          <p><select name="wpShowFB" id="wpShowFB">
            <option value="Yes"<?php if ($wpShowdownOptions['showFB'] == "Yes") echo " selected" ?>><?php _e("Yes", "showdownplugin");?></option>
            <option value=""<?php if ($wpShowdownOptions['showFB'] != "Yes") echo " selected" ?>><?php _e("No", "showdownplugin");?></option>
          </select> 
          </p>

          <h2><?php _e("Transition Message", "showdownplugin");?></h2>
		  <p><?php _e("This will show a message to block your users from any interaction on your contest page while it reloads.", "showdownplugin");?></p>
          <p><?php _e("Leave blank if you don't need a transition", "showdownplugin");?></p>
          <p><input type="text" name="wpTxtTransition" id="wpTxtTransition" value="<?php echo $wpShowdownOptions['txtTransition']; ?>"></p>

          <div class="submit">
          <input type="submit" name="update_wpShowdownSettings" value="<?php _e("Update All Settings", "showdownplugin");?>" />
		      </div>
		      </form>

		  
		  <h2><?php _e("Quick Help", "showdownplugin");?></h2>
		  <p><strong><?php _e("Where do I enter my images?", "showdownplugin");?></strong></p>
		  <p><?php _e("Look on your left menu, you should see a new custom post type called Competitors. Simply click on Add New, upload an image, set it as the featured image (your theme should support this, if not please contact your theme designer for support), add a tag and click Publish.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("Can I enter text in the Write panel for my Competitors?", "showdownplugin");?></strong></p>
		  <p><?php _e("Sure you can, you can even enter videos and a whole gallery of images. But you can only set ONE image as featured and ONLY that image will show up in your contests. You can however change the image whenever you like by uploading a new one and featuring it.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("How do I show the ShowDown contests?", "showdownplugin");?></strong></p>
		  <p><?php _e("Simply create a regular Post or Page where you will see a new a custom button with a lightning bolt (our logo) <img src='../wp-content/plugins/showdown/css/icon.png'>, click on that button to view the options and click Insert Shortcode.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("How many ShowDown shortcodes can I put in one Post/Page?", "showdownplugin");?></strong></p>
		  <p><?php _e("Just One. This includes your Archives. If you show more than two per Post/Page/Archive the voting mechanism will not work.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("I entered shortcodes but they all show up on my home page! What do I do?", "showdownplugin");?></strong></p>
		  <p><?php _e("Use the \"Insert More Tag feature\" available on the WordPress write panel to break the content away from the shortcodes and update your Posts/Pages.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("Can I enter the shortcodes in my sidebar?", "showdownplugin");?></strong></p>
		  <p><?php _e("Yes, but your theme <em>may</em> need some additional CSS styling. Please contact your theme designer for design support.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("Can you customise this plugin for me? I want more features!", "showdownplugin");?></strong></p>
		  <p><?php _e("Sorry, we don't do customisations. But we do take requests to build more features. Please visit our website to leave a comment.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("Can I make money with this plugin?", "showdownplugin");?></strong></p>
		  <p><?php _e("Sure, upgrade to the <a href=\"http://showdownplugin.com/download/\">ShowDown plugin</a>, to insert affiliate links and potentially make some money. No guarantee of revenue generation is given or implied.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("I don't see any Competitors and the URL does not work!", "showdownplugin");?></strong></p>
		  <p><?php _e("Please visit your \"Settings > Permalinks\" panel in WordPress and click on Save Changes. You should now be able to see your Competitors.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("Sometimes, my Competitor images keep repeating in contests?", "showdownplugin");?></strong></p>
		  <p><?php _e("The plugin works best with a minimum of 10 or so images in the database. All images are chosen randomly, we felt that would be the fairest method of showing them.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("Where can I view stats?", "showdownplugin");?></strong></p>
		  <p><?php _e("The main Competitor admin panel shows the Views, Points and Rating statistics.", "showdownplugin");?></p>
		  
		  <p><strong><?php _e("How can I adjust the image width and height?", "showdownplugin");?></strong></p>
		  <p><?php _e("The setting for this is right above on this page. We recommend a uniform square proportion, though there is no problem in specifying a rectagular proportion as long as your images can be formatted that way.", "showdownplugin");?></p>
		  
           </div>
          <?php
		}//End function printAdminPage()


    function printDesign() {

      $wpShowdownOptions = $this->getOptions();
        
      if (isset($_POST['update_wpShowdownSettings'])) { 
		$wpShowdownOptions['HONCapTopGradient'] = $_POST['wpHONCapTopGradient'];
        $wpShowdownOptions['HONCapBottomGradient'] = $_POST['wpHONCapBottomGradient'];
        $wpShowdownOptions['HONBackTopGradient'] = $_POST['wpHONBackTopGradient'];
        $wpShowdownOptions['HONBackBottomGradient'] = $_POST['wpHONBackBottomGradient'];
        $wpShowdownOptions['HONNumberHover'] = $_POST['wpHONNumberHover'];
        $wpShowdownOptions['HONNumberStatic'] = $_POST['wpHONNumberStatic'];

        update_option($this->adminOptionsName, $wpShowdownOptions);
        
        ?>
        <div class="updated"><p><strong><?php _e("Settings Updated.", "showdownplugin");?></strong></p></div>
        <?php
       } 
      
       // implement fudge for people upgrading having blank values and fabtastic doesn't handle blank fields very well
       if ( $wpShowdownOptions['HONCapTopGradient'] == "" ) $wpShowdownOptions['HONCapTopGradient'] = "#7d7e7d";
       if ( $wpShowdownOptions['HONCapBottomGradient'] == "" ) $wpShowdownOptions['HONCapBottomGradient'] = "#0e0e0e";
	   if ( $wpShowdownOptions['HONBackTopGradient'] == "" ) $wpShowdownOptions['HONBackTopGradient'] = "#ffff88";
       if ( $wpShowdownOptions['HONBackBottomGradient'] == "" ) $wpShowdownOptions['HONBackBottomGradient'] = "#ffe772";
       if ( $wpShowdownOptions['HONNumberHover'] == "" ) $wpShowdownOptions['HONNumberHover'] = "#ffffff";
       if ( $wpShowdownOptions['HONNumberStatic'] == "" ) $wpShowdownOptions['HONNumberStatic'] = "#000000";	
      
    ?>

    <script type="text/javascript">
      jQuery(document).ready(function() {


       // Initialise Colour Picker
            var f = jQuery.farbtastic('#picker');
            var p = jQuery('#picker').css('opacity', 0.25);
            var selected;
            jQuery('.colorwell')
              .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
              .focus(function() {
                if (selected) {
                  jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
                }
                f.linkTo(this);
                p.css('opacity', 1);
                jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
              });

        jQuery('#reset').click(function() {
          jQuery(':input').each(function() {
             if (this.type == 'text')
                this.value = "";
          });
          jQuery('#form1').submit();
        });

      });

    </script>

    <div class="wrap showdownplugininner submitpadding showdownstyling">
	
	<h2><?php _e("ShowDown Color Settings", "showdownplugin");?></h2>
	
	<h3><?php _e("Preview", "showdownplugin");?></h3>
	<p><?php _e("Save your settings below to update the preview area.", "showdownplugin");?></p>
	<style>
		a.hon-preview { text-decoration: none; padding: 5px 10px; color: <?php echo $wpShowdownOptions['HONNumberStatic']; ?>; 
		background-image: -moz-linear-gradient(top, <?php echo $wpShowdownOptions['HONBackTopGradient']; ?>, <?php echo $wpShowdownOptions['HONBackBottomGradient']; ?>); 
	 background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $wpShowdownOptions['HONBackTopGradient']; ?>), to(<?php echo $wpShowdownOptions['HONBackBottomGradient']; ?>)); 
	 background-image: -webkit-linear-gradient(top, <?php echo $wpShowdownOptions['HONBackTopGradient']; ?>, <?php echo $wpShowdownOptions['HONBackBottomGradient']; ?>); 
	 background-image: -o-linear-gradient(top, <?php echo $wpShowdownOptions['HONBackTopGradient']; ?>, <?php echo $wpShowdownOptions['HONBackBottomGradient']; ?>); 
	 background-image: linear-gradient(to bottom, <?php echo $wpShowdownOptions['HONBackTopGradient']; ?>, <?php echo $wpShowdownOptions['HONBackBottomGradient']; ?>); }
		a.hon-preview:hover { color: <?php echo $wpShowdownOptions['HONNumberHover']; ?>; 
		background-image: -moz-linear-gradient(top, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>); 
	 background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $wpShowdownOptions['HONCapTopGradient']; ?>), to(<?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>)); 
	 background-image: -webkit-linear-gradient(top, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>); 
	 background-image: -o-linear-gradient(top, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>); 
	 background-image: linear-gradient(to bottom, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>);
		}
	</style>
	<div style="margin: 0 0 20px;"><p style="
	 background-image: -moz-linear-gradient(top, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>); 
	 background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $wpShowdownOptions['HONCapTopGradient']; ?>), to(<?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>)); 
	 background-image: -webkit-linear-gradient(top, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>); 
	 background-image: -o-linear-gradient(top, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>); 
	 background-image: linear-gradient(to bottom, <?php echo $wpShowdownOptions['HONCapTopGradient']; ?>, <?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>); padding: 5px 15px; display: inline; color: #fff;">Text Preview</p> / <p style="display: inline;"><a href="#" class="hon-preview">Numbers Preview</a></p></div>
	
	<div id="picker"></div>
	
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" class="wp-showdown-colorpicker">

	<p><em><?php _e("NOTE: To reset your colors, simply clear the fields below and click Update Settings.", "showdownplugin");?></em></p>
    <h3><?php _e("Text Top Gradient", "showdownplugin");?></h3>
    <p><?php _e("Select a top gradient for the ShowDown captions:", "showdownplugin");?></p>
    <p><input class="colorwell" type="text" name="wpHONCapTopGradient" id="wpHONCapTopGradient" value="<?php echo $wpShowdownOptions['HONCapTopGradient']; ?>"></p>

    <h3><?php _e("Text Bottom Gradient", "showdownplugin");?></h3>
    <p><?php _e("Select a bottom gradient for the ShowDown captions:", "showdownplugin");?></p>
    <p><input class="colorwell" type="text" name="wpHONCapBottomGradient" id="wpHONCapBottomGradient" value="<?php echo $wpShowdownOptions['HONCapBottomGradient']; ?>"></p>
    
    <h3><?php _e("Numbers Top Gradient", "showdownplugin");?></h3>
    <p><?php _e("Select a top gradient for the ShowDown numbers:", "showdownplugin");?></p>
    <p><input class="colorwell" type="text" name="wpHONBackTopGradient" id="wpHONBackTopGradient" value="<?php echo $wpShowdownOptions['HONBackTopGradient']; ?>"></p>

    <h3><?php _e("Numbers Bottom Gradient", "showdownplugin");?></h3>
    <p><?php _e("Select a bottom gradient for the ShowDown numbers:", "showdownplugin");?></p>
    <p><input class="colorwell" type="text" name="wpHONBackBottomGradient" id="wpHONBackBottomGradient" value="<?php echo $wpShowdownOptions['HONBackBottomGradient']; ?>"></p>

    <h3><?php _e("Number colour (hover)", "showdownplugin");?></h3>
    <p><?php _e("Select a hover colour for the numbers:", "showdownplugin");?></p>
    <p><input class="colorwell" type="text" name="wpHONNumberHover" id="wpHONNumberHover" value="<?php echo $wpShowdownOptions['HONNumberHover']; ?>"></p>

    <h3><?php _e("Number colour (static)", "showdownplugin");?></h3>
    <p><?php _e("Select a static colour for the numbers:", "showdownplugin");?></p>
    <p><input class="colorwell" type="text" name="wpHONNumberStatic" id="wpHONNumberStatic" value="<?php echo $wpShowdownOptions['HONNumberStatic']; ?>"></p>

    <div class="submit">
		<input type="submit" name="update_wpShowdownSettings" value="<?php _e("Update Settings", "showdownplugin");?>" />
    </div>
    </form>    

    </div>

    <?php
    } // end function printDesign()


		//Prints out the admin page
		function printCustomPage() {
					$wpShowdownOptions = $this->getOptions();
						
            if (isset($_POST['update_wpShowdownSettings'])) { 
              $wpShowdownOptions['header'] = $_POST['wpShowdownHeader'];
              $wpShowdownOptions['resultdrawn'] = $_POST['wpResultDrawn'];
              $wpShowdownOptions['resultwin1'] = $_POST['wpResultWin1'];
              $wpShowdownOptions['resultwin2'] = $_POST['wpResultWin2'];
              $wpShowdownOptions['notvoted'] = $_POST['wpNotVoted'];
              $wpShowdownOptions['assigndraw'] = $_POST['wpAssignDraw'];
              $wpShowdownOptions['headingresult'] = $_POST['wpHeadingResult'];
              $wpShowdownOptions['headingwinner'] = $_POST['wpHeadingWinner'];
              $wpShowdownOptions['headingloser'] = $_POST['wpHeadingLoser'];
              $wpShowdownOptions['headingdrawn'] = $_POST['wpHeadingDrawn'];
              $wpShowdownOptions['haswon'] = $_POST['wpHasWon'];
              $wpShowdownOptions['average'] = $_POST['wpAverage'];
              $wpShowdownOptions['hasvoted'] = $_POST['wpHasVoted'];
              $wpShowdownOptions['assigndraw'] = $_POST['wpAssignDraw'];
              $wpShowdownOptions['hotcaption'] = $_POST['wpHotCaption'];
              $wpShowdownOptions['notcaption'] = $_POST['wpNotCaption'];

              update_option($this->adminOptionsName, $wpShowdownOptions);
              
              ?>
              <div class="updated"><p><strong><?php _e("Settings Updated.", "showdownplugin");?></strong></p></div>
            <?php
            } ?>

			<div class="wrap showdownplugininner withbg">
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

            <h2><?php _e("ShowDown Customisation", "showdownplugin");?></h2>

            <h3><?php _e("Voted Caption", "showdownplugin");?></h3>
            <p><?php _e("Caption to show when user has voted", "showdownplugin");?></p>
            <p><input type="text" name="wpHasVoted" id="wpHasVoted" value="<?php echo $wpShowdownOptions['hasvoted']; ?>"></p>

            <h3><?php _e("Vote Average", "showdownplugin");?></h3>
            <p><?php _e("How do we show the average? (example: <em>Average</em>: xxx)", "showdownplugin");?></p>
            <p><input type="text" name="wpAverage" id="wpAverage" value="<?php echo $wpShowdownOptions['average']; ?>"></p>

            <h3><?php _e("Skip Image", "showdownplugin");?></h3>
            <p><?php _e("User cannot decide on a winner", "showdownplugin");?></p>
            <p><input type="text" name="wpAssignDraw" id="wpAssignDraw" value="<?php echo $wpShowdownOptions['assigndraw']; ?>"></p>

            <h3><?php _e("Yay Caption", "showdownplugin");?></h3>
            <p><?php _e("What's at the top of the scale? (Yay)", "showdownplugin");?></p>
            <p><input type="text" name="wpHotCaption" id="wpHotCaption" value="<?php echo $wpShowdownOptions['hotcaption']; ?>"></p>

            <h3><?php _e("Nay Caption", "showdownplugin");?></h3>
            <p><?php _e("What's at the bottom of the scale? (Nay)", "showdownplugin");?></p>
            <p><input type="text" name="wpNotCaption" id="wpNotCaption" value="<?php echo $wpShowdownOptions['notcaption']; ?>"></p>

					<div class="submit">
					<input type="submit" name="update_wpShowdownSettings" value="<?php _e("Update Settings", "showdownplugin");?>" />
					</div>
				</form>
            </div>

            <?php
           
				}//End function printCustomPage()

		//Function that populates header
		function addHeaderCode() {
		   $showdownOptions = $this->getOptions();
		   
       echo '<!-- ShowDown is running -->';
       echo '<link type="text/css" rel="stylesheet" href="' . WPSDOWN_URL . '/css/wp-showdown.css.php" />' . "\n\n";
		}

    // Function that populated admin scripts
    function showdown_admin_scripts() {
      if (isset($_GET['page']) && $_GET['page'] == 'styling') {
         wp_enqueue_script('farbtastic');
      }
    }

    // Function that populated admin styles
    function showdown_admin_styles() {

       wp_enqueue_style('squadra', 'http://fonts.googleapis.com/css?family=Squada+One');
       wp_enqueue_style('duru', 'http://fonts.googleapis.com/css?family=Duru+Sans');
       wp_enqueue_style('showdownadminstyle', WPSDOWN_URL . '/css/plugin.css' );

      if (isset($_GET['page']) && $_GET['page'] == 'styling') {
        wp_enqueue_style( 'farbtastic' );
      }
    }
 
	} //End Class      
} // end If Class Exists

// Initialise Class here
if (class_exists("WPShowdown")) {
	$wp_showdown = new WPShowdown();
}

//Initialize the admin panel
if (!function_exists("WPShowdown_ap")) {
	function WPShowdown_ap() {
		global $wp_showdown;
		if (!isset($wp_showdown)) {
			return;
		}

    // define menus here
    add_menu_page ( 'ShowDown', 'ShowDown', 'manage_options', basename(__FILE__), array(&$wp_showdown, 'printTitlePage'));
    add_submenu_page (  basename(__FILE__), 'Settings', 'Settings', 'manage_options', 'settings', array(&$wp_showdown, 'printAdminPage'));
    add_submenu_page (  basename(__FILE__), 'Customise', 'Customise', 'manage_options', 'customise', array(&$wp_showdown, 'printCustomPage'));
    add_submenu_page (  basename(__FILE__), 'Styling', 'Styling', 'manage_options', 'styling', array(&$wp_showdown, 'printDesign'));

	}	
}

//Actions and Filters	
if (isset($wp_showdown)) {
	//Actions
	add_action('wp_head', array(&$wp_showdown, 'addHeaderCode'), 1);
	add_action('activate_wpshowdown/wpshowdown.php',  array(&$wp_showdown, 'init'));	
	add_action('admin_menu', 'WPShowdown_ap'); 
  add_action('admin_print_scripts', array(&$wp_showdown, 'showdown_admin_scripts'));
  add_action('admin_print_styles', array(&$wp_showdown,'showdown_admin_styles'));

	//Filters
	
	// add support for featured images .. in case theme doesn't already have this
	add_theme_support( 'post-thumbnails' );

}

?>