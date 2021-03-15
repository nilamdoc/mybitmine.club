<!doctype html>
<?php 
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pagestarttime = $mtime; 

?>
<html>

<head>
  <title>WeCapacitate - Alone we're nothting, together WeCapacitate</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
  <meta http-equiv="Content-Security-Policy" content="default-src * data: gap: content: https://ssl.gstatic.com; style-src * 'unsafe-inline'; script-src * 'unsafe-inline' 'unsafe-eval'">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400&family=Raleway:wght@100;300;400&family=Share&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/framework7/css/framework7-bundle.min.css">
  <link rel="stylesheet" href="/css/app.css" type="text/css">
  <link rel="stylesheet" href="/css/framework7-icons.css" type="text/css">
  </head>

<body>
 <div class="page" id="app">

  <div class="page-content">
  <?php echo $this->content(); ?>
  
<?php 
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pageendtime = $mtime;
	$pagetotaltime = ($pageendtime - $pagestarttime);
?>
   
   <?php echo $this->_render('element', 'footer', compact('pagetotaltime'));?>	
  </div>
  </div>
<!-- JS code -->
<script src="/framework7/js/framework7-bundle.min.js"></script>
<script src="/js/app.js"></script>
</body>
</html>