<?php
require_once __DIR__ . "/../includes/Config.class.php";
require_once __DIR__ . "/../includes/DBConnection.class.php";
require_once __DIR__ . "/../includes/MediaScanner.class.php";

$config = new Config();

$dbConnection = new DBConnection($config);

$mediaScanner = new MediaScanner($config, $dbConnection->getPdo());

$mediaScanner->fullScan();