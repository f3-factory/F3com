<?php
/*
This file is part of F3::WIKI

The contents of this file are subject to the terms of the GNU General
Public License Version 3.0. You may not use this file except in
compliance with the license. Any of the license terms and conditions
can be waived if you get permission from the copyright holder.
*/

namespace Navigation;

class Model extends \DB\Jig\Mapper
{
	/** @var \Base */
	protected $f3;

	public function __construct()
	{
		$this->f3 = \Base::instance();
		parent::__construct($this->f3->get('DB'),'navigation.json');
	}

	/**
	 * return a set of pages that belongs to a given menu
	 * @param $menu
	 * @return array
	 */
	public function getPages($menu)
	{
		$this->load(array('@name = ?', $menu));
		if(!$this->dry()) {
			$page = new \Page\Model();
			$pages = array();
			foreach ($this->pages as $item) {
				// $page->load(array('@_id = ? AND ( isset(@deleted) && @deleted = 0)', $item));
				$page->load(array('@_id = ?', $item));
				if (!$page->dry())
					$pages[] = array('title' => $page->title, 'slug' => $page->slug);
				$page->reset();
			}
		} else {
			trigger_error('menu not found');
		}
		$this->reset();
		return $pages;
	}
}
