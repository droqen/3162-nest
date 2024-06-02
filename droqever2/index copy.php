<!DOCTYPE html>
<html>
	<head>
		<title>droqever (v2)</title>
	</head>
	<body>
		<?php
			echo 'My dir: '.__DIR__;
			if (isset($_GET['url'])) {
				echo("Yes, you're in folder structure website/".$_GET['url']);
				require_once __DIR__ . '/db/init-$conn.php';
			} else {
				echo("Root");
				require_once __DIR__ . '/db/init-$conn.php';
			}
			echo '<br/>';
		?>
		game goes here . . . NOTHING FOUND THO . . .
	</body>
</html>