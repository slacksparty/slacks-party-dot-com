<?php

class PosterContestTheme extends Themelet {
	protected $page_number, $total_pages, $search_terms;

	/**
	 * @param int $page_number
	 * @param int $total_pages
	 * @param string[] $search_terms
	 */
	public function set_page($page_number, $total_pages, $search_terms) {
		$this->page_number = $page_number;
		$this->total_pages = $total_pages;
		$this->search_terms = $search_terms;
	}

	/**
	 * @param Page $page
	 * @param Image[] $images
	 */
	public function display_page(Page $page, $images) {
		$this->display_page_header($page, $images);

		$nav = $this->build_navigation($this->page_number, $this->total_pages, $this->search_terms);
		$page->add_block(new Block("Navigation", $nav, "left", 0));

		if(count($images) > 0) {
			$this->display_page_images($page, $images);
		}
		else {
			$this->display_error(404, "We have 0 contestants!", "There are no entries in the poster contest yet! Go make a poster and enter it!");
		}
	}

	/**
	 * @param string[] $parts
	 */
	public function display_admin_block($parts) {
		global $page;
		$page->add_block(new Block("List Controls", join("<br>", $parts), "left", 50));
	}


	/**
	 * @param int $page_number
	 * @param int $total_pages
	 * @param string[] $search_terms
	 * @return string
	 */
	protected function build_navigation($page_number, $total_pages, $search_terms) {
		$prev = $page_number - 1;
		$next = $page_number + 1;

		$u_tags = url_escape(implode(" ", $search_terms));
		$query = empty($u_tags) ? "" : '/'.$u_tags;


		$h_prev = ($page_number <= 1) ? "Prev" : '<a href="'.make_link('/posters/'.$prev).'">Prev</a>';
		$h_next = ($page_number >= $total_pages) ? "Next" : '<a href="'.make_link('/posters/'.$next).'">Next</a>';

		$h_classics = $h_index = "<a href='".make_link('/classics')."'>Classic Posters</a>";
		$h_posters = $h_index = "<a href='".make_link('/posters')."'>Poster Contest</a>";
		$h_slacks = $h_index = "<a href='".make_link('/slacksterpieces')."'>Slacksterpieces</a>";

		return $h_prev.' | '.$h_next.'<br>'.$h_classics.'<br>'.$h_posters.'<br>'.$h_slacks;
	}

	/**
	 * @param Image[] $images
	 * @param string $query
	 * @return string
	 */
	protected function build_table($images, $query) {
		$h_query = html_escape($query);
		$table = "<div class='shm-image-list' data-query='$h_query'>";
		foreach($images as $image) {
			$table .= $this->build_thumb_html($image);
		}
		$table .= "</div>";
		return $table;
	}

	/**
	 * @param Page $page
	 * @param Image[] $images
	 */
	protected function display_page_header(Page $page, $images) {
		global $config;

		$page_title = "The Final Slacks Party: Poster Contest";
		if (count($images) > 0) {
			$page->set_subheading("Page {$this->page_number} / {$this->total_pages}");
		}

		$page->set_title($page_title);
		$page->set_heading($page_title);
	}

	/**
	 * @param Page $page
	 * @param Image[] $images
	 */
	protected function display_page_images(Page $page, $images) {
		$page->add_block(new Block("Entries", $this->build_table($images, null), "main", 10, "image-list"));
		$this->display_paginator($page, "posters", null, $this->page_number, $this->total_pages, TRUE);
	}
}

