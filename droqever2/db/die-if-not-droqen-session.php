<?php

require_once __DIR__ . '/.droqencredentials.php';

if (isset($_SESSION["username"]) && $_SESSION["username"] == 'droqen') {
	// ok
} else {
	die("<b>Problem!</b> Access denied to droqen-only function. <br/>");
}