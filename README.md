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

## Requirements
Reggo requires **php 5.3** or later to work properly

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
There are predefined matches for characters, these are

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->alpha();		// [a-zA-ZåäöÅÄÖ]
	$reggo->alphaeng();		// [a-zA-Z]
	$reggo->lower();		// [a-zåäö]
	$reggo->upper();		// [A-ZÅÄÖ]
	
	$reggo->num();			// [0-9]
	$reggo->number();		// [0-9]
	
	$reggo->dash(); 		// [\-\_]
	$reggo->dot();			// [\.]
	$reggo->period();		// [\.]
	$reggo->space();		// [\s]
});
```

These can be combined as you wich, delimited with an underscore (_)

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->alpha_num();		// [a-zA-ZåäöÅÄÖ0-9]
	
	$reggo->alpha_dot_num();	// [a-zA-ZåäöÅÄÖ\.0-9]
	
	$reggo->lower_dash();		// [a-zåäö\-\_]
});
```

### Exact
Makes an exact match, does not escape input

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->exact('[a-f]+');
});
// /[a-f]+/
```

### Any
Match any of the input chars

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->escape('http://google.');
	$reggo->group('topdomain')->any(array(
		'se',
		'com',
		'dk'
	));
});
// /http\:\/\/google\.(se|com|dk)/
```

### Escape
To escape a string you can use the function `escape()`

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->escape('hello-lotten');
});
// /hello\-lotten/
```

This will create an exact match for *hello-lotten*, the dash will be escaped.

## Functions

### Match

### Replace

### Compile

### Flags

### Escape strings
