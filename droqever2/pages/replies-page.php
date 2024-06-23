<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
		<link rel="stylesheet" href="/scripts/corestyle.css">
		<title><?php echo $gametitle ?> replies</title>
	</head>
	<body>
		<div id="foldfp" class="fullpage">
			<div class="content">
				<div class='thanks'>
						hi! droqen here. <strong><?php echo $gametitle ?></strong> is now offline. you missed it!<br/>
						follow me on <a href='https://droqen.itch.io/'>itch io</a>
						to catch my proper finished games when i do release them!
						i am also working on a way to browse currently online droqever games,
						but it is not available yet. i'll announce it on <a href='https://x.com/droqen/'>twitter</a> when it's ready.
				</div>
				<?php
					require_once __DIR__ . '/../db/init-$conn.php';

					$res = $conn->query("SELECT replymsg
						FROM replyposts
						WHERE parent_postid = $postid
						ORDER BY id");

					if ($res->rowCount() > 0) {
						echo "
				<div class='thanks'>
						here are all of everyone's thoughts from when <b>$gametitle</b> <em>was</em> online, in order of their posting.<br/>
						thank you all for playing and sharing <3
				</div>
						";
						foreach ($res as $row) {
							$replymsg = $row[0];
							echo "
							<div class='reply'><pre>$replymsg</pre></div>";
						}
					}
				?>
			</div>
		</div>
	</body>
	<script src="/scripts/cycling_pastel_bg.js"></script>
</html>

