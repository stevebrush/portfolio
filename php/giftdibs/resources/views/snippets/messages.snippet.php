<h1>Messages</h1>
<p><a href="<?php echo $app->config("page", "new-message"); ?>" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span>New Message</a></p>
<div class="list-group">
	<?php
	$message = new Message($db);
	$messages = $message->query("SELECT Message.messageId, Message.content, Message.timestamp, Message_User.messageStatusId FROM Message LEFT OUTER JOIN Message_User ON Message.messageId = Message_User.messageId WHERE Message_User.userId = {$session->getUserId()} AND Message_User.messageStatusId != '3' GROUP BY Message.messageId ORDER BY Message.timestamp DESC");
	?>
	<?php if ($messages) : ?>
		<?php foreach ($messages as $thisMessage) : ?>
			<?php
			$user = new User($db);
			$users = $user->query("SELECT User.firstName FROM User, Message_User WHERE Message_User.messageId = {$thisMessage->get('messageId')} AND Message_User.userId = User.userId AND Message_User.userId != {$me->get('userId')}");
			$userString = "";
			if ($users) {
				$length = count($users);
				$counter = $length;
				$userString = "";
				while ($k = array_pop($users)) {
					$userString .= $k->get("firstName");
					$counter--;
					switch ($counter) {
						case 1:
							if ($length > 2) {
								$userString .= ", and ";
							} else {
								$userString .= " and ";
							}
						break;
						case 0:
						break;
						default:
							$userString .= ", ";
						break;
					}
				}
			}
			
			// Get latest reply
			$reply = new MessageReply($db);
			$latestReply = $reply->set("messageId", $thisMessage->get("messageId"))->find(1, array("content"), null, " ORDER BY timestamp DESC");
			if (!$latestReply) {
				$latestReply = $thisMessage;
			}
			
			// determine class
			$class = "";
			switch ($thisMessage->get("messageStatusId")) {
				case 1: // read
				default:
					$class = "list-group-item-read";
				break;
				case 2: // unread
					$class = "list-group-item-unread";
				break;
			}
			?>
			<a class="list-group-item <?php echo $class; ?>" href="<?php echo $app->config("page", "message", array("messageId" => $thisMessage->get("messageId"))); ?>">
				<div class="pull-right">
					<span class="text-muted"><?php echo $app->friendlyDate($thisMessage->get("timestamp")); ?>&nbsp;&nbsp;<span class="glyphicon glyphicon-chevron-right"></span></span>
				</div>
				<h5 class="list-group-item-heading"><span class="glyphicon glyphicon-stop"></span>&nbsp;<?php echo $userString; ?></h5>
				<small class="message-summary text-muted"><?php echo $latestReply->get("content"); ?></small>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<!--
<div class="row">
	<div class="col-sm-3">
		<p>
			<a href="#" class="btn btn-default btn-block btn-lg">New message</a>
		</p>
		<ul class="nav nav-stacked nav-pills">
			<li class="active"><a href="#">Inbox <span class="badge">3</span></a></li>
			<li><a href="#">Sent</a></li>
		</ul>
	</div>
	<div class="col-sm-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="#" class="btn btn-default"><input type="checkbox"></a>
				<a href="#" class="btn btn-default">Delete</a>
				<a href="#" class="btn btn-default">Mark as Unread</a>
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<tr>
						<td><input type="checkbox"></td>
						<td>Jaci</td>
						<td><strong>Just sending you an email about what you think Dad wants...</strong></td>
						<td>May 3</td>
					</tr>
					<tr>
						<td><input type="checkbox"></td>
						<td>Jaci</td>
						<td><strong>Just sending you an email about what you think Dad wants...</strong></td>
						<td>May 3</td>
					</tr>
					<tr>
						<td><input type="checkbox"></td>
						<td>Jaci</td>
						<td><strong>Just sending you an email about what you think Dad wants...</strong></td>
						<td>May 3</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
-->
