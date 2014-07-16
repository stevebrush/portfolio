<?php
$form = new Form(array(
	"slug" => "new-wish-list",
	"cssClass" => "wish-list-form",
	"heading" => (isset($wishList)) ? "Edit wish list" : "New wish list",
	"orientation" => "horizontal",
	"action" => $app->config('ajax','new-wish-list')
));

// create empty Wish List object if new wish list
if (!isset($wishList)) {
	$wishList = new WishList($db);
}

$wishList->getInputs();
$wishListId = $wishList->get("wishListId");
$followers = $me->getFollowers();

$name 			= new FormField($form, $wishList->getField("name"));
$typeOfList 	= new FormField($form, $wishList->getField("typeOfList"));
$date 			= new FormField($form, $wishList->getField("dateOfEvent"));
$description 	= new FormField($form, $wishList->getField("description"));
$showAddress 	= new FormField($form, $wishList->getField("showAddress"));
$submit 		= new FormField($form, array(
	"type" => "submit",
	"label" => (empty($wishListId)) ? "Create wish list" : "Save changes",
	"includeWrapper" => "false",
	"dataLoadingText" => "Processing...",
	"fieldClass" => "btn-primary"
));

$form->start(); 	
	?>
	<div class="panel panel-default">
		<div class="modal-header">
			<h4 class="modal-title"><?php echo $form->getHeading(); ?></h4>
		</div>
		<div class="modal-nav navbar navbar-default">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-1" data-toggle="tab"><small class="glyphicon glyphicon-camera hidden-xs"></small>&nbsp;Details</a></li>
				<li><a href="#tab-2" data-toggle="tab"><small class="glyphicon glyphicon-eye-open hidden-xs"></small>&nbsp;Privacy</a></li>
			</ul>
		</div>
		<div class="modal-body">
			<?php $form->alert(); ?>
			<div class="tab-content">
				<div class="tab-pane active" id="tab-1">
					<?php if (!empty($wishListId)) : ?>
						<input type="hidden" name="wishListId" value="<?php echo $wishListId; ?>">
						<input type="hidden" name="signature" value="<?php echo $me->createSignature($wishListId); ?>">
					<?php else : ?>
						<input type="hidden" name="signature" value="<?php echo $me->createSignature("new-wish-list"); ?>">
					<?php endif; ?>
					<?php $name->render(); ?>
					<?php $typeOfList->render(); ?>
					<div class="field-type-of-list">
						<?php $date->render(); ?>
					</div>
					<?php $description->render(); ?>
					<?php $showAddress->render(); ?>
				</div>
				<div class="tab-pane section-privacy" id="tab-2">
					<?php
					if (!empty($wishListId)) {
						$privacyId = $wishList->get("privacyId");
					}
					?>
					<label>Who may view this wish list?</label>
					<div class="radio">
						<label><input type="radio" name="privacyId" value="1"<?php if (isset($privacyId) && $privacyId == "1") echo " checked"; ?>> <span class="glyphicon glyphicon-globe"></span> Everyone</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="privacyId" value="2"<?php if (isset($privacyId) && $privacyId == "2") echo " checked"; ?>> <span class="glyphicon glyphicon-eye-close"></span> Just Me</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="privacyId" value="3"<?php if ((isset($privacyId) && $privacyId == "3") || !isset($privacyId)) echo " checked"; ?>> <span class="glyphicon glyphicon-user"></span> Just My Followers</label>
					</div>
					<?php if ($followers) : ?>
						<div class="radio">
							<label><input type="radio" name="privacyId" value="4"<?php if (isset($privacyId) && $privacyId == "4") echo " checked"; ?>> <span class="glyphicon glyphicon-cog"></span> Custom</label>
						</div>
						<div class="well section-followers">
							<?php foreach ($followers as $follower) : ?>
								<?php
								$checked = "";
								if (( !empty($wishListId) && $wishList->userCanView($follower) ) || empty($wishListId)) {
									$checked = " checked";
								}
								?>
								<div class="checkbox">
									<label><input type="checkbox" value="<?php echo $follower->get("userId"); ?>" name="userIds[]"<?php echo $checked; ?>> <span class="glyphicon glyphicon-user"></span> <?php echo $follower->fullName(); ?></label>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<?php $submit->render("field"); ?>
		</div>
	</div>
	<?php 
$form->stop();
