<?php defined('DOCROOT') or die('Denied direct script access'); ?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Menu</title>
	<?php Attach::script(array('jquery-1.10.2.min', 'bootstrap', 'inputmask', 'php', 'index', 'jquery-textrange'), false); ?>
	<?php Attach::style(array('bootstrap', 'style'), false); ?>
</head>
<body>
<div id="wrapper" class="container">
	<ul id="nav-tabs" class="nav nav-tabs">
		<li class="active"><a href="#make">Make patch</a></li>
		<li><a href="#check">Check patch</a></li>
		<li><a href="#show">Log <span class="badge hide">0</span></a></li>
	</ul>
<!----------------------------------------------------------- Tab Create patch -------------------------------------------------->
	<div class="tab-content">
		<div class="tab-pane fade in active" id="make">
			<form action="/" role="form" id="make-patch-form">
				<div class="form-group">
					<label for="project">Project path <small>absolute, <span class="label label-warning">without</span> last slash</small></label>
					<input type="text" name="project" class="form-control" id="project">
				</div>
				<div class="form-group">
					<label for="patch-path">Patch path <small>absolute, <span class="label label-warning">without</span> last slash</small></label>
					<input type="text" name="patch_path" class="form-control" id="patch-path">
				</div>
				<div id="ignored-files" class="form-group">
					<label for="ignored">Ignored Files <small>based on <code>.gitignore</code>. Can be typed manually one rule on line</small></label>
					<textarea name="ignored" class="form-control" id="ignored" rows="5"></textarea>
				</div>
				<div class="form-group">
					<label for="date-time">Last update date <small>xx.xx.xx xx:xx:xx</small></label>
					<input type="text" name="datetime" value="" class="form-control" data-mask="99.99.9999 99:99:99" id="date-time">
				</div>
				<button type="submit" id="submit" class="btn btn-default">Build</button>
				<span class="build-result label hide"></span>
			</form>
		</div>
<!----------------------------------------------------------- Tab Logs -------------------------------------------------->
		<div class="tab-pane fade" id="show">
			<div class="show-container">
				<div class="show-log"></div>
			</div>
		</div>
<!----------------------------------------------------------- Tab Check Patch -------------------------------------------------->
		<div class="tab-pane fade" id="check">
			<form action="/check" role="form" id="make-check-form">
				<div class="form-group">
					<label for="check-patch-path">Patch path <small>absolute, <span class="label label-warning">without</span> last slash</small></label>
					<input type="text" name="patch_path" class="form-control" id="check-patch-path">
				</div>
				<div class="form-group">
					<label for="ignored">Source <small><span class="label label-warning">One</span> path per line</small></label><br/>
					<input name="source" checked="checked" type="radio" id="git-result" value="git"/>
					<label for="git-result">.git comment</label>
					<input name="source" id="plain-result" type="radio" value="plain"/>
					<label for="plain-result">plain text</label>
				</div>
				<div class="form-group">
					<label for="paths-to-check">Total files: <span class="count">0</span> <small>will update on focus, blur, paste events</small></label>
					<textarea name="check" spellcheck="false" class="form-control" id="paths-to-check" cols="30" rows="25"></textarea>
				</div>
				<button type="submit" id="run" class="btn btn-default">Run</button>
				<span class="build-result label hide"></span>
			</form>
		</div>
	</div>
</div>
</body>
</html>