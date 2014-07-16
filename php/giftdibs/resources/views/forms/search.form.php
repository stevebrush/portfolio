<!--
<a href="#" class="btn btn-primary btn-xs-search navbar-btn pull-right" data-target="#search"><span class="glyphicon glyphicon-search"></span></a>
<a href="#" class="btn btn-primary btn-xs-cancel navbar-btn pull-right" data-target="#search">Cancel</a>
<div id="search" class="col-lg-6 col-md-5 col-sm-5">
	<form class="navbar-form quicksearch-form" role="search" action="<?php echo $app->config('ajax','search'); ?>">
		<div class="input-group">
			<input type="text" class="form-control" name="query" maxlength="90" placeholder="Find people, gift ideas" autocomplete="off">
			<span class="input-group-btn">
				<button class="btn btn-default btn-search" type="button"><span class="glyphicon glyphicon-search"></span></button>
				<button class="btn btn-danger btn-cancel" type="button"><strong>&times;</strong></button>
			</span>
		</div>
		<div class="list-group search-results"></div>
	</form>
</div>
-->
<?php
$form = new Form(array(
	"slug" => "search",
	"cssClass" => "navbar-form navbar-left gd-form-search",
	"heading" => "Find Someone",
	"action" => $app->config("ajax", "search"),
	"apiCallback" => "search"
));
$query = new FormField($form, array(
	"type" => "text",
	"name" => "query",
	"placeholder" => "Find someone",
	"label" => "Search",
	"autoComplete" => "false"
));
$form->start();
?>
<input type="hidden" name="target" value="#quick-search-results">
<div class="form-group">
	<label class="sr-only">Find Someone</label>
	<?php $query->render("field"); ?>
	<button class="btn btn-default btn-submit" type="submit">
		<span class="glyphicon glyphicon-search glyphicon-only"></span>
		<span class="sr-only">Submit</span>
	</button>
	<button class="btn btn-default btn-cancel" type="submit">
		<span class="glyphicon glyphicon-remove glyphicon-only"></span>
		<span class="sr-only">Cancel</span>
	</button>
</div>
<div class="search-results" id="quick-search-results"></div>
<?php
$form->stop();
?>