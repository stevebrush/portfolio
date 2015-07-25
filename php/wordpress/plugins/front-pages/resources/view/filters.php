<?php
global $wp_query;
?>
<form class="tfp-filters">
	<label>Sort by region:</label>
	<select id="tfp-sort-region">
		<option value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>">All</option>
		<option<?php echo (isset($wp_query->query_vars['tfp_region']) && $wp_query->query_vars['tfp_region'] === "USA") ? ' selected' : ""; ?> value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_region" => "USA", "tfp_sort_by" => "state", "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>">USA</option>
		<option<?php echo (isset($wp_query->query_vars['tfp_region']) && $wp_query->query_vars['tfp_region'] === "International") ? ' selected' : ""; ?> value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_region" => "International", "tfp_sort_by" => "country", "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>">International</option>
		<?php foreach ($data["sort"]["region"] as $region) : ?>
			<option<?php echo (isset($wp_query->query_vars['tfp_region']) && $wp_query->query_vars['tfp_region'] === $region) ? ' selected' : ""; ?> value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_region" => $region, "tfp_sort_by" => "country", "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>"><?php echo $region; ?></option>
		<?php endforeach; ?>
	</select>

	<?php if (isset($wp_query->query_vars['tfp_region'])) : ?>

		<?php if ($wp_query->query_vars['tfp_region'] === "USA") : ?>
			<select id="tfp-sort-state-type">
				<option<?php echo (isset($wp_query->query_vars['tfp_sort_by']) && $wp_query->query_vars['tfp_sort_by'] === "state") ? ' selected' : ""; ?> value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_sort_by" => "state", "tfp_region" => "USA", "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>">State Name</option>
				<option<?php echo (isset($wp_query->query_vars['tfp_sort_by']) && $wp_query->query_vars['tfp_sort_by'] === "title") ? ' selected' : ""; ?> value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_sort_by" => "title", "tfp_region" => "USA", "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>">Paper Name</option>
			</select>
		<?php else : ?>
			<select id="tfp-sort-country-type">
				<option<?php echo (isset($wp_query->query_vars['tfp_sort_by']) && $wp_query->query_vars['tfp_sort_by'] === "country") ? ' selected' : ""; ?> value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_sort_by" => "country", "tfp_region" => $wp_query->query_vars['tfp_region'], "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>">Country Name</option>
				<option<?php echo (isset($wp_query->query_vars['tfp_sort_by']) && $wp_query->query_vars['tfp_sort_by'] === "title") ? ' selected' : ""; ?> value="<?php echo add_query_arg(array("tfp_display" => $data["options"]["display"], "tfp_sort_by" => "title", "tfp_region" => $wp_query->query_vars['tfp_region'], "tfp_archive_id" => $wp_query->query_vars['tfp_archive_id']), get_permalink()); ?>">Paper Name</option>
			</select>
		<?php endif; ?>

		<?php if (isset($wp_query->query_vars['tfp_sort_by'])) : ?>
			<?php if ($wp_query->query_vars['tfp_sort_by'] === "title") : ?>
				<?php if ($data["sort"]["titleFirstLetter"]) : ?>
					<select id="tfp-sort-title-letter">
						<?php foreach ($data["sort"]["titleFirstLetter"] as $title => $url) : ?>
							<option<?php echo (isset($wp_query->query_vars['tfp_title_letter']) && $wp_query->query_vars['tfp_title_letter'] === $title) ? ' selected' : ""; ?> value="<?php echo $url; ?>"><?php echo $title; ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($wp_query->query_vars['tfp_sort_by'] === "state") : ?>
				<?php if ($data["sort"]["stateFirstLetter"]) : ?>
					<select id="tfp-sort-state-letter">
						<?php foreach ($data["sort"]["stateFirstLetter"] as $state => $url) : ?>
							<option<?php echo (isset($wp_query->query_vars['tfp_state_letter']) && $wp_query->query_vars['tfp_state_letter'] === $state) ? ' selected' : ""; ?> value="<?php echo $url; ?>"><?php echo $state; ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($wp_query->query_vars['tfp_sort_by'] === "country") : ?>
				<?php if ($data["sort"]["countryFirstLetter"]) : ?>
					<select id="tfp-sort-country-letter">
						<?php foreach ($data ["sort"] ["countryFirstLetter"] as $country => $url) : ?>
							<option<?php echo (isset($wp_query->query_vars['tfp_country_letter']) && $wp_query->query_vars['tfp_country_letter'] === $country) ? ' selected' : ""; ?> value="<?php echo $url; ?>"><?php echo $country; ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
</form>
