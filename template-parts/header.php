<?php
	require($server . 'vendor/autoload.php');
	
	new classes\checkData();
	$database = new classes\database();
	$workWithApi = new classes\workWithApi();
	$tools = new classes\tools();
	
	$database = $database->connect;
	$path = $tools->getServerPath();
	$home = $tools->getHomeUrl();
	$page = $tools->isPage();
	$bot = $workWithApi->bot;
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page['title'] ?></title>
	<link rel="stylesheet" href="<?php echo $home ?>src/css/common.css">
	<link rel="stylesheet" href="<?php echo $home ?>src/css/style.css">
</head>
<body>
	<header class="header">
		<h1 class="title">Бот 1.0</h1>
		<a href="//vk.com/<?php echo $bot[0]['domain'] ?>" target="_blank" class="data">
			<img src="<?php echo $bot[0]['photo_50'] ?>" alt="">
			<div class="name"><?php echo $bot[0]['first_name'] . ' ' . $bot[0]['last_name'] ?></div>
		</a>
	</header>
	<div class="wrap">
		<?php require $path . 'template-parts/aside.php'; ?>
		<main class="main">
		<h2 class="page-title"><?php echo $page['title'] ?></h2>