<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<?php Attach::script('bootstrap', false); ?>
	<?php Attach::style('bootstrap', false); ?>
</head>
<body>
	Affected files: <?php echo isset($affectedFilesCount) ? $affectedFilesCount : 0; ?>
	<br/>
	<a href="/">Back</a>
	<br/><br/>
	<?php foreach ($preparedFiles as $key => $val): ?>
		<?php if (is_array($val)): ?>
			<?php foreach ($val as $key2 => $file): ?>
				<p style="margin-left: 20px; margin-top: 5px; margin-bottom: 5px;"><?php echo $key . DIRECTORY_SEPARATOR . $file; ?></p>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
</body>
</html>