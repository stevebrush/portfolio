<?php
$form = new Form(array(
	"action" => "ajax/signup.ajax.php",
	"orientation" => "horizontal",
	"cssClass" => "gd-signup-form",
	"heading" => "Create a Free Account"
));

$facebookSignup = new FormField($form, array(
	"type" => "static",
	"value" => "<a href=\"#\" class=\"btn btn-primary btn-facebook btn-lg btn-facebook-signup\" data-loading-text=\"Processing...\">One-Click Registration with Facebook</a>"
));

$me->getInputs();

/* Database Fields */
$email 			= new FormField($form, $me->getField("emailAddress"));
$firstName 		= new FormField($form, $me->getField("firstName"));
$lastName 		= new FormField($form, $me->getField("lastName"));
$birthdayMonth 	= new FormField($form, $me->getField("birthdayMonth"));
$birthdayDay 	= new FormField($form, $me->getField("birthdayDay"));
$birthdayYear 	= new FormField($form, $me->getField("birthdayYear"));
$gender 		= new FormField($form, $me->getField("gender"));
$password 		= new FormField($form, $me->getField("password"));

$nickname = new FormField($form, array(
	"type" => "text",
	"name" => "nickname",
	"label" => "Nickname",
	"maxLength" => "5"
));

$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Sign Up",
	"fieldClass" => "btn-primary"
));

$form->start();
	?>
	<div class="form-heading">
		<h1 class="form-title">
			<?php echo $form->getHeading(); ?>
		</h1>
		<a href="<?php echo $app->config('page','login'); ?>">I already have an account&nbsp;&rarr;</a>
	</div>
	<div class="form-body">
		<?php if (isset($_GET['leaderId'])) : ?>
			<input type="hidden" name="leaderId" value="<?php echo $_GET['leaderId']; ?>">
		<?php endif; ?>
		<?php
		$form->alert();
		$facebookSignup->render();
		$firstName->render();
		$lastName->render();
		$email->render();
		$password->render();
		?>
		<div class="field-nickname">
			<?php $nickname->render(); ?>
		</div>
		<?php $gender->render(); ?>
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
	</div>
	<div class="form-footer">
		<div class="help-block">
			By submitting this form you are agreeing to our <a href="<?php echo $app->config('page','privacy'); ?>" target="_blank">Privacy Policy</a> and <a href="<?php echo $app->config('page','terms'); ?>" target="_blank">Terms</a>.
		</div>
		<?php $submit->render("field"); ?>
	</div>
<?php $form->stop(); ?>