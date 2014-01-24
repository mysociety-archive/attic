<?php

session_set_cookie_params(SESSION_COOKIE_LIFETIME, SESSION_COOKIE_PATH, SESSION_COOKIE_DOMAIN);
session_start();

function session_read($id) {	
	
	if (!isset($_SESSION[$id]) || $_SESSION[$id] === false || $_SESSION[$id] === null) {
		return '';
	}
	else {
		return $_SESSION[$id];
	}
}

function session_write($id, $data) {
	$_SESSION[$id] = $data;
}

?>