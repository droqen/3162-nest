<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
		<title><?php echo $gametitle;?></title>
		<style>
html, body, #canvas {
	margin: 0;
	padding: 0;
	border: 0;
}

body {
	color: white;
	background-color: black;
	overflow: hidden;
	touch-action: none;
}

#canvas {
	display: block;
	filter: blur(0.33vmin);
	transition: filter 2.33s ease-in;
}

#canvas:focus {
	outline: none;
	filter: none;
	transition: filter .15s linear;
}

#status, #status-splash, #status-progress {
	position: absolute;
	left: 0;
	right: 0;
}

#status, #status-splash {
	top: 0;
	bottom: 0;
}

#status {
	background-color: black;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	visibility: hidden;
}

#status-splash {
	max-height: 100%;
	max-width: 100%;
	margin: auto;
}

#status-progress, #status-notice {
	display: none;
}

#status-progress {
	bottom: 10%;
	width: 50%;
	margin: 0 auto;
}

#status-notice {
	background-color: #5b3943;
	border-radius: 0.5rem;
	border: 1px solid #9b3943;
	color: #e0e0e0;
	font-family: 'Noto Sans', 'Droid Sans', Arial, sans-serif;
	line-height: 1.3;
	margin: 0 2rem;
	overflow: hidden;
	padding: 1rem;
	text-align: center;
	z-index: 1;
}
		</style>
		<style>
body {
	min-height: 100vh;
}
#canvas {
	/* pico-8 pixel perfect */
	image-rendering: optimizeSpeed;
	image-rendering: -moz-crisp-edges;
	image-rendering: -webkit-optimize-contrast;
	image-rendering: optimize-contrast;
	image-rendering: pixelated;
	-ms-interpolation-mode: nearest-neighbor;
	border: 0;
	border-image-width: 0;
	outline: none;
}
		</style>
		<!-- <link id="-gd-engine-icon" rel="icon" type="image/png" href="index.icon.png" />
<link rel="apple-touch-icon" href="index.apple-touch-icon.png"/> -->

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
		<div id="countdown">game expires in . . . <span id="countdown_ts"></span></div>
		<?php 
		if (isset($gamesourcelink))  {
			echo "<a href='$gamesourcelink'>source code</a>";
		}
		?>
	</body>
	<script>
		function getRandomColor() {
			var letters = 'ABCDEF'; // '0123456789ABCDEF';
			var numletters = 6; // 16
			var color = '#';
			for (var i = 0; i < 6; i++) {
				color += letters[Math.floor(Math.random() * numletters)];
			}
			return color;
		}
		document.getElementById("status-progress").style.accentColor = getRandomColor();
	</script>
	<script>
		var distance = <?php echo $lifetime_remaining; ?>;
		// count down 1 every second . . .
		var x = setInterval(function() {
			distance -= 1;
			var days = Math.floor(distance / (60 * 60 * 24));
			var hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
			var minutes = Math.floor((distance % (60 * 60)) / (60));
			var seconds = Math.floor((distance % (60)) / 1);
			if (days > 7) { document.getElementById("countdown_ts").innerText = `over a week`; }
			else if (days > 1) { document.getElementById("countdown_ts").innerText = `a few days`; }
			else if (days == 1) { document.getElementById("countdown_ts").innerText = `a day or two`; }
			else if (hours > 1) { document.getElementById("countdown_ts").innerText = `${hours} hours`; }
			else if (hours == 1 || minutes > 35) { document.getElementById("countdown_ts").innerText = `an hour or two`; }
			else if (minutes > 20) { document.getElementById("countdown_ts").innerText = `half an hour`; }
			else if (minutes > 1) { document.getElementById("countdown_ts").innerText = `${minutes} minutes`; }
			else if (minutes == 1) { document.getElementById("countdown_ts").innerText = `a minute or two`; }
			else if (seconds > 10) { document.getElementById("countdown_ts").innerText = `under a minute`; }
			else { document.getElementById("countdown_ts").innerText = `a handful of seconds`; }
		}, 1000);
	</script>
</html>

