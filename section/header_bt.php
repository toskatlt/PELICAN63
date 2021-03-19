<?php
header("Content-Type: text/html; charset=utf-8");
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

//echo "<link type='text/css' rel='stylesheet' href='/css/table.css'/><br>";
//echo "<link type='text/css' rel='stylesheet' href='/css/style.css'/><br>";
//echo "<link type='text/css' rel='stylesheet' href='/css/nav.css'/><br>";
//echo "<link type='text/css' rel='stylesheet' href='/css/input.css'/><br>";


/// Optional JavaScript -->
/// jQuery first, then Popper.js, then Bootstrap JS -->
echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>';
echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>';
echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>';

include $_SERVER["DOCUMENT_ROOT"]."/config.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_object.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_terminals.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_jabber.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_admin.php");

$date_time = date("y-m-d H:i:s");

echo "<script src='/highslide/highslide.js'></script><br>";
/*
echo "<script src='/jquery/jquery-3.1.0.min.js'></script><br>";
echo "<script src='/jquery/jquery.metadata.js'></script><br>";
echo "<script src='/jquery/jquery.tablesorter.js'></script><br>";
echo "<script src='/js/js.js'></script><br>";
*/
echo "<script src='/js/spin/spin.js'></script><br>";
echo "<script src='/js/spin/hideshow.j'></script><br>"; 


echo '<head>';
	/// Bootstrap CSS 
	echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">';
	echo '<link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">';
	echo '<link rel="stylesheet" href="/bootstrap/css/bootstrap-grid.min.css">';
	echo '<link rel="stylesheet" href="/bootstrap/css/bootstrap-reboot.min.css">';
	echo "<link rel='stylesheet' href='/highslide/highslide.css'/><br>";
	/// Required meta tags
	echo '<meta charset="utf-8">';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
	echo '<title>';
		if (isset($_GET['id'])) {	
			$selectObject = selectObject ($dbcnx, $_GET['id']);
			echo $selectObject['name'];
		} elseif (isset($_GET['email'])) {
			$objectFromEmail = objectFromEmail($dbcnx, $_GET['email']);
			echo $objectFromEmail['name'];
		} else {
			echo "Пеликан";
		}
	echo '</title>';
echo '</head>';
echo "<body>";
