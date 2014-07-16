<div class="page-heading">
	<h1>Welcome to <?php echo $app->config("app", "name"); ?>!</h1>
</div>
<div class="container-fluid">
	<p class="lead"><?php echo $app->config("app", "name"); ?> is a <u>completely free</u>, no-strings-attached Social Gift Registry, made especially for families.</p>
	<div class="panel panel-default panel-body">
		<form>
			<input type="hidden" name="redirect" value="<?php echo $app->config('page','home'); ?>">
			<div class="alert form-alert clearfix" style="display:none;"></div>
			<h3>Join the hundreds now enjoying the convenience of <em>shareable</em> wish lists!</h3>
			<p>
				<a href="#" class="btn btn-facebook btn-lg btn-facebook-signup" data-loading-text="Processing...">Sign up using Facebook</a>
				<a href="<?php echo $app->config('page','signup'); ?>" class="btn btn-link btn-lg">Sign up with email address&nbsp;&rarr;</a>
			</p>
			<p>
			<?php
			$users = new User($db);
			$users = $users->find();
			?>
			<?php foreach ($users as $user) : ?>
				<img src="<?php echo $user->getThumbnail()->size("sm")->get("src"); ?>">
			<?php endforeach; ?>
			</p>
			<div class="panel panel-default panel-inverse">
				<div class="panel-heading">
					<h4>Find someone</h4>
				</div>
				<div class="panel-body">
					<input type="text" placeholder="First and last name" class="form-control">
				</div>
			</div>
		</form>
	</div>
</div>