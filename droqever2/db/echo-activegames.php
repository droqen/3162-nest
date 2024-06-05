<?php

require_once __DIR__ . '/die-if-not-droqen-session.php';
require_once __DIR__ . '/init-$conn.php';

$res = $conn->query("SELECT posts.postid, posts.name
	FROM posts, games_43customadam 
	WHERE (posttype = 1 AND posts.postid = games_43customadam.postid AND (postexpires = null OR NOW() < postexpires))
	ORDER BY posts.postid DESC");

echo "<h1>active games</h1>";

echo "[<a href='?show_add=new'>add new game</a>]<br/><br/>";

foreach ($res as $row) {
	$postid = $row[0];
	$postname = $row[1];
	echo "<a href='/-/$postname'>$postname</a> (#$postid) [<a href='?show_edit=$postid'>edit</a>] [<a href='?show_add=$postid'>add new zip</a>]<br/>";
}

// SELECT WorkOrder.id, WorkOrder.customer_id, MAX(WorkOrder.create_date) FROM `WorkOrder` GROUP BY `WorkOrder`.`customer_id` ORDER BY `WorkOrder`.`create_date` DESC

// if (isset($_GET['url'])) {
// 	require_once __DIR__ . '/db/init-$conn.php';
// 	$searchname = str_replace("-/","",$_GET['url']);
// 	$gametitle = $searchname; // change this later
// 	$res = $conn->query("SELECT postid, posttype, NOW(), postexpires FROM posts WHERE name = '$searchname' AND (postexpires = null OR NOW() < postexpires) ORDER BY postdate DESC, postid DESC");
// 	foreach ($res as $row) {
// 		$postid = $row[0];
// 		$posttype = $row[1];
// 		// echo($row[2] . "<br/>");
// 		// echo($row[3] . "<br/>");
// 		$time_current = strtotime($row[2]);
// 		$time_expires = strtotime($row[3]);
// 		// echo("current " . $time_current . "<br/>");
// 		// echo("expires " . $time_expires . "<br/>");
// 		$lifetime_remaining = max(0, $time_expires - $time_current);
// 		if ($lifetime_remaining > 0) { $access_granted = true; }
// 		break;
// 	}
// 	if (isset($postid)) {
// 		if ($posttype == 1) {
// 			$res = $conn->query("SELECT zipname, sourcelink FROM games_43customadam WHERE postid = $postid");
// 			foreach ($res as $row) {
// 				$gamezipname = $row[0];
// 				$gamesourcelink = $row[1];
// 				$gamezippath = "/games_43custom/$gamezipname.zip";
// 				if (file_exists(__DIR__ . $gamezippath)) {
// 					$gamezipsize = filesize(__DIR__ . $gamezippath);
// 				} else {
// 					die("Missing file at $gamezippath for posttype $posttype zipname $gamezipname");
// 				}
// 				break;
// 			}
// 		} else {
// 			die("Unknown posttype $posttype for #$postid");
// 		}
// 	}