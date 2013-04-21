<?php

namespace Utv\Reggo\Match;

require_once('reggo_match_base.php');

class Exact extends Base {
	private $contents = '';
	
	public function __construct($contents)
	{
		$this->contents = $contents;
	}
	
	public function compile()
	{	
		return $this->contents;
	}
}
