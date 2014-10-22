#! /usr/bin/env php
<?php
require_once __DIR__ . "/../includes/Config.class.php";

$filePath = @$argv[1];

$config = new Config($filePath);

$config->save();