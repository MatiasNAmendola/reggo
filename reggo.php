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
		
		// Check if provided a function
		/*if(is_callable($callable))
		{
			// Create new group
			$this->main_group = new Reggo\Group($group_name);
		
			// Call this callable
			call_user_func_array($callable, array(&$this->main_group));
			
			//return new Reggo($group);
		}
		else
		{
			// Only group name is provided, create default group
			$this->main_group = new Reggo\Group($callable);
			//return new Reggo($group);
		}*/
	}
	
	public function compile()
	{
		$group = $this->main_group;
		$contents = $group->compile();
		$contents = substr($contents, 1, -1);
		
		return "/{$contents}/{$this->flags}";
	}
}
