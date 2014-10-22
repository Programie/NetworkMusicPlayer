<?php
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

			if (is_dir($path . "/" . $item))
			{
				$this->scanDir($path . "/" . $item);
				continue;
			}

			if (is_file($path . "/" . $item))
			{
				// TODO: Check if this is a valid media file (e.g. mp3)
				// TODO: Retrieve ID3 tag data from file
				// TODO: Add track to library using $this->mediaLibrary->addTrack()
			}
		}
	}
}