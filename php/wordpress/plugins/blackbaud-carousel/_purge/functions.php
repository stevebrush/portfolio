<?php

function the_blackbaud_carousel ($atts) {

	$id = uniqid ();
	$args = array (
		"post_type" => $atts ["post_type"],
		"posts_per_page" => "-1",
		"orderby" => $atts ["orderby"],
		"order" => $atts ["order"]
	);

	if ($atts ["category"] != "") {
		$args [$atts ["post_type"] . "_category"] = $atts ["category"];
	}

	if ($atts ["id"] != "") {
		$args ["p"] = $atts ["id"];
	}

	$loop = new WP_Query ($args);
	$images = array ();
	$output = "";

	while ($loop->have_posts ()) {

		$loop->the_post ();

		if ("" != get_the_post_thumbnail ()) {

			$post_id = get_the_ID ();
			$title = get_the_title ();
			$content = get_the_content ();
			$excerpt = get_the_excerpt ();
			$image = get_the_post_thumbnail (get_the_ID (), "full");
			$btn_label = html_entity_decode (get_post_meta (get_the_ID (), $atts ["post_type"] . "_button_label_field", true), ENT_QUOTES, 'UTF-8');
			$btn_link = html_entity_decode (get_post_meta (get_the_ID (), $atts ["post_type"] . "_button_link_field", true), ENT_QUOTES, 'UTF-8');
			$images [] = array (
				"post_id" => $post_id,
				"title" => $title,
				"content" => $content,
				"excerpt" => $excerpt,
				"image" => $image,
				"btn_label" => $btn_label,
				"btn_link" => $btn_link
			);

		}

	}

	if (count ($images) > 0) {
		ob_start ();
		include "views/carousel.php";
		$output = ob_get_clean ();
	}

	// Restore original Post Data
	wp_reset_postdata ();
	return $output;
}
