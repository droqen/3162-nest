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
		<div id="awaken_scroll_target" style="position:absolute; top:100px;"></div>
		<div id="gamefp" class="fullpage">
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

			<script src="/beepbox/beepbox_synth_jummb.js"></script>
			<script src="/engine_43/godot.js?d=18&v=3"></script>
			<script src="/engine_43/cat.js?d=18&v=1" defer></script>
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

				let gameWidth = undefined;
				let gameHeight = undefined;

				window.addEventListener('setGameSize', (size)=>{
					gameWidth = Number(size['w']);
					gameHeight = Number(size['h']);
					console.log(`setGameSize(${gameWidth}, ${gameHeight})`);
					resizeCanvas();
				})
				window.addEventListener('resize', (_ev)=>{ resizeCanvas(); }, true);
				
				window.addEventListener('wfAwakened', (_noargs)=>{
					// document.activeElement.blur();
					document.getElementById('foldfp').style.display = '';
					// document.getElementById('thoughts_textarea').focus();
					document.getElementById('awaken_scroll_target').scrollIntoView();
					// document.getElementById('thoughts_textarea').scrollIntoView();
				})
				
				window.addEventListener('wfLucidWake', (args)=>{
					if (args['memory']=="B" && window.location.href.includes("seeing-like-an-industry")) {
						// handle a specific special case
						// TODO: use more general system for handling this pls . . . 
						window.location.href = "https://www.droqever.com/-/blind-like-an-artist";
					} else {
						document.getElementById('foldfp').style.display = '';
						document.getElementById('awaken_scroll_target').scrollIntoView();
					}
				})

				let synth;

				window.addEventListener('songlink', (args)=>{
					// todo songlink
					let songlink_full = args['songlink'];
					let hash_index = songlink_full.indexOf('#');
					if (hash_index >= 0) {
						let songcode = songlink_full.substr(hash_index);
						if (synth != null) { synth.pause(); }
						synth = new beepbox.Synth(songcode);
						synth.play(); // might not be necessary?
						// if (synth.isPlayingSong) {}
					} else {
						console.log('invalid songlink');
					}
				})

				function resizeCanvas() {
					let pw = canvasParent.clientWidth;	
					let ph = canvasParent.clientHeight;
					if (gameWidth === undefined) {
						canvasEl.style.position = "absolute";
						canvasEl.style.left = `0px`;
						canvasEl.style.top = `0px`;
						canvasEl.width = pw;
						canvasEl.height = ph;
					} else {
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
				}
			</script>
			<div id="countdown"><span id="countdown_ts"></span></div>
			<?php 
			if (isset($gamesourcelink))  {
				echo "<div id='gamesourcelink'><a href='$gamesourcelink'>view game source</a> <span style='font-size:85%;'>(might be private)</span></div>";
			}
			?>
			<div id='gamecontrols'><?php
				
				if (isset($gamebeeplink))  {
					echo "<a href='$gamebeeplink' target='_blank'>play game music</a> <span style='font-size:85%;'>(opens in new tab)</span><br/>";
				}

				if (isset($gamecontrols) and $gamecontrols != null) {
					echo $gamecontrols;
				} else {
					echo 'keyboard: wsad/↑↓←→ ';
				}
			?></div>
			<div id='imdroqen'><a href='/droqen.php'>i'm droqen!</a></div>
		</div>
		<div id="foldfp" class="fullpage" style="display: none;">
			<div class="content">
				<form id="thoughts_form">
					hi! droqen here! you cleared the stage, what'd you think?<br/>
					sign your name if you want to be known. no accounts here yet :)<br/>
					<textarea id="thoughts_textarea"></textarea><br />
					<!-- sign your name please<br/><input type="text" name="name" value="" required><br/> -->
					<!-- and your email if you would like 1 notification when it's live ^^<br/><input type="email" name="email" pattern=".+@.+\..+"><br/> -->
					<!-- <i>~all messages will go live in a big batch when the game disappears~</i><br/> -->
					<input id="thoughts_submit_button" type="submit" value="OK, send!"><span id="submit_problem"></span><br/>
					<span style="font-size:85%">(note: may be made public later, dont post your address or anything)</span>
				</form>
				<div id="thoughts_submitted" style="display:none;">
					thanks for sharing your thoughts! they'll be going live when the game disappears.
					come back later to see everyone's comments!<br/>
					<br/>
					in the meantime, please feel free to share the link to this game anywhere, visit my <a href="https://droqen.itch.io/">itch io</a> page, or bug me on <a href="https://twitter.com/droqen">twitter</a>. (ugh, ok, "<a href="https://x.com/droqen">x</a>")<br/>
					<br/>
					-love, droqen
				</div>
				<div id="discord_link">
					never miss a droqever! i'm still figuring this out, but here are a couple solutions that i'm trying:
					<ul>
						<li>join a <a href="https://discord.gg/aNsm4PveGZ">very minimal discord server</a> with 2 channels and nobody in it yet</li>
						<li>or follow the <a href="https://droqever.com/rss.php">rss feed</a> i made just for rp0. like, does anyone else use this ancient technology?</li>
					</ul>
				</div>
				<!-- <div id="gametitle">
					Testing. Title div below the fold.
				</div>
				<p>Testing. Paragraph below the fold. Lorem ipsum and what not. Oh, that's not enough text. I'll just repeat myself. </p>
				<p>Testing. Paragraph below the fold. Lorem ipsum and what not. Oh, that's not enough text. I'll just repeat myself. Testing. Paragraph below the fold. Lorem ipsum and what not. Oh, that's not enough text. I'll just repeat myself. Testing. Paragraph below the fold. Lorem ipsum and what not. Oh, that's not enough text. I'll just repeat myself. Testing. Paragraph below the fold. Lorem ipsum and what not. Oh, that's not quite enough text. I'll just repeat myself one more time. Testing. Paragraph below the fold. Lorem ipsum and what not. OK, that's got to be enough text.</p> -->
			</div>
		</div>
		<!-- <div id="technical_difficulties" style="position:absolute; left:40%; top:40%; width:50%; font-size:40px; font-weight: bold; line-height: 40px;">
			Technical Difficulties,<br/>
			Please Stand By<br/>
			-droqen
		</div> -->
	</body>
	<script>
		function submitThoughts() { // TODO.
			let xmlhttp= window.XMLHttpRequest ?
				new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

			let submit_button = document.getElementById("thoughts_submit_button");
			submit_button.value = "Sending...";
			submit_button.disabled = true;

			xmlhttp.open("POST","/db/post-addcomment.php", true);
			xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xmlhttp.onreadystatechange = function() {
				if (this.readyState === 4 && this.status === 200) {
					if(this.responseText === undefined || this.responseText.length <= 1){
						document.getElementById("thoughts_form").remove();
						document.getElementById("thoughts_submitted").style.display = '';
					} else {
						window.setTimeout(function(){
							document.getElementById("submit_problem").innerText = this.responseText;
							submit_button.value = "OK, send!";
							submit_button.disabled = false;
						},500);
					}
				}
			}

			let params = "pname=<?php echo $gametitle; ?>&msg=" + document.getElementById('thoughts_textarea').value;
			xmlhttp.send(params);
		}
		document.getElementById("thoughts_form").addEventListener('submit', function(event) { 
			event.preventDefault(); 
			submitThoughts();
		});
	</script>
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

