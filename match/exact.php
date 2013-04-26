<?php

namespace Utv\Reggo\Match;

require_once('match/base.php');

class Exact extends Base {
	private $contents = '';
	
	public function __construct($contents)
	{
		if(is_string($contents))
		{
			$this->contents = $contents;
		}
		else if(is_callable($string_or_callable))
		{
			$this->contents = call_user_func($contents);
		}
	}
	
	public function compile()
	{	
		return $this->contents;
	}
}
