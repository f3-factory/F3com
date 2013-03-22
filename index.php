<?php
/**
	F3::WIKI

	The contents of f3 file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use f3 file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

		Copyright (c) 2013 F3-Junkies
		http://www.fatfreeframework.com

		@version 0.3.0
 **/

$f3 = require __DIR__.'/lib/base.php';

// set global vars
$f3->set('AUTOLOAD', 'inc/;app/');
$f3->set('DEBUG', 1);
$f3->set('TZ', 'Europe/Berlin');
$f3->set('CACHE', FALSE);

// set paths
$f3->set('UI', 'gui/');
$f3->set('LOCALES', 'dict/');
$f3->set('TEMP', 'temp/');
$f3->set('TMPL', 'tmpl/');
// markdown content data
$f3->set('MDCONTENT', 'content/');

// extend Template Engine
\Template::instance()->extend('navigation', '\Navigation\View::renderTag');
\Template::instance()->extend('select', '\ViewHelper::renderSelect');
\Template::instance()->extend('markdown', '\ViewHelper::renderMarkdown');

// init DB
$f3->set('DB',new DB\Jig('db/'));

// app vars

$f3->set('REPO', 'https://github.com/ikkez/F3com');
$f3->set('DOMAIN', 'vircuit.net');
 
// ROUTING

// default page
$f3->route('GET /', function($f3) { $f3->reroute('/home'); });

// view page
$f3->route('GET /@page', '\Page\Controller->view');
// get edit form
$f3->route('GET /create', '\Page\Controller->edit');
$f3->route('GET /edit/@page', '\Page\Controller->edit');
$f3->route('GET /edit/@page/@marker', '\Page\Controller->edit');
// save page
$f3->route('POST /edit', '\Page\Controller->save');
$f3->route('POST /edit/@page', '\Page\Controller->save');
// delete page
$f3->route('GET /delete/@page', '\Page\Controller->delete');

// misc
$f3->route('GET /install', '\Common->installJIG');
$f3->set('ONERROR','\Common->error');


// kick start
$f3->run();
