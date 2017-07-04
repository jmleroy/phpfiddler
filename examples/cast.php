<?php
include('../fiddle.class.php');

Fiddle::start();

Fiddle::export(function () {
    $a = [1, 2, 3];
    return (Object)$a;
});

Fiddle::end();
