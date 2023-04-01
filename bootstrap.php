<?php

require_once __DIR__ . "/../vendor/autoload.php";

spl_autoload_register(function ($class_name))
{
    include __DIR__ . "/../classes/Page.php";
}

use Tracy\Debugger;

Debugger::enable();

