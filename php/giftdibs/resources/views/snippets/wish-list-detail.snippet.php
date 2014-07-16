<div class="row">
	<?php if ($me->isAlso($they)) : // ADD GIFTS FORM ?>
		<!--
		<div class="col-sm-12 section-add-gifts">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Add gifts</h4>&nbsp;&nbsp;
					<a href="<?php echo $app->config( "page", "new-gift", array("wishListId"=>$wishList->get("wishListId")) ); ?>" class="btn btn-xs btn-default"><small class="glyphicon glyphicon-wrench"></small>&nbsp;&nbsp;Create custom</a>
				</div>
				<div class="panel-body">
					<form class="form-inline row wish-list-search-form" role="search" action="<?php echo $app->config("ajax","search"); ?>">
						<input type="hidden" name="doSearchUsers" value="false">
						<div class="alert alert-success alert-form"></div>
						<div class="form-group col-sm-10 col-xs-9">
							<input type="text" class="form-control" name="query" maxlength="90" placeholder="Keywords or URL..." autocomplete="off">
						</div>
						<div class="form-group col-sm-2 col-xs-3">
							<button type="button" class="btn btn-primary btn-block btn-search" data-loading-text="Wait..."><small class="glyphicon glyphicon-shopping-cart"></small> Find</button>
							<button type="button" class="btn btn-danger btn-block btn-cancel"><span class="glyphicon glyphicon-remove"></span></button>
						</div>
					</form>
				</div>
				<div class="list-group search-results"></div>
			</div>
		</div>
		-->
	<?php endif; ?>
</div>

<?php
$packageOptions = array(
	"userId" => $they->get("userId"),
	"follower" => $me,
	"wishListId" => $wishList->get("wishListId")
);
include SNIPPET_PATH . "list-gifts.snippet.php";
?>