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
		
		// Start and end
		$reggo = new Utv\Reggo(function($group)
		{
			$group->start();
			$group->num();
			$group->end();
		});
		
		$this->assertSame('/^[0-9]$/', $reggo->compile());
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
	
	public function testGroups()
	{
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->group('next')->alpha();
			$group->group('third')->num();
			$group->group('fourth')->group('fifth')->num();
		});
		
		$groups = $reggo->groups();
		$this->assertEquals(5, count($groups));
		//$this->assertContainsOnlyInstancesOf('Group', $groups);
	}
	
	public function testMatch()
	{
		$reggo = new Utv\Reggo('test', function($group)
		{
			$group->group('name')->alpha('+');
			$group->group('number')->num('+');
		});
		
		$matching = $reggo->match('hellomoto097 hello38');
		
		// First match
		$this->assertArrayHasKey('test', $matching[0]);
		$this->assertArrayHasKey('name', $matching[0]);
		$this->assertArrayHasKey('number', $matching[0]);
		
		$this->assertSame('hellomoto097', $matching[0]['test']);
		$this->assertSame('hellomoto', $matching[0]['name']);
		$this->assertSame('097', $matching[0]['number']);
		
		$this->assertSame(2, count($matching));
		
		// Second match
		$this->assertArrayHasKey('test', $matching[1]);
		$this->assertArrayHasKey('name', $matching[1]);
		$this->assertArrayHasKey('number', $matching[1]);
		
		$this->assertSame('hello38', $matching[1]['test']);
		$this->assertSame('hello', $matching[1]['name']);
		$this->assertSame('38', $matching[1]['number']);
	}
	
	public function testReplace()
	{
		$reggo = new Utv\Reggo(function($group)
		{
			$group->group('name')->alpha('+');
			$group->num('*');
		});
		
		$replaced = $reggo->replace('hellomoto38 and54 whello34', '$name');
		$this->assertSame('hellomoto and whello', $replaced);

		// Test with names overlapp
		$replaced = $reggo->replace('hellomoto38 and54 whello34', '$names');
		$this->assertSame('hellomotos ands whellos', $replaced);
		
		// Test replacing nothing
		$replaced = $reggo->replace('hellomoto38 and54 whello34', '$all');
		$this->assertSame('hellomoto38 and54 whello34', $replaced);
	}
	
	public function testEscape()
	{
		$tests = str_split('-_.\/"\'!#?+*}{[]');
		$escaped = Utv\Reggo::escape($tests);
		
		$i = 0;
		foreach($tests as $test)
		{
			$this->assertSame('\\'.$test, Utv\Reggo::escape($test));
			$this->assertSame('\\'.$test, $escaped[$i]);
			$i += 1;
		}
	}
}
