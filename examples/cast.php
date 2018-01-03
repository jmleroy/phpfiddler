<?php
$fiddle->export(function () {
    $a = [1, 2, 3];
    return (Object)$a;
});

$fiddle->export(function () {
	$a = '';
	return intval($a);
});

$fiddle->export(function () {
	$a = null;
	return intval($a);
});