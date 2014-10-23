<?php
require_once __DIR__ . "/Logger.class.php";

class Utils
{
	/**
	 * Opposite of parse_url: Builds an URL string from the given array returned by parse_url.
	 *
	 * @param array $url The array returned bu parse_url
	 *
	 * @return string The full URL
	 */
	public static function buildUrl($url)
	{
		$fullUrl = "";

		if (!isset($url["scheme"]) or !$url["scheme"])
		{
			return null;
		}

		$fullUrl .= $url["scheme"];
		$fullUrl .= "://";

		if (isset($url["user"]))
		{
			$fullUrl .= $url["user"];
			if (isset($url["pass"]))
			{
				$fullUrl .= ":";
				$fullUrl .= $url["pass"];
			}
			$fullUrl .= "@";
		}

		if (!isset($url["host"]) or !$url["host"])
		{
			return null;
		}

		$fullUrl .= $url["host"];

		$defaultPort = null;
		switch ($url["scheme"])
		{
			case "http":
				$defaultPort = 80;
				break;
			case "https":
				$defaultPort = 443;
				break;
		}

		if ($defaultPort and isset($url["port"]) and $url["port"] != $defaultPort)
		{
			$fullUrl .= ":";
			$fullUrl .= $url["port"];
		}

		if (isset($url["path"]) and $url["path"])
		{
			$fullUrl .= $url["path"];
		}

		if (isset($url["query"]) and $url["query"])
		{
			$fullUrl .= "?";
			$fullUrl .= $url["query"];
		}

		if (isset($url["fragment"]) and $url["fragment"])
		{
			$fullUrl .= "#";
			$fullUrl .= $url["fragment"];
		}

		return $fullUrl;
	}

	public static function castValue($value, $type, $allowNull = false)
	{
		if ($value == null and $allowNull)
		{
			return null;
		}

		settype($value, $type);

		return $value;
	}

	/**
	 * Convert the specified file size in bytes to a human readable format.
	 *
	 * @param int $value The file size in bytes
	 *
	 * @return string The size format as "<Size> <Unit> (e.g. 100 MB)
	 */
	public static function formatFileSize($value)
	{
		$units = array("B", "KB", "MB", "GB", "TB");
		$value = (int) abs($value);

		foreach ($units as $index => $unit)
		{
			if ($value < 1024 || $index == count($units) - 1)
			{
				return round($value, 2) . " " . $unit;
			}
			$value /= 1024;
		}

		return "";
	}

	public static function formatTime($seconds)
	{
		$days = floor($seconds / 60 / 60 / 24);
		$seconds -= $days * 60 * 60 * 24;

		$hours = floor($seconds / 60 / 60);
		$seconds -= $hours * 60 * 60;

		$minutes = floor($seconds / 60);
		$seconds -= $minutes * 60;

		$string = "";

		if ($days)
		{
			if ($days == 1)
			{
				$string .= "1 day";
			}
			else
			{
				$string .= $days . " days";
			}

			$string .= " ";
		}

		$string .= str_pad($hours, 2, "0", STR_PAD_LEFT);
		$string .= ":";
		$string .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
		$string .= ":";
		$string .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

		return $string;
	}

	/**
	 * Returns the root URL (e.g. http://example.com or https://sub.example.com:8443)
	 *
	 * @return string The URL
	 */
	public static function getRootUrl()
	{
		return ($_SERVER["HTTPS"] ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"];
	}

	/**
	 * Try to create an empty temporary file and return the full path of it.
	 *
	 * @param Config $config Instance of the Config class
	 * @return string The full path of the created file
	 */
	public static function getTempFile($config)
	{
		return tempnam($config->getValue("paths.tempDir"), "NetworkMusicPlayer_");
	}

	/**
	 * Get the application user agent
	 *
	 * @return string The user agent
	 */
	public static function getUserAgent()
	{
		$curlVersion = curl_version();
		return "NetworkMusicPlayer/" . self::getVersion() . " (curl/" . $curlVersion["version"] . ")";
	}

	/**
	 * Get the application version
	 *
	 * @return string The version
	 */
	public static function getVersion()
	{
		$versions = array();

		$document = new \DOMDocument();
		$document->load(__DIR__ . "/../changes/releases.xml");
		$releases = $document->getElementsByTagName("release");

		/**
		 * @var $release DOMElement
		 */
		foreach ($releases as $release)
		{
			$versions[] = $release->getAttribute("version");
		}

		usort($versions, function($item1, $item2)
		{
			return version_compare($item2, $item1);
		});

		return $versions[0];
	}
}