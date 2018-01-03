<?php
$fiddle->export(function () {
    $a = true;
    $b = false;
    $c = false;
    
    return $a || $b && $c;
});

$fiddle->export(function () {
    $a = 1;
    $b = 1;

    return $a === $b;
});