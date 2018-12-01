<?php header("Content-type: text/css"); 

// hook into Wordpress and pull out the options
if (!function_exists('get_option')) 
   require_once('../../../../wp-config.php'); 

$options = get_option('WPShowdownOptions');

$HONCapTopGradient = empty($options['HONCapTopGradient']) ? '#7d7e7d' : $options['HONCapTopGradient'];
$HONCapBottomGradient = empty($options['HONCapBottomGradient']) ? '<?php echo HONCapBottomGradient; ?>' : $options['HONCapBottomGradient'];
$HONBackTopGradient = empty($options['HONBackTopGradient']) ? '#ffff88' : $options['HONBackTopGradient'];
$HONBackBottomGradient = empty($options['HONBackBottomGradient']) ? '#ffe772' : $options['HONBackBottomGradient'];
$HONNumberHover = empty($options['HONNumberHover']) ? '#ffffff' : $options['HONNumberHover'];
$HONNumberStatic = empty($options['HONNumberStatic']) ? '#000000' : $options['HONNumberStatic'];

?>

/*
ShowDown Plugin
Style Name: ShowDownPlugin
Style Author: Hyder Jaffari
Author URL: http://www.weborithm.com
Last Update: June 29th, 2012
*/

.wp-showdown-container:after, .wp-showdown-results:after, #form_arena:after, .wp-showdown-battleimages:after { clear: both; content: " "; display: block; line-height: 0; height: 0; visibility: hidden; }
.wp-showdown-container {  border-bottom: 1px dashed #eee; margin: 0 0 20px; padding: 20px 0 0; width: 100%; }
.wp-showdown-container h2, .wp-showdown-results h2 { background: none; border: 0; border-bottom: 1px dashed #eee; font-size: 20px; margin: 0 0 20px; padding: 0 0 20px; text-align: center; }
.wp-showdown-image { float: left; text-align: center; width: 50%; }
.wp-showdown-image:hover h3 a { background: url(select.png) no-repeat 0 3px; padding: 0 0 0 25px }
.wp-showdown-image h3, .wp-showdown-results h3 { background: none; border: 0; font-size: 20px; margin: 0; padding: 0 0 20px; }
.wp-showdown-draw { float: left; font-size: 14px; padding: 0 0 20px; text-align: center; width: 100%; }

.wp-showdown-results { padding: 0 0 20px 0; width: 100%; }
.wp-showdown-loser, .wp-showdown-winner, .wp-showdown-drawn { float: left; text-align: center; width: 50%; }
.wp-showdown-results .attachment-thumbnail { background: none; border: 0; float: none; height: auto; margin: 0; padding: 0; }
.sidebar .wp-showdown-results .attachment-thumbnail, .aside .wp-showdown-results .attachment-thumbnail { width: 90%; }
.post .wp-showdown-results .attachment-thumbnail { width: auto; }
.wp-showdown-results p { background: none; border: 0; color: #999; font-size: 10px; padding: 0; text-transform: uppercase; }
.wp-showdown-results h3 { padding: 0 0 20px; }
.wp-showdown-winner h3 span { background: url(winner.png) no-repeat; padding: 2px 0 4px 40px; }
.wp-showdown-loser h3 span { background: url(loser.png) no-repeat; padding: 2px 0 4px 40px; }
.wp-showdown-drawn h3 span { background: url(draw.png) no-repeat; padding: 2px 0 4px 40px; }

.wp-showdown-content { padding: 0; position: relative; }
.wp-showdown-content a { display: block;  }

.wp-showdown-content p { padding: 0; }
.wp-showdown-container .wp-showdown-content p { padding: 0 10px; }
.wp-showdown-content p a { display: inline; }

.wp-showdown-select { bottom: 10px; display: inline-block; height: 30px; width: 34px; top: auto; position: absolute; z-index: 100; }
.wp-showdown-content:hover .wp-showdown-select { background: url(select.png); }

.wp-showdown-announcement h2 { background: none; border: 0; font-size: 20px; margin: 0; padding: 20px 0; text-align: center; }
.wp-showdown-announcement h2 span { background: url(ribbon.png) no-repeat; padding: 15px 0 19px 52px; }

.wp-showdown-stats { padding: 0 0 20px; }
.wp-showdown-stats p { display: inline-block; padding: 0 20px 0 0; }

p.wp-showdown-caption { color: #999; font-size: 10px; padding: 0; text-transform: uppercase; }
p.wp-showdown-notvoted { text-align: center; }

/* Hot or Not */
.wp-showdown-hotornot { padding: 20px 0; position: relative; }
.wp-showdown-hotornot .wp-showdown-votehere .wp-showdown-content { padding: 0; }

/* Vote Here */
.wp-showdown-votehere { text-align: center; }

.wp-showdown-votenumbers { margin: 0 0 1px; padding: 0; position: relative; }

/* Gradient 1 */
.wp-showdown-votenumbers span.wp-showdown-captionhot, .wp-showdown-votenumbers span.wp-showdown-captionnot, .wp-showdown-votehere span.wp-showdown-voteclass a:hover, .wp-showdown-votehere .wp-showdown-draw, .wp-showdown-youvoted p.wp-showdown-youvotedavg, .wp-showdown-votehere .wp-showdown-votedraw { background: <?php echo $HONCapTopGradient; ?>; /* Old browsers */
background: -moz-linear-gradient(top, <?php echo $HONCapTopGradient; ?> 0%, <?php echo $HONCapBottomGradient; ?> 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $HONCapTopGradient; ?>), color-stop(100%,<?php echo HONCapBottomGradient; ?>)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, <?php echo $HONCapTopGradient; ?> 0%,<?php echo $HONCapBottomGradient; ?> 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, <?php echo $HONCapTopGradient; ?> 0%,<?php echo $HONCapBottomGradient; ?> 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top, <?php echo $HONCapTopGradient; ?> 0%,<?php echo $HONCapBottomGradient; ?> 100%); /* IE10+ */
background: linear-gradient(top, <?php echo $HONCapTopGradient; ?> 0%,<?php echo $HONCapBottomGradient; ?> 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $HONCapTopGradient; ?>', endColorstr='<?php echo $HONCapBottomGradient; ?>',GradientType=0 ); /* IE6-9 */ }

/* Gradient 2 */
.wp-showdown-votehere span.wp-showdown-voteclass, .wp-showdown-votehere .wp-showdown-draw:hover, .wp-showdown-votehere .wp-showdown-votedraw:hover { background: <?php echo $HONBackTopGradient; ?>; /* Old browsers */
background: -moz-linear-gradient(top, <?php echo $HONBackTopGradient; ?> 0%, <?php echo $HONBackBottomGradient; ?> 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $HONBackTopGradient; ?>), color-stop(100%,<?php echo $HONBackBottomGradient; ?>)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, <?php echo $HONBackTopGradient; ?> 0%,<?php echo $HONBackBottomGradient; ?> 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, <?php echo $HONBackTopGradient; ?> 0%,<?php echo $HONBackBottomGradient; ?> 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top, <?php echo $HONBackTopGradient; ?> 0%,<?php echo $HONBackBottomGradient; ?> 100%); /* IE10+ */
background: linear-gradient(top, <?php echo $HONBackTopGradient; ?> 0%,<?php echo $HONBackBottomGradient; ?> 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $HONBackTopGradient; ?>', endColorstr='<?php echo $HONBackBottomGradient; ?>',GradientType=0 ); /* IE6-9 */ }

/* "Hot" and "Not" captions */
.wp-showdown-votenumbers span.wp-showdown-captionhot, .wp-showdown-votenumbers span.wp-showdown-captionnot { color: <?php echo $HONNumberHover; ?>; display: inline-block; font-size: 14px; margin: 0; padding: 5px 10px; text-transform: uppercase; }

/* Vote Numbers */
.wp-showdown-votehere span.wp-showdown-voteclass { display: inline-block; }

.wp-showdown-votehere span.wp-showdown-voteclass a { border: 1px solid <?php echo $HONBackBottomGradient; ?>; color: <?php echo $HONNumberStatic; ?>; display: inline-block; font-size: 14px; padding: 4px 10px; text-decoration: none; }

.wp-showdown-votehere span.wp-showdown-voteclass a:hover { border: 1px solid <?php echo $HONCapBottomGradient; ?>; color: <?php echo $HONNumberHover; ?>; text-decoration: none; /* Old browsers */ }

.wp-showdown-votehere .wp-showdown-draw, .wp-showdown-votehere .wp-showdown-votedraw { float: none; margin: 20px auto; padding: 0; width: 25%; -moz-border-radius: 3px; -webkit-border-radius: 3px; border: 1px solid <?php echo $HONCapBottomGradient; ?>; }

.wp-showdown-votehere .wp-showdown-draw a, .wp-showdown-votehere .wp-showdown-votedraw a { color: <?php echo $HONNumberHover; ?>; display: block; padding: 5px 10px 7px; text-decoration: none !important; }
.wp-showdown-votehere .wp-showdown-draw:hover, .wp-showdown-votehere .wp-showdown-votedraw:hover { border: 1px solid <?php echo $HONBackBottomGradient; ?>; }

.wp-showdown-votehere .wp-showdown-draw:hover a, .wp-showdown-votehere .wp-showdown-votedraw:hover a { color: <?php echo $HONNumberStatic; ?>; text-decoration: none; }

p.wp-showdown-voteherecaption { font-size: 10px; font-weight: bold; letter-spacing: 0.5px; margin: auto; padding: 0; text-transform: uppercase; }

/* Voted */
.wp-showdown-youvoted { padding: 0 0 20px; text-align: center; }
.wp-showdown-youvotedimg a { display: block; height: 100px; box-shadow: none !important; }
.wp-showdown-youvotedimg img { display: block; margin: auto; }
.wp-showdown-youvoted p.wp-showdown-youvotedvote { font-size: 10px; margin: 0; padding: 0 0 10px; width: auto; }
.wp-showdown-youvoted p.wp-showdown-youvotedvote span { color: #EA0E0E; font-weight: bold; }

.wp-showdown-youvoted p.wp-showdown-youvotedavg { color: <?php echo $HONNumberHover; ?>; font-size: 9px; font-weight: bold; letter-spacing: 0.5px; margin: 0 auto; padding: 5px 0; text-align: center; width: 100px; text-transform: uppercase; }

/* Sharing Links */
.wp-showdown-sharing { text-align: center; }
span.wp-showdown-facebook { background: url(../images/facebook.png) no-repeat; display: inline-block; font-size: 11px; padding: 0 0 10px 21px; text-transform: uppercase; }
span.wp-showdown-twitter { background: url(../images/twitter.png) no-repeat; display: inline-block; font-size: 11px; padding: 0 0 10px 21px; text-transform: uppercase; }

/* Group Name */
.wp-showdown-groupname { text-align: center; }

/* Block Message */
.blockMsg { font-size: 14px; }