<?php
$fiddle->withExecutionTime()
    ->export(function() {
    preg_match('/^\d{3}\-\d{3}\-(\d{4})$/', '555-666-7890', $matches);
    [$match, $lastFour] = $matches;
});