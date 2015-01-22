<?php
global $wp_query;
$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<?php if (isset($data["date"])) : ?>
	<h4 class="tfp-date"><?php echo $data["date"]; ?></h4>
<?php endif; ?>
<div class="tfp-utility">
	<div class="tfp-navigation">
		<ul class="nav nav-pills">
			<li<?php echo ($data["options"]["display"] === "gallery") ? ' class="active"': ""; ?>><a href="<?php echo add_query_arg(array("tfp_display" => "gallery"), get_permalink()); ?>"><span class="fa fa-th-large"></span>Gallery</a></li>
			<li<?php echo ($data["options"]["display"] === "list") ? ' class="active"': ""; ?>><a href="<?php echo add_query_arg(array("tfp_display" => "list"), get_permalink()); ?>"><span class="fa fa-bars"></span>List</a></li>
			<li<?php echo ($data["options"]["display"] === "map") ? ' class="active"': ""; ?>><a href="<?php echo add_query_arg(array("tfp_display" => "map", "tfp_region" => "USA", "tfp_sort_by" => "state"), get_permalink()); ?>"><span class="fa fa-globe"></span>Map</a></li>
		</ul>
		<ul class="nav nav-pills">
			<li<?php echo ($data["options"]["display"] === "archive-date" || $data["options"]["display"] === "archive-summary") ? ' class="active"': ""; ?>><a href="<?php echo add_query_arg(array("tfp_display" => "archive-summary"), get_permalink()); ?>"><span class="fa fa-archive"></span>Archives</a></li>
			<?php if (isset($data["showTopTen"]) && $data["showTopTen"] == true) : ?>
				<li<?php echo ($data["options"]["display"] === "topten") ? ' class="active"': ""; ?>><a href="<?php echo add_query_arg(array("tfp_display" => "topten"), get_permalink()); ?>"><span class="fa fa-star"></span>Top Ten</a></li>
			<?php else : ?>
				<li><a href="#" disabled class="tfp-disabled"><span class="fa fa-star"></span>Top Ten</a></li>
			<?php endif; ?>
		</ul>
	</div>
	<?php if (isset($data["sort"])) : ?>
		<?php include "filters.php"; ?>
	<?php endif; ?>
</div>
