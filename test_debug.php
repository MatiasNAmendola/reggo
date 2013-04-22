<?php

require('reggo.php');

$reggo = new Utv\Reggo(function($group)
{
	$group->alpha();
	$group->num();
	
	$group->group('sub')->any(array(
		'lol',
		'i',
		'bollen'
	));
});
$reggo->debug();
