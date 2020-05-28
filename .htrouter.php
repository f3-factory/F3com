<?php

// public directory definition
$public_dir=__DIR__;

// serve existing files as-is
$uri = parse_url($_SERVER['REQUEST_URI']);
if (file_exists($public_dir.$uri['path']))
	return FALSE;
	
$is_php_webserver = true;

// patch SCRIPT_NAME and pass the request to index.php
$_SERVER['SCRIPT_NAME']='index.php';
require($public_dir.'/index.php');