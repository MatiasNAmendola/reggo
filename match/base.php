<?php

namespace Utv\Reggo\Match;

abstract class Base {

	protected $length = NULL;
	
	public function __construct($min = NULL, $max = NULL)
	{
		$this->_set_length($min, $max);
	}
	
	protected function _set_length($min = NULL, $max = NULL)
	{
		if(is_null($max) && !is_null($min))
		{
			$this->length = array($min);
		}
		else if(!is_null($min))
		{
			$this->length = array($min, $max);
		}
	}
	
	protected function _length()
	{
		if(is_null($this->length))
		{
			return '';
		}
		
		// If length is a string, then this should be used
		if(is_string($this->length[0]))
		{
			return $this->length[0];
		}
		
		// If length is only 1, then nothing should be returned
		if($this->length[0] === 1 && !isset($this->length[1]))
		{
			return '';
		}
		
		return '{'.implode(',', $this->length).'}';
	}
}
