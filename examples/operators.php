<?php
include('fiddle.class.php');

Fiddle::start();

Fiddle::export(function () {
    $a = true;
    $b = false;
    $c = false;
    
    return $a || $b && $c;
});

Fiddle::end();