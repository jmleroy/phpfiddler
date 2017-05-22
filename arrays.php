<?php
include_once('fiddle.class.php');

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
Fiddle::export(function() {
	preg_match('/^\d{3}\-\d{3}\-(\d{4})$/', '555-666-7890', $matches);
	[$match, $lastFour] = $matches;
	return $lastFour;
});
Fiddle::end();