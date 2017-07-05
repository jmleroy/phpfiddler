<?php

$fiddle->start();

$fiddle->export(function () {
    $a = 'plok.php';
    return strrchr($a, '.');
});

$fiddle->end();