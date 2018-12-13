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
		$page->add_block(new Block("Image Details", $this->build_info($image, $editor_parts), "main", 20));
	}

	/**
	 * @return string
	 */
	protected function build_navigation(Image $image) {

		$h_classics = $h_index = "<a href='".make_link('/classics')."'>Classic Posters</a>";
		$h_posters = $h_index = "<a href='".make_link('/posters')."'>Poster Contest</a>";
		$h_slacks = $h_index = "<a href='".make_link('/slacksterpieces')."'>Slacksterpieces</a>";

		return $h_classics.'<br>'.$h_posters.'<br>'.$h_slacks;
	}

	protected function build_info(Image $image, $editor_parts) {
		global $user;

		if(count($editor_parts) == 0) return ($image->is_locked() ? "<br>[Image Locked]" : "");

		$html = make_form(make_link("post/set"))."
					<input type='hidden' name='image_id' value='{$image->id}'>
					<table style='width: 500px;' class='image_info form'>
		";
		foreach($editor_parts as $part) {
			$html .= $part;
		}
		if(
			(!$image->is_locked() || $user->can("edit_image_lock")) &&
			$user->can("edit_image_tag")
		) {
			$html .= "
						<tr><td colspan='4'>
							<input class='view' type='button' value='Edit' onclick='$(\".view\").hide(); $(\".edit\").show();'>
							<input class='edit' type='submit' value='Set'>
						</td></tr>
			";
		}
		$html .= "
					</table>
				</form>
		";
		return $html;
	}
}

