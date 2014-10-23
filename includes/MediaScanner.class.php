<?php
require_once __DIR__ . "/MediaLibrary.class.php";

class MediaScanner
{
	/**
	 * @var Config Instance of the Config class
	 */
	private $config;
	/**
	 * @var MediaLibrary Instance of the MediaLibrary class
	 */
	private $mediaLibrary;

	public function __construct($config, $pdo)
	{
		$this->config = $config;

		$this->mediaLibrary = new MediaLibrary($this->config, $pdo);
	}

	/**
	 * Perform a full scan of the configured media directory
	 */
	public function fullScan()
	{
		$this->scanDir($this->config->getValue("paths.media"));
	}

	/**
	 * Scan the specified directory recursively for media and at it to the media library.
	 *
	 * @param string $path The absolute path to the directory
	 */
	public function scanDir($path)
	{
		$dir = scandir($path);
		foreach ($dir as $item)
		{
			if ($item[0] == ".")
			{
				continue;
			}

			$filePath = $path . "/" . $item;

			if (is_dir($filePath))
			{
				$this->scanDir($filePath);
				continue;
			}

			if (is_file($filePath))
			{
				$trackId = $this->mediaLibrary->getTrackIdByFilePath($filePath);
				if ($trackId)
				{
					continue;
				}

				if ($this->mediaLibrary->addTrack($filePath) == null)
				{
					Logger::error("Unable to add '" . $filePath . "'!", "MediaScanner/scanDir");
				}
			}
		}
	}
}