<?php
class MediaLibrary
{
	/**
	 * @var Config Instance of the Config class
	 */
	private $config;
	/**
	 * @var PDO Instance of PDO connected to the database
	 */
	private $pdo;

	public function __construct($config, $pdo)
	{
		$this->config = $config;
		$this->pdo = $pdo;
	}

	/**
	 * Search a track by the given file path.
	 *
	 * @param string $filePath The full file path of the track
	 *
	 * @return int The ID of the track or null if not found
	 */
	public function getTrackIdByFilePath($filePath)
	{
		$query = $this->pdo->prepare("
			SELECT `id`
			FROM `tracks`
			WHERE `filePath` = :filePath
		");

		$query->execute(array
		(
			":filePath" => $filePath
		));

		// Track not found
		if (!$query->rowCount())
		{
			return null;
		}

		return (int) $query->fetch()->id;
	}

	/**
	 * Add the given track to the library
	 *
	 * @param string $filePath The path to the file
	 * @param ID3Data $id3Data The data of the ID3 tag
	 *
	 * @return int The ID of the track
	 */
	public function addTrack($filePath, $id3Data)
	{
		$query = $this->pdo->prepare("
			INSERT INTO `tracks`
			SET
				`filePath` = :filePath,
				`title` = :title,
				`artist` = :artist,
				`album` = :album,
				`trackNumber` = :trackNumber,
				`trackCount` = :trackCount,
				`diskNumber` = :diskNumber,
				`diskCount` = :diskCount
		");

		$query->execute(array
		(
			":filePath" => $filePath,
			":title" => $id3Data->title,
			":artist" => $id3Data->artist,
			":album" => $id3Data->album,
			":trackNumber" => $id3Data->trackNumber,
			":trackCount" => $id3Data->trackCount,
			":diskNumber" => $id3Data->diskNumber,
			":diskCount" => $id3Data->diskCount
		));

		return (int) $this->pdo->lastInsertId();
	}
}