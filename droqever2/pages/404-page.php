<!DOCTYPE html>
<html>
	<head>
		<title>droqever (v2 pending)</title>
		<style>
			body {
				transition: background-color 9s ease-in-out;
				background-color: #ffffff;
			}
		</style>
	</head>
	<body>
		<div style="position:absolute; left:50%; top:25%; width:400px; text-align:center; height: 2em; margin-left:-200px;">
			hello & welcome to droqever.com
			<br/>
			new games come and go but this is not a site for navigating
			<br/>
			im droqen. follow me in internet places (links to places)
			<br/>
			sometimes i'll post there.
		</div>
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
			document.body.style.backgroundColor = getRandomColor();
			setTimeout(() => {
				document.body.style.backgroundColor = getRandomColor();
				setInterval(() => {
					document.body.style.backgroundColor = getRandomColor();
				}, 10000);
			}, 1);
		</script>
	</body>
</html>