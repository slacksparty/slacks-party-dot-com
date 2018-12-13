<?php
new UserClass("anonymous", "base", array(
	"create_image" => True,
	"create_comment" => True,
	"create_image_report" => True,
));

new UserClass("user", "base", array(
	"big_search" => True,
	"create_image" => True,
	"create_comment" => True,
	"edit_image_tag" => True,
	"edit_image_source" => True,
	"create_image_report" => True,
));
?>