<?php
	require_once __DIR__ . '/init-$conn.php';

	if (!isset($_POST['from_postid'])) { die("Missing param 7203"); }
	if (!isset($_POST['exitname'])) { die("Missing param 3817"); }

	$from_postid = $_POST['from_postid'];
	$exitname = $_POST['exitname'];

	$stmt = $conn->prepare("SELECT to_postid, entername FROM pathways WHERE from_postid=? AND exitname=? LIMIT 1");
	$stmt->execute([$from_postid, $exitname]);
	$row = $stmt->fetch();
	if ($row) {
		$to_postid = $row[0];
		$entername = $row[1];
		if (true) { // check posts to get games table name
			$stmt = $conn->prepare("SELECT postid, zipname, zipsize FROM games_43 WHERE postid=? LIMIT 1");
			$stmt->execute([$to_postid]);
			$row = $stmt->fetch();
			if ($row) {
				$postid = $row[0];
				$gamezipname = $row[1];
				$gamezipsize = $row[2];
				$gamezippath = "/games_43/$gamezipname.zip";

				die("OK!;$postid;$gamezippath;$gamezipsize");
			} else {
				die("Problem! to_postid value $to_postid not found on table games_43");
			}
		} else {
			// pass
		}
	} else {
		die("Problem! from_postid . exitname pair '$from_postid . $exitname' not found on table 'pathways'");
	}
