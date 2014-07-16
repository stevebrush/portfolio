<?php $they->getInputs(); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">
				<?php echo $they->firstNamePossessive() . " gift guide"; ?>
			</h4>
		</div>
		<div class="modal-body">
		
			<div class="form-horizontal form-striped">
				<div class="form-group">
					<div class="col-sm-3 text-muted control-label"><?php echo $they->getField("interests")["label"]; ?></div>
					<div class="col-sm-9">
						<p class="form-control-static"><?php echo $they->get("interests"); ?></p>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 text-muted control-label"><?php echo $they->getField("favoriteStores")["label"]; ?></div>
					<div class="col-sm-9">
						<p class="form-control-static"><?php echo $they->get("favoriteStores"); ?></p>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 text-muted control-label"><?php echo $they->getField("shirtSize")["label"]; ?></div>
					<div class="col-sm-9">
						<p class="form-control-static"><?php echo $they->get("shirtSize"); ?></p>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 text-muted control-label"><?php echo $they->getField("shoeSize")["label"]; ?></div>
					<div class="col-sm-9">
						<p class="form-control-static"><?php echo $they->get("shoeSize"); ?></p>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 text-muted control-label"><?php echo $they->getField("pantSize")["label"]; ?></div>
					<div class="col-sm-9">
						<p class="form-control-static"><?php echo $they->get("pantSize"); ?></p>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 text-muted control-label"><?php echo $they->getField("hatSize")["label"]; ?></div>
					<div class="col-sm-9">
						<p class="form-control-static"><?php echo $they->get("hatSize"); ?></p>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 text-muted control-label"><?php echo $they->getField("ringSize")["label"]; ?></div>
					<div class="col-sm-9">
						<p class="form-control-static"><?php echo $they->get("ringSize"); ?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btn-block" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>