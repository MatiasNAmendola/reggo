# Reggo
Reggo makes it easier to **Regular expressions** with structure

## Why?
When creating a regexp, especially bigger ones, it is hard to keep a strucure. You can not

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

// Compile into a normal regexp
$reggo->compile();		// /([a-zA-ZåäöÅÄÖ]+)\s([a-zA-ZåäöÅÄÖ]+)/

// Matching against a string
$match = $reggo->match('Brad Pit');
// array(
// 		[0] => array(
//			'all' => 'Brad pit',
// 			'first' => 'Brad',
// 			'last' => 'Pit'
//		)
// )

// Replacing
$reggo->replace('Brad Pit', 'Hans $last');		// Hans Pit
```

## Requirements
Reggo requires **php 5.3** or later to work properly. This is due to the use of `callbacks` as well as `__call()` and `__callStatic`. They are the things that make it possible to create a nice **API**.

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
```

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
Makes an exact match, does not escape input. Usage `exact($string)`

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->exact('[a-f]+');
});
// /[a-f]+/
```

### Any
Match any of the input chars, `any(array($str1, $st2...))`

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
To escape a string you can use the function `escape($string)`

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
Matches `Reggo` against a string and returns an array with all matches.

```php
// Matches a name as "first last"
$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->group('first')->alpha('+');
	$reggo->space();
	$reggo->group('last')->alpha('+');
});

$match = $reggo->match('Brad Pit, Lotten Harold');
// array(
// 		[0] => array(
//			'all' => 'Brad pit',
// 			'first' => 'Brad',
// 			'last' => 'Pit'
//		),
//		[1] => array(...)
// )

```

### Replace
Replace the match with something else. In `Reggo` you can use `$group_name` to replace with the match of that group, instead of `$0, $1 ...` as you normaly would.

```php
<?php
// Matches a name as "first last"
$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->group('first')->alpha('+');
	$reggo->space();
	$reggo->group('last')->alpha('+');
});

// Replacing
$reggo->replace('Brad Pit', 'Hans $last');		// Hans Pit
```

### Compile
Compiles `Reggo` into a normal regexp.

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->num();
});

$reggo->flags(Utv\Reggo::FLAG_GLOBAL);

// Compile into normal regexp
$reggo->compile(); 		// /[0-9]/g
```

### Flags
You can add flags to the regexp by using the function `$reggo->flags($string)`.

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->num();
});

// Add global flag
$reggo->flags('g');

// Compile into normal regexp
$reggo->compile();		// /[0-9]/g
```

You can use the globals if you do not remember the flags.

```php
$reggo->flags(Utv\Reggo::FLAG_GLOBAL);
```

### Escape strings
You can use `Reggo` to escape string for you, this way you can se cleaner code.

```php
<?php

$reggo = new Utv\Reggo(function($reggo)
{
	$reggo->any(array(
		'com',
		'se',
		Utv\Reggo::escape('co.uk')
	));
	
	// Or escape the whole array
	$reggo->any(Utv\Reggo::escape(array(
		'com',
		'se',
		'co.uk'
	)));
});
// /com|se|co\.uk/

```
