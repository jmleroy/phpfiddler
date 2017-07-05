<?php
$fiddle->export(function () {
    $a = true;
    $b = false;
    $c = false;
    
    return $a || $b && $c;
});