<?php

require_once __DIR__ . '/die-if-not-droqen-session.php';
require_once __DIR__ . '/init-$conn.php';

echo "POST:<br/>";
print_r($_POST);
echo "<br/><br/>";
echo "FILES:<br/>";
print_r($_FILES);
echo "<br/><br/>";

if (isset($_POST['insert_previd'])) {

	// GET PREVID READY...
	$previd = $_POST['insert_previd'];
	if ($previd == 0) $previd = null;

	// GET DATES READY...
	$date = new DateTime("now");
	$postdate = $date->format('Y-m-d');
	$days = rand(6,7);
	$hrs = rand(1,24);
	$date->modify("+$days day");
	$date->modify("+$hrs hour");
	$postexpires = $date->format('Y-m-d H:i:s');
	
	// GET GAME UPLOAD READY...
	$base_filename = strtolower(pathinfo( basename($_FILES["gamezip"]["name"]) ,PATHINFO_FILENAME));
	$base_filetype = strtolower(pathinfo( basename($_FILES["gamezip"]["name"]) ,PATHINFO_EXTENSION));
	$base_filesize = $_FILES["gamezip"]["size"];
	if ($base_filetype != "zip") { die("<b>Problem!</b> Uploaded file is invalid (only ZIP files are allowed)"); }
	if ($base_filesize > 5000000) { die("<b>Problem!</b> Uploaded file is way too large (must be <= 5MB)"); }

	$stmt = $conn->prepare("INSERT INTO posts (posttype, previd, name, postdate, postexpires) VALUES (1, ?, ?, ?, ?)");
	$insert_count = $stmt->execute([$previd, $_POST['postname'], $postdate, $postexpires]);

	$id = 0;

	if ($insert_count > 0) {
		$id = $conn->lastInsertId();
		if ($id > 0) {
			echo("<br/>Inserted id #$id (1/3)");
		} else { die("<b>Problem!</b> Failed inserting id, got return result #$id"); }
	} else { die("<b>Problem!</b> Failed inserting, did not insert any rows"); }

	$target_filedir = $_SERVER['DOCUMENT_ROOT'] . "/games_43custom";
	$target_filename = $base_filename . "_$id.$base_filetype";
	$target_filepath = "$target_filedir/$target_filename";

	if (move_uploaded_file($_FILES["gamezip"]["tmp_name"], $target_filepath)) {
		echo("<br/>Uploaded file $target_filename (2/3)");
	} else { die("<b>Problem!</b> Failed uploading file."); }

	$zipname = $_FILES["gamezip"]["name"];

	if (isset($_POST['sourcelink'])) {
		$sourcelink = $_POST['sourcelink'];
	} else {
		$sourcelink = 'x';
	}

	$stmt = $conn->prepare("INSERT INTO games_43customadam (postid, zipname, zipsize, sourcelink) VALUES (?, ?, ?, ?)");
	$stmt->execute([$id, $base_filename . "_$id", $base_filesize, $sourcelink]);

	echo("<br/>Inserted entry into games_43customadam (3/3) - No confirmation tho, I guess you should double check?");
}

if (isset($_POST['editid'])) {
	// editing?
	if (isset($_FILES["gamezip"])) {
		echo("<br/>OK, trying to upload new gamezip...");
		$editid = $_POST['editid'];
		$zipname = '';
		$res = $conn->query("SELECT zipname FROM games_43customadam WHERE postid = $editid LIMIT 1");
		foreach ($res as $row) {
			$zipname = $row[0];
			break;
		}
		if ($zipname != '') {
			$target_filedir = $_SERVER['DOCUMENT_ROOT'] . "/games_43custom";
			$target_filename = "$zipname.zip";
			$target_filepath = "$target_filedir/$target_filename";
			if (move_uploaded_file($_FILES["gamezip"]["tmp_name"], $target_filepath)) {
				echo("<br/>Looks OK! (replaced file!)");
			} else { die("<b>Problem!</b> Failed uploading file."); }
			$filesize = $_FILES["gamezip"]["size"];
			$conn->query("UPDATE games_43customadam SET zipsize = $filesize WHERE postid = $editid");
		} else { die("<b>Problem!</b> No zipname for editid #$editid"); }
	}
	if (isset($_POST['sourcelink'])) {
		$sourcelink = $_POST['sourcelink'];
		$stmt = $conn->prepare("UPDATE games_43customadam SET sourcelink = ? WHERE postid = $editid");
		$stmt->execute([$sourcelink]);
		echo("<br/>OK! Um, updated the sourcelink to `$sourcelink`! I guess!");
	}

	echo("<br/>...<br/>Done making the above edits to #$editid! If you didn't see anything, that means nothing happened!");

}

echo "<br/><br/>
<a href='?'>Back to game list.</a>
";


// $base_filename = strtolower(pathinfo( basename($_FILES["fileToUpload"]["name"]) ,PATHINFO_FILENAME));
// 	$base_filetype = strtolower(pathinfo( basename($_FILES["fileToUpload"]["name"]) ,PATHINFO_EXTENSION));
// 	$uploadOk = 0;

// 	$year = "0000";
// 	$month = "00";
// 	$day = "00";
// 	echo "OK, attempting to add game...";
// 	echo "<br/>gameTitle = " . $_POST["gameTitle"];
// 	echo "<br/>gameDescription = " . $_POST["gameDescription"];
// 	echo "<br/>releaseDate = " . $_POST["releaseDate"];
// 	$explodedDate = explode("-", $_POST["releaseDate"]);
// 	if (count($explodedDate)==3) {
// 		$year = $explodedDate[0];
// 		$month = $explodedDate[1];
// 		$day = $explodedDate[2];
// 		echo "<br/> - yyyy = $year";
// 		echo "<br/> - mm = $month";
// 		echo "<br/> - dd = $day";
// 		$uploadOk = 1; // ok!
// 	} else {
// 		echo "<br/><b>Problem!</b> Release date not properly formatted (it is not hyphen-delimited into three parts)";
// 		echo "<br/>-- explodedDate = $explodedDate";
// 		echo "<br/>-- count(explodedDate) = " . count($explodedDate);
// 		$uploadOk = 0;
// 	}
// 	echo "<br/>fileToUpload = $base_filename.$base_filetype";
// 	if ($base_filetype != "zip") {
// 		echo "<br/><b>Problem!</b> Uploaded file is invalid (only ZIP files are allowed)";
// 		$uploadOk = 0;
// 	}
// 	if ($_FILES["fileToUpload"]["size"] > 500000) {
// 		echo "<br/><b>Problem!</b> Uploaded file is too large (must be <= 500KB)";
// 		$uploadOk = 0;
// 	}