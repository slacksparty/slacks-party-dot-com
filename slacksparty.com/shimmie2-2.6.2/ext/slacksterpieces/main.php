<?php
/**
 * Name: Slacksterpieces
 * Author: SlacksParty <clark@slacksparty.com>
 * License: GPLv2
 * Description: The search for the one true slacksterpiece continues

// /*
//  * SearchTermParseEvent:
//  * Signal that a search term needs parsing
//  */
// class SearchTermParseEvent extends Event {
// 	/** @var null|string  */
// 	public $term = null;
// 	/** @var string[] */
// 	public $context = array();
// 	/** @var \Querylet[] */
// 	public $querylets = array();

// 	/**
// 	 * @param string|null $term
// 	 * @param string[] $context
// 	 */
// 	public function __construct($term, array $context) {
// 		$this->term = $term;
// 		$this->context = $context;
// 	}

// 	/**
// 	 * @return bool
// 	 */
// 	public function is_querylet_set() {
// 		return (count($this->querylets) > 0);
// 	}

// 	/**
// 	 * @return \Querylet[]
// 	 */
// 	public function get_querylets() {
// 		return $this->querylets;
// 	}

// 	/**
// 	 * @param \Querylet $q
// 	 */
// 	public function add_querylet($q) {
// 		$this->querylets[] = $q;
// 	}
// }

// class SearchTermParseException extends SCoreException {
// }

// class PostListBuildingEvent extends Event {
// 	/** @var array */
// 	public $search_terms = array();

// 	/** @var array */
// 	public $parts = array();

// 	/**
// 	 * @param string[] $search
// 	 */
// 	public function __construct(array $search) {
// 		$this->search_terms = $search;
// 	}

// 	/**
// 	 * @param string $html
// 	 * @param int $position
// 	 */
// 	public function add_control(/*string*/ $html, /*int*/ $position=50) {
// 		while(isset($this->parts[$position])) $position++;
// 		$this->parts[$position] = $html;
// 	}
// }

class Slacksterpieces extends Extension {
    /** @var int */
	private $stpen = 0;  // search term parse event number

	public function onInitExt(InitExtEvent $event) {
		global $config;
		$config->set_default_int("slackster_images", 24);
		$config->set_default_bool("slackster_tips", true);
		$config->set_default_string("slackster_order", "id DESC");
	}

	public function onPageRequest(PageRequestEvent $event) {
		global $database, $page;
		if($event->page_matches("slacksterpieces")) {
			if(isset($_GET['search'])) {
				// implode(explode()) to resolve aliases and sanitise
				$search = url_escape(Tag::implode(Tag::explode($_GET['search'], false)));
				if(empty($search)) {
					$page->set_mode("redirect");
					$page->set_redirect(make_link("slacksterpieces/1"));
				}
				else {
					$page->set_mode("redirect");
					$page->set_redirect(make_link('slacksterpieces/1'));
				}
				return;
			}

			$search_terms = array("slacksterpieces");
			$page_number = $event->get_page_number();
			$page_size = $event->get_page_size();

			$count_search_terms = count($search_terms);

			try {
				#log_debug("index", "Search for ".implode(" ", $search_terms), false, array("terms"=>$search_terms));
				$total_pages = Image::count_pages($search_terms);
				if(SPEED_HAX && $count_search_terms === 0 && ($page_number < 10)) { // extra caching for the first few slacksterpieces pages
					$images = $database->cache->get("slacksterpieces:$page_number");
					if(!$images) {
						$images = Image::find_images(($page_number-1)*$page_size, $page_size, $search_terms);
						$database->cache->set("slacksterpieces:$page_number", $images, 60);
					}
				}
				else {
					$images = Image::find_images(($page_number-1)*$page_size, $page_size, $search_terms);
				}
			}
			catch(SearchTermParseException $stpe) {
				// FIXME: display the error somewhere
				$total_pages = 0;
				$images = array();
			}

			$count_images = count($images);

			$plbe = new PostListBuildingEvent($search_terms);
			send_event($plbe);

			$this->theme->set_page($page_number, $total_pages, $search_terms);
			$this->theme->display_page($page, $images);
			if(count($plbe->parts) > 0) {
				$this->theme->display_admin_block($plbe->parts);
			}

		}
	}

	public function onSetupBuilding(SetupBuildingEvent $event) {
		$sb = new SetupBlock("Slacksterpieces Options");
		$sb->position = 20;

		$sb->add_label("Show ");
		$sb->add_int_option("slackster_images");
		$sb->add_label(" images on the slacksterpieces list");

		$event->panel->add_block($sb);
	}

	public function onImageInfoSet(ImageInfoSetEvent $event) {
		global $database;
		if(SPEED_HAX) {
			$database->cache->delete("thumb-block:{$event->image->id}");
		}
	}

