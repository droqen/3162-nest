<?php
	require_once __DIR__ . '/init-$conn.php';

	if (!isset($_POST['pname'])) { die("Missing param 9543"); }
	if (!isset($_POST['msg'])) { die("Missing param 0356"); }

	$postname = $_POST['pname'];
	$reply_msg = $_POST['msg'];

	$stmt = $conn->prepare("SELECT postid FROM posts WHERE name=? AND NOW() < postexpires ORDER BY posts.postid DESC LIMIT 1");
	$stmt->execute([$postname]);
	$row = $stmt->fetch();
	if ($row) {
		$postid = $row[0];
		$stmt = $conn->prepare("INSERT INTO replyposts (parent_postid, replydatetime, replymsg) VALUES (?, NOW(), ?)");
		$insert_count = $stmt->execute([$postid, $reply_msg]);
		if ($insert_count > 0) {
			die(0); // success!
		} else {
			die("Problem submitting! Not sure what the problem is. Report to droqen! $postid $reply_msg");
		}
	} else {
		die("Problem submitting! Maybe bad postname '$postname' -- or maybe the game is already gone?");
	}
