<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id'] == $_COOKIE['id']) {
		
		/*
		if (!file_exists("video/285")) { mkdir("video/285", 0777); }
		exec('ping -c1 -w1 192.168.0.100 > /dev/null && echo 1|| echo 0', $output);
		if ($output[0] == '1') {
			if (!file_exists("video/285/192.168.0.100")) { mkdir("video/285/192.168.0.100", 0777); }
			if (!file_exists("video/285/192.168.0.100/full/")) { mkdir("video/285/192.168.0.100/full/", 0777); }

			$pic_com = file_get_contents("http://admin:742620@192.168.0.100/ISAPI/Streaming/channels/401/picture", false, $ctx);
			
			file_put_contents("video/285/192.168.0.100/4.jpg", $pic_com);
			
			$img = "video/285/192.168.0.100/4.jpg";
			$imsize = getimagesize($img);
			
			$width_img = $imsize[0]; //ширина	
			$height_img = $imsize[1]; // высота
			
			$width = '1200';
			$height = '982';
			
			resize($width, $height, '4', $width_img, $height_img, '285', '192.168.0.100');
			$randval = rand();
			
			echo "<center><a href='video/285/192.168.0.100/full/4.jpg?n=".$randval."' class='highslide' onclick='return hs.expand(this)'><img src='video/285/192.168.0.100/4.jpg?n=".$randval."' style='width: 350px;border-color: black;border-width: 2px;' class='highslide'></a></center>";
		}
		*/
		
		echo "<div><center>";
		echo "<iframe width='640' height='382' src='https://rtsp.me/embed/GErGYBy5/' frameborder='0' allowfullscreen></iframe>";
		echo "<iframe width='640' height='382' src='https://rtsp.me/embed/QaYbhDhZ/' frameborder='0' allowfullscreen></iframe>";
		echo "</div>";
 	
	}
}
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");
?>