<?php
/*
This file is part of F3::WIKI

The contents of this file are subject to the terms of the GNU General
Public License Version 3.0. You may not use this file except in
compliance with the license. Any of the license terms and conditions
can be waived if you get permission from the copyright holder.
*/

namespace Page;

class Model extends \DB\Jig\Mapper {

	/**
	 * sync with table
	 */
	public function __construct()
	{
		parent::__construct(\Base::instance()->get('DB'), 'pages.json');
	}

	public function loadExistingPage($slug)
	{
		//$this->load(array('@slug = ? AND (isset(@deleted) && @deleted = ?)', $slug, 0));
		$this->load(array('@slug = ?', $slug));
	}

}
