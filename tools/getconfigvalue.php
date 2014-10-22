#! /usr/bin/env php
<?php
require_once __DIR__ . "/../includes/Config.class.php";

$path = @$argv[1];

if (!$path)
{
	echo "Usage: " . $argv[0] . " <path>\n";
	exit(1);
}

$config = new Config();

echo $config->getValue($path);