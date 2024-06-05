<?php

require_once __DIR__ . '/db/.droqencredentials.php';

if (isset($_SESSION['username']) && $_SESSION['username'] == 'droqen') {
	require_once __DIR__ . '/pages/admin-page.php';
} else {
	require_once __DIR__ . '/pages/login-page.html';
	echo $_GET['droqen'];
	echo '<br/>';
	echo $_GET['droqen_proof'];
	echo '<br/>';
	// && $_GET['droqen_proof']=="i%27m+droqen%21"
}