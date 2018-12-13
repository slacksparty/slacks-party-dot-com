<?php

class CustomViewImageTheme extends ViewImageTheme {
	/*
	 * Build a page showing $image and some info about it
	 */
	public function display_page(Image $image, $editor_parts) {
		global $page;

		$h_metatags = str_replace(" ", ", ", html_escape($image->get_tag_list()));

		$page->set_title("Image {$image->id}: ".html_escape($image->get_tag_list()));
		$page->add_html_header("<meta name=\"keywords\" content=\"$h_metatags\">");
		$page->add_html_header("<meta property=\"og:title\" content=\"$h_metatags\">");
		$page->add_html_header("<meta property=\"og:type\" content=\"article\">");
		$page->add_html_header("<meta property=\"og:image\" content=\"".make_http($image->get_thumb_link())."\">");
		$page->add_html_header("<meta property=\"og:url\" content=\"".make_http(make_link("post/view/{$image->id}"))."\">");
		$page->set_heading(html_escape($image->get_tag_list()));
		$page->add_block(new Block("Navigation", $this->build_navigation($image), "left", 0));
	}

	/**
	 * @return string
	 */
	protected function build_navigation(Image $image) {

		$h_classics = $h_index = "<a href='".make_link('post/list/classics/1')."'>Classic Posters</a>";
		$h_posters = $h_index = "<a href='".make_link('post/list/posters/1')."'>Poster Contestants</a>";
		$h_slacks = $h_index = "<a href='".make_link('post/list/slacksterpiece/1')."'>Slacksterpieces</a>";

		return $h_classics.'<br>'.$h_posters.'<br>'.$h_slacks;
	}
}

