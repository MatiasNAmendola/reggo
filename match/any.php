<?php

namespace Utv\Reggo\Match;

require_once('match/base.php');

class Any extends Base {
	private $items = array();
	
	public function __construct($items)
	{
		$this->items = $items;
	}
	
	public function compile()
	{
		return implode('|', $this->items);
	}
}
