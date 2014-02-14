<?php
	/** @var array $preparedFiles */
	/** @var string $projectPath */
	/** @var string $projectName */
	/** @var string $lastUpdateTime */
?>
<div class="panel panel-default panel-info">
	<div class="panel-heading">
		<small><?php echo date('d.m.Y H:i:s'); ?></small> Make patch for <b><?php echo $projectName; ?></b>:
		<button type="button" class="pull-right close-btn btn btn-default btn-xs">
			<span class="glyphicon glyphicon-remove-circle"></span>
		</button>
		<button type="button" class="pull-right collapse-btn btn btn-default btn-xs">
			<span class="size glyphicon glyphicon-collapse-up"></span>
		</button>
	</div>
	<div class="panel-body" style="overflow: auto;">
		<span class="label label-info">Quick search:</span> <input id="search" type="text"/><br/>
		<span class="label label-info">From:</span><i> <?php echo date('d.m.Y H:i:s', $lastUpdateTime); ?></i><br/>
		<span class="label label-info">Path:</span> <i><?php echo $patchPath; ?></i><br/>
		<span class="label label-info">Files (<?php echo $affectedFilesCount; ?>):</span><br/>
		<hr style="margin: 10px 0;"/>
		<?php foreach ($preparedFiles as $key => $path): ?>
			<?php if (is_array($path)): ?>
				<?php foreach ($path as $key2 => $file): ?>
					<p class="log-text"><?php echo str_replace(strtolower($projectPath), '', strtolower($key)) . DIRECTORY_SEPARATOR . $file; ?><span class="bimbo"></span></p>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>