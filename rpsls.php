<?php

require __DIR__ . '/vendor/autoload.php';

use App\PlayCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$input = new ArrayInput(['play']);
$application = new Application;
$application->add(new PlayCommand);
$application->run($input);
