<?php

namespace Utv;

require_once('group/group.php');

class Reggo {
	
	private static $default_group = 'all';
	private $main_group;
	private $flags = '';
	
	// Flags
	const FLAG_GLOBAL = 'g';
	const FLAG_IGNORE_CASE = 'i';
	
	public function __construct($group_name, $callable = NULL)
	{
	
		// Check if using default group;
		if(is_null($callable)) {
			$callable = $group_name;
			$group_name = self::$default_group;
		}
		
		$this->main_group = new Reggo\Group($group_name, $callable);
	}
	
	/**
	 * Set the flags for this regexp
	 */
	public function flags($str)
	{
		if(is_string($str))
		{
			$this->flags = $str;
		}
	}
	
	/**
	 * List of all groups, in the right order
	 */
	public function groups()
	{
		return $this->main_group->groups();
	}
	
	/**
	 * Match against a string
	 */
	public function match($str)
	{
		$groups = $this->groups();
		$group_keys = array_map(function($group)
		{
			return $group->name;
		}, $groups);
		
		// Match
		preg_match_all($this->compile(), $str, $matches, PREG_SET_ORDER);
		
		$match_arr = array();
		
		// Convert to nicer array
		foreach($matches as $match)
		{
			$match_arr[] = array_combine($group_keys, $match);
		}
		
		return $match_arr;
	}
	
	/** 
	 * Test function against a string
	 */
	public function test($str)
	{
		return preg_match($this->compile());
	}
	
	/**
	 * Replace string with new text
	 * CAUTION: Use single quotes (') when you have $names in $replacement
	 */
	public function replace($str, $replacement)
	{
		$groups = $this->groups();
		
		// Replace all variables
		for($i=0; $i < count($groups); $i += 1)
		{
			$group = $groups[$i];
			$replacement = str_replace('$'.$group->name, '$'.$i, $replacement);
		}
	
		return preg_replace($this->compile(), $replacement, $str);
	}
	
	public static function escape($input)
	{
		$inputs = (array) $input;
		
		// Escape all input
		$escaped = array_map(function($input)
		{
			return preg_replace('/([^a-zA-Z0-9åäöÅÄÖ])/', '\\\\$0', $input);
		}, $inputs);
	
		// Only return a string if string is the input
		if(is_string($input))
		{
			return $escaped[0];
		}
		
		return $escaped;
	}
	
	/**
	 * Compile into a regexp string
	 */
	public function compile()
	{
		$group = $this->main_group;
		$contents = $group->compile();
		$contents = substr($contents, 1, -1);
		
		return "/{$contents}/{$this->flags}";
	}
	
	public function debug()
	{
		$items = $this->main_group->debug(0);
		
		foreach($items as $item)
		{
			$name = '';
		
			if($item[1] instanceof Reggo\Group)
			{
				$name = '[name:'.$item[1]->name.']';	
			}
			
			$tabulator = str_pad('-> ', $item[0]*4 + 1, '-', STR_PAD_LEFT);
			$compilation = $item[1]->compile();
			
			$str = str_pad($tabulator.$compilation, 50, ' ');
			echo $str;
			echo str_pad($name, 20, ' ');
			echo str_pad('<-', 30, get_class($item[1]));
			echo "\n";
		}
	}
}
