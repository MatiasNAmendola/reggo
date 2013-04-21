<?php

require_once('reggo.php');

class ReggoTest extends PHPUnit_Framework_TestCase {
	
	public function testConstruct()
	{
		// Only name
		$reggo = new Utv\Reggo('test');
		$this->assertInstanceOf('Utv\Reggo', $reggo);
		
		$reggo = new Utv\Reggo('test', function($group)
		{
			
		});
		$this->assertInstanceOf('Utv\Reggo', $reggo);
	}
	
	public function testCompileAnyOff()
	{
		// Alpha with length
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->alpha(1, 3);
		});
		$this->assertSame('/[a-zA-ZåäöÅÄÖ]{1,3}/', $reggo->compile());
		
		// Alpha without length
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->alpha();
		});
		$this->assertSame('/[a-zA-ZåäöÅÄÖ]/', $reggo->compile());
		
		// Alpha with not
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->not_alpha();
		});
		$this->assertSame('/[^a-zA-ZåäöÅÄÖ]/', $reggo->compile());
		
		// Multiple anotations
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->alpha_num();
		});
		$this->assertSame('/[a-zA-ZåäöÅÄÖ0-9]/', $reggo->compile());
		
		// Multiple anyOffs
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->alpha_num();
			$group->num(2,3);
		});
		$this->assertSame('/[a-zA-ZåäöÅÄÖ0-9][0-9]{2,3}/', $reggo->compile());
		
		// Special lengths
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->num('+');
			$group->num('.');
			$group->num('*');
			$group->num(1);
		});
		$this->assertSame('/[0-9]+[0-9].[0-9]*[0-9]/', $reggo->compile());
	}
	
	public function testCompileGroups()
	{
		// Inner group
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->group('inner', function($group2)
			{
				$group2->num(1);
			});
		});
		$this->assertSame('/([0-9])/', $reggo->compile());
		
		// Two inner groups
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->group('inner1', function($group2)
			{
				$group2->num(1);
			});
			
			$group->num();
			
			$group->group('inner2', function($group3)
			{
				$group3->num(2, 3);
			});
		});
		$this->assertSame('/([0-9])[0-9]([0-9]{2,3})/', $reggo->compile());
	}
	
	public function testCompileAny()
	{
		// A simple any
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->any(array(
				'lotta',
				'mattias',
				'adam'
			));
		});
		$this->assertSame('/lotta|mattias|adam/', $reggo->compile());
		
		// A little harder
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->group('domainname', function($inner)
			{
				$inner->lower_dash('+');
			});
			
			$group->period();
			
			$group->group('end', function($inner)
			{
				$inner->any(array(
					'se',
					'net',
					'com',
					'co\.uk',
				));
			});
		});
		$this->assertSame('/([a-zåäö\-\_]+)\.(se|net|com|co\.uk)/', $reggo->compile());
	}
	
	public function testCompileExact()
	{
		// Simple
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->exact('adam');
		});
		$this->assertSame('/adam/', $reggo->compile());
		
		// Somthing more
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->exact('adam');
			
			$group->group('inner')->num();
		});
		$this->assertSame('/adam([0-9])/', $reggo->compile());
	}
	
	public function testCompileEscape()
	{
		// Test escaping
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->escape('adam-hansson');
		});
		$this->assertSame('/adam\-hansson/', $reggo->compile());
		
		// More chars
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->escape('adam-hansson, the one and only');
		});
		$this->assertSame('/adam\-hansson\,\ the\ one\ and\ only/', $reggo->compile());
	}
}
