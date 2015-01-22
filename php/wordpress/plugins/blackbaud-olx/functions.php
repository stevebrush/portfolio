<?php

# Reference this template tag in your theme files (inside the loop) to print the form's embed code.
# To display a form outside the loop, simply provide the post's ID.

function the_olx_form ($postId = null) {

	$html = "";

	if (isset ($postId)) {
		$postId = $postId;
	} else {
		global $post;
		$postId = $post->ID;
	}

	$html .= html_entity_decode (get_post_meta ($postId, "olx_forms_embed_code_field", true), ENT_QUOTES, 'UTF-8');
	$html .= html_entity_decode (get_post_meta ($postId, "olx_forms_html_after_field", true), ENT_QUOTES, 'UTF-8');

	echo $html;

}
