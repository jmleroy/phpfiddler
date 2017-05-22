<?php
include('fiddle.class.php');

Fiddle::start();

Fiddle::export(function () {
    $a = 'plok.php';
    return strrchr($a, '.');
});

Fiddle::end();