	public function onSearchTermParse(SearchTermParseEvent $event) {
		$matches = array();
		// check for tags first as tag based searches are more common.
		if(preg_match("/^tags([:]?<|[:]?>|[:]?<=|[:]?>=|[:|=])(\d+)$/i", $event->term, $matches)) {
			$cmp = ltrim($matches[1], ":") ?: "=";
			$count = $matches[2];
			$event->add_querylet(
				new Querylet("EXISTS (
				              SELECT 1
				              FROM image_tags it
				              LEFT JOIN tags t ON it.tag_id = t.id
				              WHERE images.id = it.image_id
				              GROUP BY image_id
				              HAVING COUNT(*) $cmp $count
				)")
			);
		}
		else if(preg_match("/^ratio([:]?<|[:]?>|[:]?<=|[:]?>=|[:|=])(\d+):(\d+)$/i", $event->term, $matches)) {
			$cmp = preg_replace('/^:/', '=', $matches[1]);
			$args = array("width{$this->stpen}"=>int_escape($matches[2]), "height{$this->stpen}"=>int_escape($matches[3]));
			$event->add_querylet(new Querylet("width / height $cmp :width{$this->stpen} / :height{$this->stpen}", $args));
		}
		else if(preg_match("/^(filesize|id)([:]?<|[:]?>|[:]?<=|[:]?>=|[:|=])(\d+[kmg]?b?)$/i", $event->term, $matches)) {
			$col = $matches[1];
			$cmp = ltrim($matches[2], ":") ?: "=";
			$val = parse_shorthand_int($matches[3]);
			$event->add_querylet(new Querylet("images.$col $cmp :val{$this->stpen}", array("val{$this->stpen}"=>$val)));
		}
		else if(preg_match("/^(hash|md5)[=|:]([0-9a-fA-F]*)$/i", $event->term, $matches)) {
			$hash = strtolower($matches[2]);
			$event->add_querylet(new Querylet('images.hash = :hash', array("hash" => $hash)));
		}
		else if(preg_match("/^(filetype|ext)[=|:]([a-zA-Z0-9]*)$/i", $event->term, $matches)) {
			$ext = strtolower($matches[2]);
			$event->add_querylet(new Querylet('images.ext = :ext', array("ext" => $ext)));
		}
		else if(preg_match("/^(filename|name)[=|:]([a-zA-Z0-9]*)$/i", $event->term, $matches)) {
			$filename = strtolower($matches[2]);
			$event->add_querylet(new Querylet("images.filename LIKE :filename{$this->stpen}", array("filename{$this->stpen}"=>"%$filename%")));
		}
		else if(preg_match("/^(source)[=|:](.*)$/i", $event->term, $matches)) {
			$source = strtolower($matches[2]);

			if(preg_match("/^(any|none)$/i", $source)){
				$not = ($source == "any" ? "NOT" : "");
				$event->add_querylet(new Querylet("images.source IS $not NULL"));
			}else{
				$event->add_querylet(new Querylet('images.source LIKE :src', array("src"=>"%$source%")));
			}
		}
		else if(preg_match("/^posted([:]?<|[:]?>|[:]?<=|[:]?>=|[:|=])([0-9-]*)$/i", $event->term, $matches)) {
			$cmp = ltrim($matches[1], ":") ?: "=";
			$val = $matches[2];
			$event->add_querylet(new Querylet("images.posted $cmp :posted{$this->stpen}", array("posted{$this->stpen}"=>$val)));
		}
		else if(preg_match("/^size([:]?<|[:]?>|[:]?<=|[:]?>=|[:|=])(\d+)x(\d+)$/i", $event->term, $matches)) {
			$cmp = ltrim($matches[1], ":") ?: "=";
			$args = array("width{$this->stpen}"=>int_escape($matches[2]), "height{$this->stpen}"=>int_escape($matches[3]));
			$event->add_querylet(new Querylet("width $cmp :width{$this->stpen} AND height $cmp :height{$this->stpen}", $args));
		}
		else if(preg_match("/^width([:]?<|[:]?>|[:]?<=|[:]?>=|[:|=])(\d+)$/i", $event->term, $matches)) {
			$cmp = ltrim($matches[1], ":") ?: "=";
			$event->add_querylet(new Querylet("width $cmp :width{$this->stpen}", array("width{$this->stpen}"=>int_escape($matches[2]))));
		}
		else if(preg_match("/^height([:]?<|[:]?>|[:]?<=|[:]?>=|[:|=])(\d+)$/i", $event->term, $matches)) {
			$cmp = ltrim($matches[1], ":") ?: "=";
			$event->add_querylet(new Querylet("height $cmp :height{$this->stpen}",array("height{$this->stpen}"=>int_escape($matches[2]))));
		}
		else if(preg_match("/^order[=|:](id|width|height|filesize|filename)[_]?(desc|asc)?$/i", $event->term, $matches)){
			$ord = strtolower($matches[1]);
			$default_order_for_column = preg_match("/^(id|filename)$/", $matches[1]) ? "ASC" : "DESC";
			$sort = isset($matches[2]) ? strtoupper($matches[2]) : $default_order_for_column;
			Image::$order_sql = "images.$ord $sort";
			$event->add_querylet(new Querylet("1=1")); //small hack to avoid metatag being treated as normal tag
		}
		else if(preg_match("/^order[=|:]random[_]([0-9]{1,4})$/i", $event->term, $matches)){
			//order[=|:]random requires a seed to avoid duplicates
			//since the tag can't be changed during the parseevent, we instead generate the seed during submit using js
			$seed = $matches[1];
			Image::$order_sql = "RAND($seed)";
			$event->add_querylet(new Querylet("1=1")); //small hack to avoid metatag being treated as normal tag
		}

		$this->stpen++;
	}
}
