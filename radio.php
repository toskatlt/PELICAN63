<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

?>

<meta charset="utf-8">
<script src="js/radio/jwplayer/jwplayer.js" ></script>
<script>js/radio/jwplayer.key="eW5i/s/wO0wkHkP4n3E2X8qeJolQ+OQEP8dcGw==";</script>

<div class="foto_man"><img src='img/png_man.png' width='50%' height='50%'></div>

<div class="body_radio">
	<table id="radio">
		<tr>
			<td>HumorFM</td>
			<td><div id="HumorFM"></div>
			<script type='text/javascript'>
			jwplayer("HumorFM").setup({
			width: 300,
			height: 25,
			file: 'http://192.168.0.2:8000/HumorFM',
			type: 'mp3'
			});
			</script></td>
		</tr>

		<tr>
			<td>Avtoradio</td>
			<td><div id="Avtoradio"></div>
			<script type='text/javascript'>
			jwplayer("Avtoradio").setup({
			width: 300,
			height: 25,
			file: 'http://192.168.0.2:8000/avtoradio',
			type: 'mp3'
			});
			</script></td>
		</tr>

		<tr>
			<td>ChillOut</td>
			<td><div id="ChillOut"></div>
			<script type='text/javascript'>
			jwplayer("ChillOut").setup({
			width: 300,
			height: 25,
			file: 'http://192.168.0.2:8000/c15_3',
			type: 'mp3'
			});
			</script></td>
		</tr>


		<tr>
			<td>Studio21</td>
			<td><div id="Studio21"></div>
			<script type='text/javascript'>
			jwplayer("Studio21").setup({
			width: 300,
			height: 25,
			file: 'http://192.168.0.2:8000/S21_1',
			type: 'mp3'
			});
			</script></td>
		</tr>
	</table>
</div>

<?php include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 