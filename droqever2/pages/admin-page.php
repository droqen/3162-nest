<?php
if (isset($_SESSION['username']) && $_SESSION['username'] == 'droqen') {
	// ok
} else {
	require_once(__DIR__ . '/404-page.html');
	die();
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
		<link rel="stylesheet" href="/scripts/corestyle.css">
		<title>active posts - droqever.com</title>
	</head>
	<body>

<?php
	if (isset($_POST['postname'])) {
		require_once __DIR__ . '/../db/post-editform.php';
	} else if (isset($_GET['show_add']) || isset($_GET['show_edit'])) {
		require_once __DIR__ . '/../db/show-editform.php';
	} else {
		require_once __DIR__ . '/../db/echo-activegames.php';
	}
?>

	</body>
	<script src="/scripts/cycling_pastel_bg.js"></script>
	<!-- <script>
		const tx = document.getElementsByTagName("textarea");
		for (let i = 0; i < tx.length; i++) {
			tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
			tx[i].addEventListener("input", OnInput, false);
		}
		function OnInput() {
			this.style.height = 'auto';
			this.style.height = (this.scrollHeight) + "px";
		}
	</script> -->
</html>