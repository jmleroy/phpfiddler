<?php

$fiddle->start();

$fiddle->export(function () {
    $a = new \DateTime('1965-01-01');
    $b = new \DateTime('1968-01-01');
    
    return [
    	'a' => $a->format('U'),
    	'b' => $b->format('U'),
    	'a < b' => $a->format('U') < $b->format('U'),
    ];
});

$fiddle->end();