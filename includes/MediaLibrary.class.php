<?php
require_once __DIR__ . "/ID3Data.class.php";

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
	 * @return null|int The ID of the track or null if not found
	 */
	public function getTrackIdByFilePath($filePath)
	{
		Logger::debug("Requesting ID of '" . $filePath . "'", "MediaLibrary/getTrackIdByFilePath");

		$query = $this->pdo->prepare("
			SELECT `id`
			FROM `tracks`
			WHERE `filePathHash` = UNHEX(MD5(:filePath))
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
	 * Get the file path of the given track.
	 *
	 * @param int $trackId The ID of the track
	 *
	 * @return string|null The absolute file path or null if not found
	 */
	public function getTrackFilePathById($trackId)
	{
		$query = $this->pdo->prepare("
			SELECT `filePath`
			FROM `tracks`
			WHERE `id` = :id
		");

		$query->execute(array
		(
			":id" => $trackId
		));

		if (!$query->rowCount())
		{
			return null;
		}

		return $query->fetch()->filePath;
	}

	/**
	 * Update the stored meta data of the given track from ID3 data inside the file.
	 *
	 * @param int $trackId The ID of the track which should be updated
	 * @param ID3Data|null $id3Data The ID3 data to use (Set to null or omit to read data from the file)
	 *
	 * @return bool Whether the update was successful
	 */
	public function updateTrackFromId3($trackId, $id3Data = null)
	{
		$filePath = $this->getTrackFilePathById($trackId);

		if (!$filePath)
		{
			return false;
		}

		if (!$id3Data)
		{
			$id3Data = new ID3Data;

			if (!$id3Data->readFromFile($filePath))
			{
				return false;
			}
		}

		$query = $this->pdo->prepare("
			UPDATE `tracks`
			SET
				`title` = :title,
				`artist` = :artist,
				`album` = :album,
				`genre` = :genre,
				`year` = :year,
				`trackNumber` = :trackNumber,
				`trackCount` = :trackCount,
				`diskNumber` = :diskNumber,
				`diskCount` = :diskCount,
				`length` = :length
			WHERE `id` = :id
		");

		try
		{
			$query->execute(array
			(
				":title" => $id3Data->title,
				":artist" => $id3Data->artist,
				":album" => $id3Data->album,
				":genre" => $id3Data->genre,
				":year" => (int) $id3Data->year,
				":trackNumber" => (int) $id3Data->trackNumber,
				":trackCount" => (int) $id3Data->trackCount,
				":diskNumber" => (int) $id3Data->diskNumber,
				":diskCount" => (int) $id3Data->diskCount,
				":length" => (int) $id3Data->length,
				":id" => $trackId
			));
		}
		catch (PDOException $exception)
		{
			Logger::error("Database error: " . $exception->getMessage(), "MediaLibrary/updateTrackFromId3");

			return false;
		}

		return true;
	}

	/**
	 * Add the given track to the library
	 *
	 * @param string $filePath The path to the file
	 *
	 * @return int|null The ID of the track or null if the track could not be added (e.g. not a valid media file)
	 */
	public function addTrack($filePath)
	{
		$id3Data = new ID3Data;

		if (!$id3Data->readFromFile($filePath))
		{
			return null;
		}

		Logger::info("Adding file '" . $filePath . "' to tracks", "MediaLibrary/addTrack");

		$query = $this->pdo->prepare("
			INSERT INTO `tracks`
			SET
				`filePath` = :filePath,
				`filePathHash` = UNHEX(MD5(:filePath)),
				`dateAdded` = NOW()
		");

		$query->execute(array
		(
			":filePath" => $filePath
		));

		$trackId = (int) $this->pdo->lastInsertId();

		$this->updateTrackFromId3($trackId, $id3Data);

		return $trackId;
	}
}