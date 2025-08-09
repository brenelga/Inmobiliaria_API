<?php
$port = (int)($_ENV['PORT'] ?? 8000);
$command = sprintf(
    'php -S 0.0.0.0:%d -t public %s',
    $port,
    escapeshellarg(__DIR__.'/public/index.php')
);
passthru($command);