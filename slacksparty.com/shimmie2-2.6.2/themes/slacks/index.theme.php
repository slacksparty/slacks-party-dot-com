<?php

class CustomIndexTheme extends IndexTheme {

	/**
	 * @param Page $page
	 * @param Image[] $images
	 */
	protected function display_page_header(Page $page, $images) {
		global $config;

		if (count($this->search_terms) == 0) {
			$page_title = $config->get_string('title');
		} else {
			$search_string = implode(' ', $this->search_terms);
			$page_title = html_escape($search_string);
			if (count($images) > 0) {
				$page->set_subheading("Page {$this->page_number} / {$this->total_pages}");
			}
		}
		if ($this->page_number > 1 || count($this->search_terms) > 0) {
			// $page_title .= " / $page_number";
		}

		$page->set_title($page_title);
		$heading = $page_title;
		if ($page_title == "classics") {
			$heading = "Classic Posters";
		} elseif ($page_title == "posters") {
			$heading = "2018 Poster Contest Entries";
		} elseif ($page_title == "slacksterpiece") {
			$heading = "Slacksterpieces";
		}
		$page->set_heading($heading);
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

			if ($page->title == "classics") {
				$this->display_error(404, "Empty", "how did you get to this page?");
			} elseif ($page->title == "posters") {
				$this->display_error(404, "We have 0 contestants!", "There are no entries in the poster contest yet! Go make a poster and enter it!");
			} elseif ($page->title == "slacksterpiece") {
				$this->display_error(404, "The greatest slacksterpiece has yet to be created!", "We have no Slacksterpieces to show you... go draw your slacksterpiece and then return here.");
			} else {
				$this->display_error(404, "Empty", "how did you get to this page?");
			}
		}
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


		$h_prev = ($page_number <= 1) ? "Prev" : '<a href="'.make_link('post/list'.$query.'/'.$prev).'">Prev</a>';
		$h_next = ($page_number >= $total_pages) ? "Next" : '<a href="'.make_link('post/list'.$query.'/'.$next).'">Next</a>';

		$h_classics = $h_index = "<a href='".make_link('post/list/classics/1')."'>Classic Posters</a>";
		$h_posters = $h_index = "<a href='".make_link('post/list/posters/1')."'>Poster Contestants</a>";
		$h_slacks = $h_index = "<a href='".make_link('post/list/slacksterpiece/1')."'>Slacksterpieces</a>";

		return $h_prev.' | '.$h_next.'<br>'.$h_classics.'<br>'.$h_posters.'<br>'.$h_slacks;
	}

	/**
	 * @param Page $page
	 * @param Image[] $images
	 */
	protected function display_page_images(Page $page, $images) {
		if (count($this->search_terms) == 1) {
			$query = url_escape(implode(' ', $this->search_terms));
			$page->add_block(new Block("Images", $this->build_table($images, "#search=$query"), "main", 10, "image-list"));
			$this->display_paginator($page, "post/list/$query", null, $this->page_number, $this->total_pages, TRUE);
		} elseif (count($this->search_terms) > 0) {
			$query = url_escape(implode(' ', $this->search_terms));
			$page->add_block(new Block("Images", $this->build_table($images, "#search=$query"), "main", 10, "image-list"));
			$this->display_paginator($page, "post/list/$query", null, $this->page_number, $this->total_pages, TRUE);
		} else {
			$page->add_block(new Block("Images", $this->build_table($images, null), "main", 10, "image-list"));
			$this->display_paginator($page, "post/list", null, $this->page_number, $this->total_pages, TRUE);
		}
	}
}

