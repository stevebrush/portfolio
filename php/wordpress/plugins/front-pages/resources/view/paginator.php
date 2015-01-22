<div class="tfp-pagination">
	<p>
		<?php echo $paginator["totalItems"]; ?> front pages &nbsp;&nbsp;&nbsp;
		show
		<a<?php echo ($data["options"]["show"] === "40") ? ' class="active"': ""; ?> href="<?php echo add_query_arg(array("tfp_show" => "40")); ?>">40</a>
		<a<?php echo ($data["options"]["show"] === "80") ? ' class="active"': ""; ?> href="<?php echo add_query_arg(array("tfp_show" => "80")); ?>">80</a>
		<a<?php echo ($data["options"]["show"] === "100") ? ' class="active"': ""; ?> href="<?php echo add_query_arg(array("tfp_show" => "100")); ?>">100</a>
		<a<?php echo ($data["options"]["show"] > 100) ? ' class="active"': ""; ?> href="<?php echo add_query_arg(array("tfp_show" => "all")); ?>">all</a>
		per page
	</p>
	<?php if ($paginator["totalPages"] > 1) : ?>
		<p>
			<?php if ($paginator["currentPage"] > 1) : ?>
				<a href="<?php echo add_query_arg(array("tfp_page" => ($paginator["currentPage"] - 1))); ?>">Previous</a>
			<?php endif; ?>
			<?php for ($i = 1; $i <= $paginator["totalPages"]; $i++) : ?>
				<?php if ($i === 1 || ($i < ($paginator["currentPage"] + 3) && $i > ($paginator["currentPage"] - 3)) || $i === $paginator["totalPages"]) : ?>
					<a<?php echo ($i === $paginator["currentPage"]) ? ' class="active"' : ""; ?> href="<?php echo add_query_arg(array("tfp_page" => $i)); ?>"><?php echo $i; ?></a>
				<?php elseif ($i === 2 || $i === $paginator["totalPages"] - 1) : ?>
					<span>&#8230;</span>
				<?php endif; ?>
			<?php endfor; ?>
			<?php if ($paginator["currentPage"] < $paginator["totalPages"]) : ?>
				<a href="<?php echo add_query_arg(array("tfp_page" => ($paginator["currentPage"] + 1))); ?>">Next</a>
			<?php endif; ?>
		</p>
	<?php endif; ?>
</div>
