<?php 
$doe 				= $wishList->getDateOfEvent();
$description 		= $wishList->get("description");
$formattedAddress 	= $they->formattedAddress();
$privateUsers 		= new User($db);
$privateUsers 		= $wishList->getPrivateUsers();
?>
<p><a href="<?php echo $app->config('page', 'profile', array('userId'=>$they->get("userId"))); ?>"><?php echo $they->fullName(); ?></a></p>

<?php /* DESKTOP MENU */ ?>
<div class="pull-right hidden-xs">
	<?php if ($me->isAlso($they)) : ?>
		<a href="<?php echo $app->config( 'page', 'edit-wish-list', array("wishListId"=>$wishList->get("wishListId")) ); ?>" class="btn btn-default"><small class="glyphicon glyphicon-pencil"></small>&nbsp;&nbsp;Edit</a>
		<a href="#" class="btn btn-default"><small class="glyphicon glyphicon-share-alt"></small>&nbsp;&nbsp;Share</a>
		<a href="#" class="btn btn-default"><small class="glyphicon glyphicon-print"></small>&nbsp;&nbsp;Print</a>
		<a href="#delete-wish-list-modal" data-toggle="modal" class="btn btn-default"><small class="glyphicon glyphicon-trash"></small>&nbsp;&nbsp;Delete</a>
	<?php else : ?>
		<a href="#" class="btn btn-default"><small class="glyphicon glyphicon-print"></small>&nbsp;&nbsp;Print</a>
	<?php endif; ?>
</div>

<?php /* MOBILE MENU */ ?>
<div class="btn-group pull-right visible-xs">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		<small class="glyphicon glyphicon-cog"></small>&nbsp;<span class="caret"></span>
	</button>
	<?php if ($me->isAlso($they)) : ?>
		<ul class="dropdown-menu" role="menu">
			<li><a href="<?php echo $app->config('page','edit-wish-list',array('wishListId'=>$wishList->get("wishListId"))); ?>"><small class="glyphicon glyphicon-pencil"></small>&nbsp;&nbsp;Edit</a></li>
			<li><a href="#"><small class="glyphicon glyphicon-share-alt"></small>&nbsp;&nbsp;Share</a></li>
			<li><a href="#"><small class="glyphicon glyphicon-print"></small>&nbsp;&nbsp;Print</a></li>
			<li class="divider"></li>
			<li><a href="#delete-wish-list-modal" data-toggle="modal"><small class="glyphicon glyphicon-trash"></small>&nbsp;&nbsp;Delete</a></li>
		</ul>
	<?php else : ?>
		<ul class="dropdown-menu" role="menu">
			<li><a href="#"><span class="glyphicon glyphicon-share-alt"></span>&nbsp;&nbsp;Share</a></li>
			<li><a href="#"><span class="glyphicon glyphicon-print"></span>&nbsp;&nbsp;Print</a></li>
		</ul>
	<?php endif; ?>
</div>
<div id="wish-list-details">
	<div class="table-responsive">
		<table class="table table-striped">
			<tr><td>List Type:</td><td><?php echo $wishList->getTypeLabel(); ?>&nbsp;&nbsp;<a href="#"><small class="glyphicon glyphicon-question-sign"></small></a></td></tr>
			<?php if ($description) : ?>
				<tr><td>Notes:</td><td><?php echo $description; ?></td></tr>
			<?php endif; ?>
			<?php if ($doe) : ?>
				<tr><td>Date of Event:</td><td><?php echo $doe; ?></td></tr>
			<?php endif; ?>
			<?php if ($session->isLoggedIn() && $formattedAddress && $wishList->get("showAddress")) : ?>
				<tr><td>Ship gifts to:</td><td><?php echo $formattedAddress; ?></td></tr>
			<?php endif; ?>
			<tr><td>Privacy:</td><td>
				<?php $wishListId = $wishList->get("privacyId"); ?>
				<?php if ($wishListId == 1) : ?>
					<small class="glyphicon glyphicon-globe"></small>&nbsp;&nbsp;This wish list is <strong>public</strong>.
				<?php elseif ($wishListId == 2) : ?>
					<small class="glyphicon glyphicon-eye-close"></small>&nbsp;&nbsp;Only <strong>you</strong> can view this wish list.
				<?php elseif ($wishListId == 3) : ?>
					<small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Only <strong><?php echo ($me->isAlso($they)) ? "your" : $they->firstNamePossessive(); ?> followers</strong> can view this wish list.
				<?php elseif ($wishListId == 4) : ?>
					<?php if ($privateUsers) : ?>
						<small class="glyphicon glyphicon-eye-close"></small>&nbsp;&nbsp;Only 
						<?php $counter = 0; $length = count($privateUsers); ?>
						<?php foreach ($privateUsers as $user) : ?>
							<a href="<?php echo $app->config('page','profile',array('userId'=>$user->get("userId"))); ?>"><strong><?php echo $user->fullName(); ?></strong></a>
							<?php echo ($counter++ > $length) ? ", " : ""; ?>
						<?php endforeach; ?>
						<?php if ($me->isAlso($they)) : ?> and you<?php endif; ?>
						 may view this wish list.
					<?php endif; ?>
				<?php endif; ?>
			</td></tr>
			<tr><td>Updated:</td><td><?php echo $app->friendlyDate($wishList->get("timestamp")); ?></td></tr>
		</table>
	</div>
</div>
<?php if ($me->isAlso($they)) : ?>
	<div class="modal" id="delete-wish-list-modal">
		<?php include MODAL_PATH."delete-wish-list.modal.php"; ?>
	</div>
<?php endif; ?>