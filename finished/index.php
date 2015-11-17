<?php
require_once('classes/Rate.php');
$objRate = new Rate();
$posts = $objRate->getPosts();
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Thumbs up and down with PHP and jQuery</title>
	<meta name="description" content="Thumbs up and down with PHP and jQuery" />
	<meta name="keywords" content="Thumbs up and down with PHP and jQuery" />
	<link href="/css/core.css" rel="stylesheet" type="text/css" />
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>

<div id="wrapper">

	<p><a href="#" class="reset">Reset</a></p>
	
	<div id="comments">
		
		<?php if (!empty($posts)) { ?>
		
			<?php foreach($posts as $row) { ?>
			
				<div class="comment">
					<span class="name">
						Posted by <?php echo htmlentities(stripslashes($row['full_name'])); ?> on <time datetime="<?php echo date('Y-m-d', strtotime($row['date'])); ?>"><?php echo $row['date_formatted']; ?></time>
					</span>
					<p><?php echo htmlentities(stripslashes($row['comment'])); ?></p>
					<?php echo $objRate->buttonSet($row['id']); ?>
				</div>
			
			<?php } ?>
		
		<?php } else { ?>
			<p>There are currently no comments.</p>
		<?php } ?>
		
	</div>

</div>



<script src="/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="/js/core.js" type="text/javascript"></script>
</body>
</html>