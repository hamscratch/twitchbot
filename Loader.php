<?php

$pee = spl_autoload_extensions('.php');
var_dump($pee);
spl_autoload_register(function (string $class_name) {
    $parts = explode("_", $class_name);
    $filepath = implode("/", $parts);
    $path = __DIR__ .  '/' . $filepath . '.php';
    if (file_exists($path)) {
        require_once($path);
    } else {
        var_dump(debug_backtrace());
    }       
});