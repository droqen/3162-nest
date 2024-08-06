<?php

$show_gamepost = false;
$show_replies = false;

if (isset($_GET['url'])) {
	require_once __DIR__ . '/db/init-$conn.php';
	$searchname = str_replace("-/","",$_GET['url']);
	$gametitle = $searchname; // change this later
	$res = $conn->query("SELECT
			postid,
			posttype,
			NOW(),
			postexpires,
			IF(postexpires = null or NOW()<postexpires,'True','False') 
		FROM posts
		WHERE
			name = '$searchname'
			-- AND (postexpires = null OR NOW() < postexpires)
			ORDER BY postdate DESC, postid DESC");
	foreach ($res as $row) {
		$postid = $row[0];
		$posttype = $row[1];
		// echo($row[2] . "<br/>");
		// echo($row[3] . "<br/>");
		$time_current = strtotime($row[2]);
		$time_expires = strtotime($row[3]);
		// do i need to use $row[4]?
		// echo("current " . $time_current . "<br/>");
		// echo("expires " . $time_expires . "<br/>");
		$lifetime_remaining = max(0, $time_expires - $time_current);
		if ($lifetime_remaining > 0) { $show_gamepost = true; }
		else { $show_replies = true; }
		break;
	}
	if (isset($postid)) {
		if ($posttype == 3) {
			$res = $conn->query("SELECT zipname, sourcelink, controls FROM games_43 WHERE postid = $postid");
			foreach ($res as $row) {
				$gamezipname = $row[0];
				$gamesourcelink = $row[1];
				$gamecontrols = $row[2];
				$gamezippath = "/games_43/$gamezipname.zip";
				if (file_exists(__DIR__ . $gamezippath)) {
					$gamezipsize = filesize(__DIR__ . $gamezippath);
				} else {
					die("Missing file at $gamezippath for posttype $posttype zipname $gamezipname");
				}
				break;
			}
		} else if ($posttype == 2) {
				$res = $conn->query("SELECT zipname, sourcelink, controls FROM games_43beta3 WHERE postid = $postid");
				foreach ($res as $row) {
					$gamezipname = $row[0];
					$gamesourcelink = $row[1];
					$gamecontrols = $row[2];
					$gamezippath = "/games_43beta3/$gamezipname.zip";
					if (file_exists(__DIR__ . $gamezippath)) {
						$gamezipsize = filesize(__DIR__ . $gamezippath);
					} else {
						die("Missing file at $gamezippath for posttype $posttype zipname $gamezipname");
					}
					break;
				}
		} else if ($posttype == 1) {
			$res = $conn->query("SELECT zipname, sourcelink, controls FROM games_43customadam WHERE postid = $postid");
			foreach ($res as $row) {
				$gamezipname = $row[0];
				$gamesourcelink = $row[1];
				$gamecontrols = $row[2];
				$gamezippath = "/games_43custom/$gamezipname.zip";
				if (file_exists(__DIR__ . $gamezippath)) {
					$gamezipsize = filesize(__DIR__ . $gamezippath);
				} else {
					die("Missing file at $gamezippath for posttype $posttype zipname $gamezipname");
				}
				break;
			}
		} else {
			die("Unknown posttype $posttype for #$postid (known posttypes are [1,2])");
		}
	}
}

if ($show_gamepost && isset($gamezipsize)) {
	require_once __DIR__ . '/pages/game-page-43custom.php';
} else if ($show_replies && isset($postid)) {
	require_once __DIR__ . '/pages/replies-page.php';
} else {
	require_once __DIR__ . '/pages/404-page.html';
}