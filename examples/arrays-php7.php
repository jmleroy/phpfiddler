<?php
use PhpFiddler\Fiddle;

$fiddle = new Fiddle;

$fiddle->start();
$fiddle->withExecutionTime();

$fiddle->export(function() {
    preg_match('/^\d{3}\-\d{3}\-(\d{4})$/', '555-666-7890', $matches);
    [$match, $lastFour] = $matches;
});

$fiddle->end();