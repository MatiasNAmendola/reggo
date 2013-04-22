<?php

namespace Utv;

require_once('reggo_group.php');

class Reggo {
	
	private static $default_group = 'all';
	private $main_group;
	private $flags = '';
	
	/*public function __construct(Reggo\Group &$group)
	{
		$this->main_group = $group;
	}*/
	
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
	 * List of all groups, in the right order
	 */
	public function groups()
	{
		return $this->main_group->groups();
	}
	
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
	
	public function compile()
	{
		$group = $this->main_group;
		$contents = $group->compile();
		$contents = substr($contents, 1, -1);
		
		return "/{$contents}/{$this->flags}";
	}
}
