<?php

# Useful for printing arrays on the page for debugging purposes.
if (! function_exists ("print_f")) {
	function print_f ($arr, $title = "") {

		if ($title) {
			echo "<h2>" . $title . "</h2>";
		}

		echo "<pre style=\"height:200px;overflow:auto;border:1px solid #ccc;background:#f2f1f0;margin:0 0 15px;\">";
		print_r ($arr);
		echo "</pre>";

	}
}


# Reference this template tag in your theme files (inside the loop) to print the form's embed code.
# To display a form outside the loop, simply provide the post's ID.
function the_olx_form ($postId = null) {

	$html = "";

	if (! isset ($postId)) {
		global $post;
		$postId = $post-> ID;
	}

	global $BlackbaudOnlineExpress;

	$html .= html_entity_decode (get_post_meta ($postId, "olx_forms_embed_code_field", true), ENT_QUOTES, 'UTF-8');
	$html .= html_entity_decode (get_post_meta ($postId, "olx_forms_html_after_field", true), ENT_QUOTES, 'UTF-8');

	// Social Sharing Lightbox.
	$data = $BlackbaudOnlineExpress-> GetSocialSharingData ($postId);

	if ($data ["active"] == "true") {
		ob_start ();
		include OLXFORMS_RESOURCE_PATH . "view/js-data.php";
		$html .= ob_get_clean ();
	}

	echo $html;

}
