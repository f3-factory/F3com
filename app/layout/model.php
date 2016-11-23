<?php
/*
This file is part of F3::WIKI

The contents of this file are subject to the terms of the GNU General
Public License Version 3.0. You may not use this file except in
compliance with the license. Any of the license terms and conditions
can be waived if you get permission from the copyright holder.
*/

namespace Layout;

class Model extends \DB\Jig\Mapper 
{
	/** @var \Base */
	protected $f3;

	public function __construct()
	{
		$this->f3 = \Base::instance();
		parent::__construct($this->f3->get('DB'), 'layout.json');
	}
}
