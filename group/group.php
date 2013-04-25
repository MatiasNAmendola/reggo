<?php

namespace Utv\Reggo;

require_once('match/exact.php');
require_once('match/any.php');
require_once('match/any_of.php');

class Group {
	
	public $name;
	public $contents = array();
	
	public static $replacements = array(
		'alpha' 	=> 'a-zA-ZåäöÅÄÖ',
		'alphaeng'	=> 'a-zA-ZåäöÅÄÖ',
		'lower'		=> 'a-zåäö',
		'upper' 	=> 'A-ZÅÄÖ',
		'num' 		=> '0-9',
		'number' 	=> '0-9',
		'dash' 		=> '\-\_',
		'dot' 		=> '\.',
		'period' 	=> '\.'
	);
	
	public function __construct($name, $callable = NULL)
	{
		$this->name = $name;
		
		if(is_callable($callable))
		{
			// Call this callable
			call_user_func_array($callable, array(&$this));
		}
	}
	
	public function __call($name, $attributes)
	{
	
		$min = array_shift($attributes);
		$max = array_shift($attributes);
	
		// Check if any of the multiple [...] matches
		if(strpos($name, '_') !== FALSE || array_key_exists($name, self::$replacements))
		{
			$char_types = split('_', $name);
			$invert = false;
			
			// Check if first sub-name is not, then invert matching
			if($char_types[0] === 'not')
			{
				// Remove the first parameter
				array_shift($char_types);
				$invert = true;
			}
			
			// Capture all characters that should be in content
			$contents = self::_capture_characters($char_types);
			
			$match = new Match\AnyOf($contents, $min, $max, $invert);
			
			$this->contents[] = $match;
		}
		
		return $this;
	}
	
	private static function _capture_characters($char_types)
	{
		// To use in function below
		$replacements = &self::$replacements;
		
		$contents = array_reduce($char_types, function($result, $val) use ($replacements)
		{
			// Check that key exists
			if(array_key_exists($val, self::$replacements))
			{
				return $result . $replacements[$val];
			}
		
			return $result;
		});
		
		return $contents;
	}
	
	public function group($name, $callable = NULL)
	{
		$group = new Group($name, $callable);
		
		$this->contents[] = $group;
		
		return $group;
	}
	
	public function any($contents_arr, $min = NULL, $max = NULL)
	{
		$this->contents[] = new Match\Any($contents_arr, $min, $max);
		return $this;
	}
	
	public function exact($string_or_callable)
	{
		if(is_string($string_or_callable))
		{
			$this->contents[] = new Match\Exact($string_or_callable);
		}
		else if(is_callable($string_or_callable))
		{
			call_user_func_array($string_or_callable, array(&$this));
		}
		
		return $this;
	}
	
	public function escape($string)
	{
		$escaped = preg_replace('/([^a-zA-Z0-9åäöÅÄÖ])/', '\\\\$0', $string);
		return $this->exact($escaped);
	}
	
	/**
	 * Returns an array with all subgroups
	 * in the right order
	 */
	public function groups()
	{
		// Add this
		$groups = array($this);
		
		// Add all subgroups
		foreach($this->contents as $content)
		{
			if($content instanceof Group)
			{
				$groups = array_merge($groups, $content->groups());
			}
		}
		
		return $groups;
	}
	
	public function compile()
	{
		$contents = array_reduce($this->contents, function($result, $val)
		{
			return $result . $val->compile();
		}, '');
		
		return '('.$contents.')';
	}
	
	/**
	 * Prints debug text to verify why regexp looks the way
	 * it does
	 */
	public function debug($depth)
	{
		$items = array(array($depth, $this));
	
		foreach($this->contents as $part)
		{
			if($part instanceof Group)
			{
				$items = array_merge($items, $part->debug($depth + 1));
			}
			else
			{
				$items[] = array($depth + 1, $part);
			}
		}
		
		return $items;
	}
}
