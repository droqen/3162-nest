<!DOCTYPE html>
<html xmlns='http://www.w3.org/1999/xhtml' lang='' xml:lang=''>

<?php
	$wasm_file = "index.wasm";
?>

<head>
	<meta charset='utf-8' />
	<meta name='viewport' content='width=device-width, user-scalable=no' />
	<title>droqever</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700" rel="stylesheet">
	<link rel="stylesheet" href="/droqever-base-style.css">
	<link rel="stylesheet" href="/bridge/touch-stick.css">
	<style type='text/css'>
		<?php
			$xoff = rand(20,40);
			$yoff = rand(10,30);
			$rotx = rand(50,100)*0.01;
			$roty = rand(50,100)*0.01;
			$rotz = rand(50,100)*0.01;
			if (rand(0,1)==1) { $rotx *= -1; }
			if (rand(0,1)==1) { $roty *= -1; }
			if (rand(0,1)==1) { $rotz *= -1; }
			if (rand(0,1)==1) { $rotz *= -1; }
			if (rand(0,3) <3) { $yoff *= -1; }
			$xoffvw = $xoff . "vw";
			$yoffvh = $yoff . "vh";
			$degoff = rand(40,50) . "deg";
			$deghov = rand(20,30) . "deg";
		?>
		#canvas {
			transform: <?php echo "translate3d($xoffvw, $yoffvh, 0) rotate3d($rotx,$roty,$rotz,$degoff)"?>;
		}
		#canvas:hover:not(:focus) {
			transform: <?php echo "translate3d($xoffvw, $yoffvh, 0) rotate3d($rotx,$roty,$rotz,$deghov)"?>;
		}
	</style>
	<link id='-droqever-favicon' rel='icon' type='image/png' href='/droqqoon_512_green.png' />
	<!-- <link rel='apple-touch-icon' href='UNUSED' /> -->

</head>

<body>
	<div id="touch_stick_wrapper"></div>
	<div id="touch_button_wrapper"></div>
	<?php include "db/dbconn.php" ?>
	<?php
		global $currentGameKeyName;
		global $currentGameYear;
		global $currentGameMonth;
		$keyname = $_GET['game'];
		$gamefilepath = "";
		require_once "db/dbconn.php";
		if (!$conn) { die("No connection"); }
		if ($keyname) {
			$sth = $conn->query("SELECT release_date, title, longdesc, zipname FROM games WHERE zipname = '$keyname'");
		} else {
			$sth = $conn->query("SELECT release_date, title, longdesc, zipname FROM games ORDER BY release_date DESC LIMIT 1");
		}
		$row = $sth -> fetch();
		if ($row) {
			$year = "0000";
			$month = "00";
			$day = "00";
			$explodedDate = explode("-", $row[0]);
			$keyname = $row[3];
			if (count($explodedDate)==3) {
				$year = $explodedDate[0];
				$month = $explodedDate[1];
				$day = $explodedDate[2];
				$gamefilepath = "/games/$year/$month/$day--$keyname";
				$currentGameKeyName = $keyname;
				$currentGameMonth = $month;
				$currentGameYear = $year;
			}

			$gameTitle = $row[1];
			$gameDescription = $row[2];
		}
	?>
	<script>
		function showcal(mo, yr) {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("gamelist").innerHTML = this.responseText;
				}
			};
			xmlhttp.open("GET", "index-calendar.php?mo=" + mo.toString() + "&yr=" + yr.toString(), true);
			xmlhttp.send();
		}
	</script>
	<div id='gamelist' style='position:fixed; left: 10px; top: 10px;'>
		<?php include "index-calendar.php" ?>
	</div>
	<script src='/bridge/godot-bridge.js' type='text/javascript'></script>
	<script src='/bridge/touch-stick.js' type='text/javascript'></script>
	<script type='text/javascript'>
		GODOT_ENGINE_CONFIG = {
			"basePath": "/g3/<?php echo $wasm_file ?>",
			"args": [], "canvasResizePolicy": 0, "experimentalVK": false, "focusCanvas": true, "gdnativeLibs": [],
			"executable": "<?php echo $gamefilepath ?>",
			"mainPack": "<?php echo $gamefilepath ?>.zip",
			"fileSizes": { "<?php echo $gamefilepath ?>.zip": 45680, "<?php echo $wasm_file ?>": 17865444 },
		};
		console.log(GODOT_ENGINE_CONFIG);
	</script>
	<?php
		if ($gamefilepath) {
			if (file_exists(__DIR__."$gamefilepath.zip")) {
				include 'index-play-body.php';
			} else {
// echo "
// 	<p>Game not found '<b>$keyname</b>'</p>
// ";
			}
		} else {
// echo "
// 	<p>Try adding <b>?game=test</b> to the end of the url!</p>
// ";
		}
	?>
</body>

</html>