<?php

$loader = require __DIR__ . '/vendor/autoload.php';

$application = new \Symfony\Component\Console\Application();
$application->add(new \Groot\RunCommand());
$application->run();