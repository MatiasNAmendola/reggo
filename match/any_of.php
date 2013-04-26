<?php

namespace Utv\Reggo\Match;

require_once('match/base.php');

class AnyOf extends Base {
	private $inverted = false;
	private $contents;
	
	public static $replacements = array(
		'alpha' 	=> 'a-zA-ZåäöÅÄÖ',
		'alphaeng'	=> 'a-zA-Z',
		'lower'		=> 'a-zåäö',
		'upper' 	=> 'A-ZÅÄÖ',
		'num' 		=> '0-9',
		'number' 	=> '0-9',
		'dash' 		=> '\-\_',
		'dot' 		=> '\.',
		'period' 	=> '\.',
		'space'		=> '\s'
	);
	
	public static $numbers = array(
		'once' 			=> 1,
		'maby' 			=> '?',
		'zero_or_one' 	=> '?',
		'zero_or_more'	=> '*',
		'one_or_more' 	=> '+',
	);
	
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
	
	public function __call($name, $arguments)
	{
		// If length
		if(array_key_exists($name, self::$numbers))
		{
			$this->_set_length(self::$numbers[$name]);
		}
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
