<?php
	/** @var array $preparedFiles */
	/** @var string $projectPath */
	/** @var string $projectName */
	/** @var string $lastUpdateTime */
?>
<div class="panel panel-default panel-info">
	<div class="panel-heading">
		<small><?php echo date('d.m.Y H:i:s'); ?></small> Check patch <b><?php echo $projectName; ?></b>:
		<button type="button" class="pull-right close-btn btn btn-default btn-xs">
			<span class="glyphicon glyphicon-remove-circle"></span>
		</button>
		<button type="button" class="pull-right collapse-btn btn btn-default btn-xs">
			<span class="size glyphicon glyphicon-collapse-up"></span>
		</button>
	</div>
	<div class="panel-body" style="overflow: auto;">
		<span class="label label-info">Quick search:</span> <input id="search" type="text"/><br/>
		<span class="label label-info">Filter:</span>
		<input type="radio" name="filter" id="filter-all"/><label for="filter-all">all</label>
		<input type="radio" name="filter" id="filter-edited"/><label for="filter-edited" class="info">edited</label>
		<input type="radio" name="filter" id="filter-created"/><label for="filter-created" class="success">created</label>
		<input type="radio" name="filter" id="filter-deleted"/><label for="filter-deleted" class="warning">deleted</label>
		<input type="radio" name="filter" id="filter-broken"/><label for="filter-broken" class="danger">broken</label>
		<br/>
		<span class="label label-info">Path:</span> <i><?php echo $patchPath; ?></i><br/>
		<span class="label label-info">Files (<?php echo $affectedFilesCount; ?>):</span><br/>
		<hr style="margin: 10px 0;"/>
		<b class="info">Edited:</b><br/>
		<?php foreach ($checked['edited'] as $key => $file): ?>
			<p class="log-text edited"><?php echo $file; ?><span class="bimbo"></span></p>
		<?php endforeach; ?>
		<b class="success">Created:</b><br/>
		<?php foreach ($checked['created'] as $key => $file): ?>
			<p class="log-text created"><?php echo $file; ?><span class="bimbo"></span></p>
		<?php endforeach; ?>
		<b class="warning">Deleted:</b><br/>
		<?php foreach ($checked['deleted'] as $key => $file): ?>
			<p class="log-text deleted"><?php echo $file; ?><span class="bimbo"></span></p>
		<?php endforeach; ?>
		<b class="danger">Broken:</b><br/>
		<?php foreach ($checked['broken'] as $key => $file): ?>
			<p class="log-text broken"><?php echo $file; ?><span class="bimbo"></span></p>
		<?php endforeach; ?>
	</div>
</div>