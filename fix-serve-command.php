<?php
// Parche temporal para ServeCommand
file_put_contents(
    'vendor/laravel/framework/src/Illuminate/Foundation/Console/ServeCommand.php',
    str_replace(
        '$this->port + 1',
        '(int)$this->port + 1',
        file_get_contents('vendor/laravel/framework/src/Illuminate/Foundation/Console/ServeCommand.php')
    )
);