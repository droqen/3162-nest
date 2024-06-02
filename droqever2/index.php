<?php
if (isset($_GET['url'])) {
	require_once __DIR__ . '/db/init-$conn.php';
	$searchname = str_replace("-/","",$_GET['url']);
	$res = $conn->query("SELECT postid, posttype FROM posts WHERE name = '$searchname' ORDER BY postdate DESC, postid DESC");
	foreach ($res as $row) {
		$postid = $row[0];
		$posttype = $row[1];
		break;
	}
	if (isset($postid)) {
		if ($posttype == 1) {
			$res = $conn->query("SELECT zipname FROM games_43customadam WHERE postid = $postid");
			foreach ($res as $row) {
				$gamezipname = $row[0];
				$gamezippath = "/games_43custom/$gamezipname.zip";
				if (file_exists(__DIR__ . $gamezippath)) {
					$gamezipsize = filesize(__DIR__ . $gamezippath);
				} else {
					die("Missing file for posttype $posttype zipname $zipname");
				}
				break;
			}
		} else {
			die("Unknown posttype $posttype for #$postid");
		}
	}
}

if (isset($gamezipsize)) {
	require_once __DIR__ . '/pages/game-page-43custom.php';
} else {
	require_once __DIR__ . '/pages/404-page.php';
}