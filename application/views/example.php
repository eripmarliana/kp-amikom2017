<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
</head>
<body>
	<div>
		<a href='<?php echo site_url('welcome/groups')?>'>Groups</a> |
		<a href='<?php echo site_url('welcome/issue')?>'>Issue</a> |
		<a href='<?php echo site_url('welcome/issue_checklist')?>'>Issue Checklist</a> |
		<a href='<?php echo site_url('welcome/issue_review')?>'>Issue Review</a> | 
		<a href='<?php echo site_url('welcome/employees_management')?>'>Employees</a> |		 
		<a href='<?php echo site_url('welcome/film_management')?>'>Films</a> |
		<a href='<?php echo site_url('welcome/multigrids')?>'>Multigrid [BETA]</a>
		
	</div>
	<div style='height:20px;'></div>  
    <div>
		<?php echo $output; ?>
    </div>
</body>
</html>
