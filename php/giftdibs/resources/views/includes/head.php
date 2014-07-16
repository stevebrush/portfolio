<!doctype html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	
	<meta property="og:title" content="<?php echo (!$page->getMeta("title")) ? $app->config("meta","title") : $page->getMeta("title"); ?>">
	<meta property="og:url" content="<?php echo $app->currentUrl(); ?>">
	<meta property="og:image" content="<?php echo (!$page->getMeta("image")) ? $app->config("meta","image") : $page->getMeta("image"); ?>">
	<meta property="og:description" content="<?php echo (!$page->getMeta("description")) ? $app->config("meta","description") : $page->getMeta("description"); ?>">
	
	<title><?php echo $page->getTitle(); ?></title>
	
	<link rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<!--[if lt IE 9]>
		<script src="//cdn.jsdelivr.net/html5shiv/3.7.0/html5shiv.js"></script>
		<link href="//cdn.jsdelivr.net/respond/1.4.2/cross-domain/respond-proxy.html" id="respond-proxy" rel="respond-proxy">
		<link href="img/respond.proxy.gif" id="respond-redirect" rel="respond-redirect">
		<script src="//cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
		<script src="//cdn.jsdelivr.net/respond/1.4.2/cross-domain/respond.proxy.js"></script>
	<![endif]-->
	
</head>
<body>
	<?php include INCLUDE_PATH . "facebook-js-sdk.php"; ?>