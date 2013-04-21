<?php

namespace Utv\Reggo\Match;

require_once('reggo_match_base.php');

class AnyOf extends Base {
	private $inverted = false;
	private $contents;
	
	public function __construct($contents, $min = NULL, $max = NULL, $inverted = false)
	{
		parent::__construct($min, $max);
		
		$this->contents = $contents;
		$this->inverted = $inverted;
	}
	
	public function invert()
	{
		$this->inverted = true;
	}
	
	public function compile()
	{
		$regexp = '[';
	
		// If it should be inverted
		if($this->inverted)
		{
			$regexp .= '^';
		}
		
		// Remove the [] if length is 1
		switch(strlen($this->contents))
		{
			case 2:
				if(substr($this->contents, 0, 1) !== '\\')
				{
					break;
				}
		
			case 1:
				return $this->contents.$this->_length();
		}
		
		$regexp .= $this->contents;
		
		return $regexp.']'.$this->_length();
	}
}
