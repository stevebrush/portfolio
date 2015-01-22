<?php
/* 
 * Before you start, make sure your theme's single.php file includes the following in the loop:
 * <?php get_template_part('content', get_post_type($post)); ?>
 *
 */

# Show the form's title.
# Since OLX Forms are post types, you can access all other post attributes normally.
the_title ('<h1>','</h1>');

# Displays the form's embed code using a template tag:
the_olx_form ();

# Or, do an explicit call to its shortcode:
echo do_shortcode ('[olx_form form_id="87"]');
?>