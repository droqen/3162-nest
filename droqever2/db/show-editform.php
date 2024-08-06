<?php

require_once __DIR__ . '/die-if-not-droqen-session.php';
require_once __DIR__ . '/init-$conn.php';

$currgame_postid = 0;
$prevgame_postid = 0;
$postname = '';
$sourcelink = '';
$controls = '';
$nameinput_options = 'required';
$input_gamezip = "<input type='file' name='gamezip'></input><br/><br/>";
$hidden_line = '';
$submit_command_name = '???';

if (isset($_GET['show_add'])) {
	if ($_GET['show_add'] == 'new') {
		echo "<h1>adding game</h1>";
		$hidden_line = "<input type='hidden' name='insert_previd' value=0></input>";
		$submit_command_name = 'insert new!';
	} else {
		$prevgame_postid = $_GET['show_add'];
		$res = $conn->query("SELECT name FROM posts WHERE postid = $prevgame_postid");
		foreach ($res as $row) {
			$postname = $row[0];
			break;
		}
		if ($postname != '') {
			echo "<h1>adding variant of $postname</h1>";
			// $postname = $postname;
			$nameinput_options = "readonly style='opacity:0.5;pointer-events:none;'";
			$hidden_line = "<input type='hidden' name='insert_previd' value=$prevgame_postid></input>";
			$submit_command_name = 'insert variant!';
		} else {
			die("<b>Problem!</b> Trying to add with bad postid #$postid<br/>");
		}
	}
} else if (isset($_GET['show_edit'])) {
	$currgame_postid = $_GET['show_edit'];
	$res = $conn->query("SELECT name FROM posts WHERE postid = $currgame_postid");
	foreach ($res as $row) {
		$postname = $row[0];
		break;
	}
	$res = $conn->query("SELECT zipname, sourcelink, controls FROM games_43beta3 WHERE postid = $currgame_postid");
	foreach ($res as $row) {
		$zipname = $row[0];
		$sourcelink = $row[1];
		$controls = $row[2];
		break;
	}
	if ($zipname != '') {
		echo "<h1>editing $postname</h1>";
		$nameinput_options = "readonly style='opacity:0.5;pointer-events:none;'";
		// $input_gamezip = '';
		$hidden_line = "<input type='hidden' name='editid' value=$currgame_postid></input>";
		$submit_command_name = 'update!';
	} else {
		die("<b>Problem!</b> Trying to edit bad postid #$postid<br/>");
	}
}

echo "
<form method='POST' action='?submit' enctype='multipart/form-data'>
	<i>godot v4.3 (rc1, but hopefully remains compatible w future versions)</i><br/>
	<br/>
	Post name<br/>
	<input type='text' name='postname' value='$postname' $nameinput_options></input><br/><br/>
	Controls (optional. if unset, defaults to ''keyboard: wsad/↑↓←→'')<br/>
	<input type='text' name='controls' value='$controls'></input><br/><br/>
	Full source code link (REQUIRED.)<br/>
	<input type='text' name='sourcelink' value='$sourcelink' required></input><br/><br/>
	$input_gamezip
	$hidden_line
	<input type='submit' value='$submit_command_name'></input>
</form>";