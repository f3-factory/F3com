<?php
/*
F3::WIKI

The contents of f3 file are subject to the terms of the GNU General
Public License Version 3.0. You may not use f3 file except in
compliance with the license. Any of the license terms and conditions
can be waived if you get permission from the copyright holder.

Copyright (c) 2016 F3-Junkies
http://www.fatfreeframework.com

@version 0.9.0
*/

$f3 = require __DIR__.'/lib/base.php';

// set global vars
$f3->set('AUTOLOAD', 'inc/;app/');
$f3->set('DEBUG', 1);
$f3->set('TZ', 'Europe/Berlin');
$f3->set('CACHE', FALSE);
$f3->set('HIGHLIGHT', TRUE);

// set paths
$f3->set('UI', 'gui/');
$f3->set('LOCALES', 'dict/');
$f3->set('LANGUAGE','en-US,en-GB');
$f3->set('TEMP', 'temp/');
$f3->set('TMPL', 'tmpl/');


// set existing doc versions
$f3->set('DOCVERSIONS', array('3.6', '3.5'));

// extend Template Engine
\Template::instance()->extend('navigation', '\Navigation\View::renderTag');
\Template::instance()->extend('select', '\ViewHelper::renderSelect');
\Template::instance()->extend('markdown', '\ViewHelper::renderMarkdown');

// app vars
$f3->set('REPO', 'https://github.com/F3Community/F3com-data');
$f3->set('DOMAIN', 'fatfreeframework.com');

// default page
$f3->route('GET /', function($f3) { $f3->reroute('/home'); });
// search page
$f3->route('GET /search', '\Page\Controller->search');
// view page
$f3->route('GET /@page', '\Page\Controller->view');
$f3->route('GET /@version/@page', '\Page\Controller->view');
// get edit form
$f3->route(
	array(
		'GET /create',
		'GET /edit/@page',
		'GET /edit/@page/@marker'
	),
	'\Page\Controller->edit'
);
// save page
$f3->route(
	array(
		'POST /edit',
		'POST /edit/@page'
	),
	'\Page\Controller->save'
);
// delete page
$f3->route('GET /delete/@page', '\Page\Controller->delete');

// misc
$f3->route('GET /install', '\Common->installJIG');
$f3->set('ONERROR','\Common->error');

// kick start
$f3->run();
