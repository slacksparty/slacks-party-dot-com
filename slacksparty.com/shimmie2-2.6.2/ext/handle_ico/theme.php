<?php

class IcoFileHandlerTheme extends Themelet {
	public function display_image(Page $page, Image $image) {
		$ilink = $image->get_image_link();
		$html = "
			<img id='main_image' class='shm-main-image' alt='main image' src='$ilink'
			data-width='{$image->width}' data-height='{$image->height}'>
		";
		$page->add_block(new Block("Image", $html, "main", 10));
	}
}

