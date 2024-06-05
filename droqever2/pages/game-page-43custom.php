<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
		<link rel="stylesheet" href="/scripts/corestyle.css">
		<title><?php echo $gametitle;?></title>
	</head>
	<body>
		<canvas id="canvas">
			Your browser does not support the canvas tag.
		</canvas>

		<noscript>
			Your browser does not support JavaScript.
		</noscript>

		<div id="status">
			<!-- <img id="status-splash" src="/engine_43custom/splash.png" alt=""> -->
			<progress id="status-progress"></progress>
			<div id="status-notice"></div>
		</div>

		<script src="/engine_43custom/godot.js?d=2&v=2"></script>
		<script src="/engine_43custom/cat.js?d=2&v=2" defer></script>
		<script defer>
			setTimeout(()=>{
				Cat.boot("<?php echo $gamezippath; ?>", <?php echo $gamezipsize; ?>);
			},100);
		</script>
		<script>
			const canvasEl = document.getElementById("canvas")
			const canvasParent = canvasEl.parentElement;

			function isArrayOrTypedArray(x) {
				return Boolean(
					x && (typeof x === 'object') && (Array.isArray(x) || (ArrayBuffer.isView(x) && !(x instanceof DataView)))
				);
			}

			window.addEventListener('canvasGameLoaded', (_ev)=>{
				console.log(`game loaded`);
				resizeCanvas();
			});

			let gameWidth = 200;
			let gameHeight = 200;

			window.addEventListener('setGameSize', (size)=>{
				gameWidth = Number(size['w']);
				gameHeight = Number(size['h']);
				console.log(`setGameSize(${gameWidth}, ${gameHeight})`);
				resizeCanvas();
			})
			window.addEventListener('resize', (_ev)=>{ resizeCanvas(); }, true);

			function resizeCanvas() {
				let pw = canvasParent.clientWidth;	
				let ph = canvasParent.clientHeight;
				let bw = gameWidth;
				let bh = gameHeight;
				let scale = Math.floor(Math.min(pw/bw, ph/bh));
				if (scale < 1) { scale = 1; }
				let sw = bw * scale;
				let sh = bh * scale;
				canvasEl.style.position = "absolute";
				canvasEl.style.left = `${Math.floor((pw-sw)/2)}px`;
				canvasEl.style.top = `${Math.floor((ph-sh)/2)}px`;
				canvasEl.width = sw;
				canvasEl.height = sh;
			}
		</script>
		<div id="countdown"><span id="countdown_ts"></span></div>
		<?php 
		if (isset($gamesourcelink))  {
			echo "<div id='gamesourcelink'><a href='$gamesourcelink'>view game source</a> <span style='font-size:85%;'>(might be private)</span></div>";
		}
		?>
		<div id="imdroqen"><a href='/droqen.php'>i'm droqen!</a></div>
	</body>
	<script src="/scripts/cycling_pastel_bg.js"></script>
	<script>
		var distance = <?php echo $lifetime_remaining; ?>;
		// count down every 5 seconds
		function updateCountdown() {
			var days = Math.round(distance / (60 * 60 * 24));
			var hours = Math.round((distance % (60 * 60 * 24)) / (60 * 60));
			var minutes = Math.round((distance % (60 * 60)) / (60));
			var seconds = Math.round((distance % (60)) / 1);
			if (days > 0) { document.getElementById("countdown_ts").innerText = `game disappears in ${days} day${days==1?'':'s'}`; }
			else if (hours > 0) { document.getElementById("countdown_ts").innerText = `game disappears in ${hours} hour${hours==1?'':'s'}`; }
			else if (minutes > 0) { document.getElementById("countdown_ts").innerText = `game disappears in ${minutes} minute${minutes==1?'':'s'}`; }
			else if (seconds > 0) { document.getElementById("countdown_ts").innerText = `game disappears in ${seconds} second${seconds==1?'':'s'}`; }
			else {
				document.getElementById("countdown_ts").innerText = `this game link has expired. byeee <3`;
				document.getElementById("canvas").style.transition = "opacity 60s ease";
				document.getElementById("canvas").style.opacity = "0";
				clearInterval(x);
				setTimeout(function(){window.location.reload();}, 60000); // reload page in 60 seconds. once it has completely vanished...
			}
		}
		var x = setInterval(function() {
			distance -= 5;
			updateCountdown();
		}, 5000);
		setTimeout(function() {
			updateCountdown(); // do it asap
			document.getElementById("countdown_ts").style.opacity = 1;
		}, 50);
	</script>
</html>

