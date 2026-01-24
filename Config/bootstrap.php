<?php

session_start();

require __DIR__ . '/../Core/helpers.php';
require __DIR__ . '/../Core/Database.php';
require __DIR__ . '/../Core/View.php';
require __DIR__ . '/../Core/Router.php';
require __DIR__ . '/../Core/Auth.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../Controllers/' . $class . '.php',
        __DIR__ . '/../Models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});
