<?php
include_once('fiddle.class.php');

Fiddle::start();
Fiddle::withExecutionTime();

Fiddle::export(function() {
    preg_match('/^\d{3}\-\d{3}\-(\d{4})$/', '555-666-7890', $matches);
    [$match, $lastFour] = $matches;
});

Fiddle::end();