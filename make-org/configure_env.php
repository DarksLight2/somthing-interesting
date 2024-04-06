<?php

$args = [];

for ($i = 1; $i < $argc; $i++) {
    [$arg, $value] = explode('=', $argv[$i], 2);
    $args[str_replace("--", "", $arg)] = $value;
}

$env = file_get_contents('.env');
$lines = explode("\n", $env);

$vars = [];

foreach ($lines as $line) {
    [$key, $value] = explode('=', $line, 2);
    $vars[$key] = $value;
}

$vars['DB_DATABASE'] = $args['short_org_name'];
