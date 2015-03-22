<?php use Cygnite\AssetManager\Asset; ?>

<div class="pull-right">
    <?php echo Asset::link('product', 'Back', array('class' => 'btn btn-default btn-small btn-inverse')); ?>
</div>

<div class="form-horizontal">
    <h2>Showing product #<?php echo $this->record->id; ?></h2>

    			<div class="form-group">
                    <label class="col-sm-2 control-label">Product Name</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><span><?php echo $this->record->product_name; ?></span></p>
                    </div>
                </div>
			<div class="form-group">
                    <label class="col-sm-2 control-label">Category</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><span><?php echo $this->record->category; ?></span></p>
                    </div>
                </div>
			<div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><span><?php echo $this->record->description; ?></span></p>
                    </div>
                </div>
			<div class="form-group">
                    <label class="col-sm-2 control-label">Validity</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><span><?php echo $this->record->validity; ?></span></p>
                    </div>
                </div>
			<div class="form-group">
                    <label class="col-sm-2 control-label">Price</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><span><?php echo $this->record->price; ?></span></p>
                    </div>
                </div>
			<div class="form-group">
                    <label class="col-sm-2 control-label">Created At</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><span><?php echo $this->record->created_at; ?></span></p>
                    </div>
                </div>
			<div class="form-group">
                    <label class="col-sm-2 control-label">Updated At</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><span><?php echo $this->record->updated_at; ?></span></p>
                    </div>
                </div>


</div>