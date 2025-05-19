<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

$config = new Configuration();

return $config
    ->addPathToScan(__DIR__ . '/../src', false)
    ->addPathToScan(__DIR__ . '/../tests', true)
;
