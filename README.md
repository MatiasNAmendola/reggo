# Reggo
Reggo is made to make it easier to create structure when making **Regular expressions**

## Why?
When creating a regexp, especially bigger ones, there is hard to keep a strucure. You can not

* Use comments
* Split up in parts

## Usage

```php
<?php
require('reggo.php'):

// Matches a name as "first last"
$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->group('first')->alpha('+');
	$reggo->space();
	$reggo->group('last')->alpha('+');
});

$reggo->compile();
// /([a-zA-ZåäöÅÄÖ]+)\s([a-zA-ZåäöÅÄÖ]+)/

// Matching
$match = $reggo->match('Brad Pit');
// array(
// 		[0] = array(
//			'all' => 'Brad pit',
// 			'first' => 'Brad',
// 			'last' => 'Pit'
//		)
// )

// Replacing
$reggo->replace('Brad Pit', 'Hans $last');
// Hans Pit
```

# Documentation

## Usage

### Groups
To create a group, use the function `group($name, [$callable])`. 

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->group('mygroup', function($group)
	{
		$group->num();
	});
});

$reggo->compile();
// /([0-9])/
```
As you se in the example above, the name of the group is never present in the regexp, but the will be used in 
Reggos functions, for exampe `match` and `replace`.

#### Small groups
It is also possible to create smaller groups through chaining. 

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->group('mygroup')->num();
});

$reggo->compile();
// /([0-9])/

### Matches

### Exact

### Any

### Escape

## Functions

### Match

### Replace

### Compile
