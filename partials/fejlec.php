<?php
	header( 'Content-type: text/html; charset=utf-8' );


	if($_SERVER['SERVER_NAME'] == 'localhost')
		require_once('./partials/allandok.php');
	else
		require_once('./partials/allandokSz.php');
	require_once('./partials/primFgGyujt.php');

	$db = new Allandok();
	$fgLex = new PrimFgGyujt();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html  lang="hu">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Kutatók Éjszakája 2016 – Prím megjelenítő</title>
		<link rel="shortcut icon" href="./partials/favicon.ico">

		<link rel="stylesheet" href="./partials/css/select2.min.css" />
		<link rel="stylesheet" href="./partials/css/dataTables.min.css" />
		<link rel="stylesheet" href="./partials/css/stilus.css" />

		<script type="text/javascript" src="./partials/js/jquery-3.1.0.min.js"></script>
		<script type="text/javascript" src="./partials/js/select2.min.js"></script>
		<script type="text/javascript" src="./partials/js/dataTables.min.js"></script>
		<script type="text/javascript" src="./partials/js/grapics.js"></script>
	</head>
	<body>
		<a href="./index.php" alt="Animáció">Animáció kezelő</a>
		<a href="./feltolt.php" alt="Feltöltés">Feltöltés</a>
		<!-- <a href="./strategia.php" alt="Stratégiák">Stratégiák</a> -->
		<a href="./verziok.php" alt="Verziók">Verziók</a>
		<hr />
