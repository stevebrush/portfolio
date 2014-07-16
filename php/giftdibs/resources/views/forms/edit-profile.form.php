<?php

$fbLoggedIn = false;

try {
	if ($facebook->getUser()) {
		$fbLoggedIn = true;
	}
} catch (FacebookApiException $e) {
	$fbLoggedIn = false;
}

$form = new Form(array(
	"slug" => "edit-profile",
	"cssClass" => "edit-profile-form",
	"heading" => "Edit profile",
	"orientation" => "horizontal",
	"action" => $app->config("ajax","edit-profile"),
	"allowUpload" => "true"
));

$me->getInputs();

/* Database Fields */
$firstName 		= new FormField($form, $me->getField("firstName"));
$lastName 		= new FormField($form, $me->getField("lastName"));
$birthdayMonth 	= new FormField($form, $me->getField("birthdayMonth"));
$birthdayDay 	= new FormField($form, $me->getField("birthdayDay"));
$birthdayYear 	= new FormField($form, $me->getField("birthdayYear"));
$gender 		= new FormField($form, $me->getField("gender"));

$birthdayPrivate = new FormField($form, array(
	"type" => "checkbox",
	"name" => "birthdayPrivate",
	"checked" => !$me->get("birthdayPrivate"),
	"label" => "Let followers see my birthday (month and day only)",
	"value" => "birthday"
));
$deleteThumbnail = new FormField($form, array(
	"type" => "checkbox",
	"name" => "deleteThumbnail",
	"checked" => "false",
	"label" => "Remove thumbnail?",
	"value" => "yes"
));
$userId = new FormField($form, array(
	"type" => "hidden",
	"name" => "userId",
	"value" => $me->get("userId")
));
$signature = new FormField($form, array(
	"type" => "hidden",
	"name" => "signature",
	"value" => $me->createSignature("edit-profile")
));
$redirect = new FormField($form, array(
	"type" => "hidden",
	"name" => "redirect",
	"value" => $app->config("page","edit-profile")
));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Save Changes",
	"fieldClass" => "btn-primary"
));
$form->start(); 
	$form->heading();
	?>
	<div class="form-body">
		<?php
		$form->alert();
		$userId->render("field");
		$signature->render("field");
		$redirect->render("field");
		?>
		<div class="form-group image-uploader">
			<?php $thumbnail = $me->getThumbnail(); ?>
			<?php if ($thumbnail->get("imageId")) : ?>
				<input type="hidden" name="imageId" value="<?php echo $thumbnail->get("imageId"); ?>">
			<?php endif; ?>
			<label for="thumbnail_<?php echo $form->getFormId(); ?>" class="col-sm-3 control-label">Thumbnail</label>
			<div class="col-sm-9">
				<div class="media">
					<div class="thumbnail pull-left"><img class="media-object" src="<?php echo $thumbnail->size("sm")->get("src"); ?>" alt="<?php echo $me->fullName(); ?>"></div>
					<div class="media-body">
						<?php if ($thumbnail->get("imageId")) : ?>
							<p><?php $deleteThumbnail->render("field"); ?></p>
						<?php endif; ?>
						<input type="file" name="thumbnail">
					</div>
				</div>
			</div>
		</div>
		<?php 
		$firstName->render();
		$lastName->render();
		$gender->render();
		?>
		<div class="form-group form-group-selects">
			<?php 
			$birthdayMonth->render("label");
			$birthdayMonth->decorationStart();
				$birthdayMonth->render("field"); 
				$birthdayDay->render("field"); 
				$birthdayYear->render("field"); 
			$birthdayMonth->decorationStop();
			?>
		</div>
		<?php $birthdayPrivate->render(); ?>
	</div>
	<div class="form-footer">
		<?php $submit->render("field"); ?>
		<?php if ($fbLoggedIn) : ?>
			<a href="#" class="btn btn-facebook btn-facebook-update-profile" data-loading-text="Processing..." data-gd-user-id="<?php echo $me->get("userId"); ?>" data-gd-redirect="<?php echo $app->currentUrl(); ?>">Reload info from Facebook</a>
		<?php else : ?>
			<a href="#" class="btn btn-facebook btn-facebook-link-account" data-loading-text="Processing...">Link your Facebook account</a>
		<?php endif; ?>
	</div>
<?php $form->stop();