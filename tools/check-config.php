#! /usr/bin/env php
<?php
require_once __DIR__ . "/../includes/Config.class.php";

$config = new Config();

$missingConfigValues = array();

foreach ($config->getConfigData() as $name => $configData)
{
	if (!isset($configData->defaultValue) and (!isset($configData->value) or $configData->value == null))
	{
		$missingConfigValues[] = $name;
	}
}

if (!empty($missingConfigValues))
{
	echo "Missing config values:\n";

	foreach ($missingConfigValues as $name)
	{
		echo "  " . $name . "\n";
	}
}