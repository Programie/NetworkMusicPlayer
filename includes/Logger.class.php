<?php
class Logger
{
	/**
	 * @var Config Instance of Config class
	 */
	public static $config;
	/**
	 * @var string The file in which log entries should be logged (null = stdout)
	 */
	public static $logFile = null;

	public static function log($level, $message, $scope = "")
	{
		if ($level <= self::$config->getValue("logLevel"))
		{
			switch ($level)
			{
				case LOG_DEBUG:
					$level = "Debug";
					break;
				case LOG_ERR:
					$level = "Error";
					break;
				case LOG_INFO:
					$level = "Info";
					break;
				case LOG_WARNING:
					$level = "Warning";
					break;
				default:
					$level = "Unknown";
					break;
			}

			$message = str_replace("\n", "\\n", $message);// Escape line breaks

			$string = "[" . date("Y-m-d H:i:s") . "] [" . $level . "] [" . $scope . "] " . $message . "\n";

			if (Logger::$logFile == null)
			{
				echo $string;
			}
			else
			{
				file_put_contents(Logger::$logFile, $string, FILE_APPEND);
			}
		}
	}

	public static function parseLine($line)
	{
		$data = new StdClass;

		if (preg_match("/\[([0-9\-\ \:]+)\] \[(Debug|Error|Info|Warning|Unknown)\] \[(.*)\] (.*)/", $line, $matches))
		{
			$data->date = strtotime($matches[1]);

			switch ($matches[2])
			{
				case "Debug":
					$data->level = LOG_DEBUG;
					break;
				case "Error":
					$data->level = LOG_ERR;
					break;
				case "Info":
					$data->level = LOG_INFO;
					break;
				case "Warning":
					$data->level = LOG_WARNING;
					break;
				default:
					$data->level = null;
					break;
			}

			$data->scope = $matches[3];
			$data->message = $matches[4];
		}

		return $data;
	}

	public static function debug($message, $scope = null)
	{
		self::log(LOG_DEBUG, $message, $scope);
	}

	public static function error($message, $scope = null)
	{
		self::log(LOG_ERR, $message, $scope);
	}

	public static function info($message, $scope = null)
	{
		self::log(LOG_INFO, $message, $scope);
	}

	public static function warning($message, $scope = null)
	{
		self::log(LOG_WARNING, $message, $scope);
	}
}