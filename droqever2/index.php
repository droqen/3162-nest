<?php

$access_granted = false;

if (isset($_GET['url'])) {
	require_once __DIR__ . '/db/init-$conn.php';
	$searchname = str_replace("-/","",$_GET['url']);
	$res = $conn->query("SELECT postid, posttype, NOW(), postexpires FROM posts WHERE name = '$searchname' AND (postexpires = null OR NOW() < postexpires) ORDER BY postdate DESC, postid DESC");
	foreach ($res as $row) {
		$postid = $row[0];
		$posttype = $row[1];
		// echo($row[2] . "<br/>");
		// echo($row[3] . "<br/>");
		$time_current = strtotime($row[2]);
		$time_expires = strtotime($row[3]);
		// echo("current " . $time_current . "<br/>");
		// echo("expires " . $time_expires . "<br/>");
		$lifetime_remaining = max(0, $time_expires - $time_current);
		if ($lifetime_remaining > 0) { $access_granted = true; }
		break;
	}
	if (isset($postid)) {
		if ($posttype == 1) {
			$res = $conn->query("SELECT zipname, sourcelink FROM games_43customadam WHERE postid = $postid");
			foreach ($res as $row) {
				$gamezipname = $row[0];
				$gamesourcelink = $row[1];
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

if ($access_granted && isset($gamezipsize)) {
	require_once __DIR__ . '/pages/game-page-43custom.php';
} else {
	require_once __DIR__ . '/pages/404-page.php';
}