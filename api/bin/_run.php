<?php
try {
    require "$argv[1]";
    set_time_limit(0);
    $s = new $argv[2]($container);
    $s->work();
} catch(\Throwable $e) {
    $log->error('Runtime error', ['error' => $e]);
}
