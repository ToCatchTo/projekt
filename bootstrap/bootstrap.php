<?php

require_once __DIR__ . "/../vendor/autoload.php";

spl_autoload_register(
    function ($class_name) {
        $filename =__DIR__ . "/../classes/{$class_name}.php";
        if (file_exists($filename))
            include $filename;
    }
);

spl_autoload_register(
    function ($class_name) {
        $filename =__DIR__ . "/../models/{$class_name}.php";
        if (file_exists($filename))
            include $filename;
    }
);

use Tracy\Debugger;

Debugger::enable();


