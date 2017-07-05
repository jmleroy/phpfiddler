<?php
use PhpFiddler\Fiddle;

Fiddle::start();

Fiddle::withExecutionTime();
Fiddle::export(function () {
	$a = [ 1, 2, 3 ];
	$b = [ 4, 5, 6, 7 ];
	$c = $a + $b;
	return $c;
});
?>

<?php
Fiddle::export(function () {
	$a = [ 1, 2, 3 ];
	$b = [ 4, 5, 6, 7 ];
	$c = array_replace($b, $a);
	return $c;
});
Fiddle::withExecutionTime(false);
?>

<?php
Fiddle::export(function () {
	$a = [ 'a' => 1, 'b' => 2, 'c' => 3 ];
	$b = [ 'a' => 0, 'd' => 4, 'e' => 5 ];
	$c = $a + $b;
	return $c;
});
?>

<?php
Fiddle::export(function () {
	$a = [ 1, 2, 3 ];
	$b = [ 4, 5, 6, 7 ];
	$c = array_merge($a, $b);
	return $c;
});

?>

<?php

$a = $b = range (0, 999);
$b[] = 1000;
shuffle($a);
shuffle($b);

Fiddle::withExecutionTime();
Fiddle::export(function() use ($a, $b) {
	$c = array_merge(array_diff($a, $b), array_diff($b, $a));
	return $c;
});

$c = 'plok';

Fiddle::export(function() use ($a, $b, $c) {
	sort($a);
	sort($b);
	return $a == $b;
});
Fiddle::end();