<?php
use PhpFiddler\Fiddle;

Fiddle::start();

Fiddle::export(function () {
    $a = 'plok.php';
    return strrchr($a, '.');
});

Fiddle::end();