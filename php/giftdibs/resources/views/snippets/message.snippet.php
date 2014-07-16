<?php
$author = new User($db);
$author = $author->set("userId", $thisMessage->get("userId"))->find(1);
$reply = new MessageReply($db);
$replies = $reply->set("messageId", $thisMessage->get("messageId"))->find();

// update message status
$message_user = new Message_User($db);
$messageUser = $message_user->set(array(
	"messageId" => $thisMessage->get("messageId"),
	"userId" => $me->get("userId")
))->find(1);
if ($messageUser) {
	$messageUser->set("messageStatusId", "1")->update(); // make "read"
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h1><?php echo $userString; ?></h1>
	</div>
	<div class="panel-nav">
		<!--<a href="#" class="btn btn-default"><input type="checkbox"></a>-->
		<a href="<?php echo $app->config("page", "messages"); ?>" class="btn btn-default btn-sm"><small class="glyphicon glyphicon-arrow-left"></small>&nbsp;&nbsp;Messages</a>
		<a href="<?php echo $app->config("ajax", "delete-message"); ?>" class="btn btn-default btn-sm btn-data" data-signature="<?php echo $me->createSignature($thisMessage->get("messageId")); ?>" data-message-id="<?php echo $thisMessage->get("messageId"); ?>" data-redirect="<?php echo $app->config("page", "messages"); ?>"><small class="glyphicon glyphicon-trash"></small>&nbsp;&nbsp;Delete</a>
		<a href="<?php echo $app->config("ajax", "message-mark-unread"); ?>" class="btn btn-default btn-sm btn-data" data-signature="<?php echo $me->createSignature($thisMessage->get("messageId")); ?>" data-message-id="<?php echo $thisMessage->get("messageId"); ?>" data-redirect="<?php echo $app->config("page", "messages"); ?>"><small class="glyphicon glyphicon-inbox"></small>&nbsp;&nbsp;Mark as Unread</a>
	</div>
	<div class="panel-body">
			
		<!-- AUTHOR -->
		<div class="media message">
			<div class="pull-left thumbnail"><img src="<?php echo $author->getThumbnail()->size("sm")->get("src"); ?>"></div>
			<div class="media-body">
				<?php echo $thisMessage->get("content"); ?><br>
				<small>
					<a href="#"><?php echo $author->fullName(); ?></a><span class="text-muted"> &middot; <?php echo $app->friendlyDate($thisMessage->get("timestamp")); ?></span>
				</small>
			</div>
		</div>
		
		<!-- REPLIES -->
		<?php if ($replies) : ?>
			<?php 
			$u = new User($db);
			?>
			<?php foreach ($replies as $reply) : ?>
				<?php
				$user = $u->set("userId", $reply->get("userId"))->find(1);
				?>
				<div class="media message">
					<div class="pull-left thumbnail"><img src="<?php echo $user->getThumbnail()->size("sm")->get("src"); ?>"></div>
					<div class="media-body">
						<?php echo $reply->get("content"); ?><br>
						<small>
							<a href="<?php echo $app->config("page", "profile", array("userId" => $user->get("userId"))); ?>"><?php echo $user->fullName(); ?></a><span class="text-muted"> &middot; <?php echo $app->friendlyDate($reply->get("timestamp")); ?></span>
						</small>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="panel-footer" id="message-reply-container">
		<div class="media">
			<div class="pull-left thumbnail" style="width:40px;height:40px;"><img src="<?php echo $me->getThumbnail()->size("sm")->get("src"); ?>"></div>
			<div class="media-body">
				<?php include FORM_PATH . "message-reply.form.php"; ?>
			</div>
		</div>
	</div>
</div>