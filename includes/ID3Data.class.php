<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../vendor/james-heinrich/getid3/getid3/getid3.php";// TODO: Not automatically included by Composer autoload mechanism?

class ID3Data
{
	public $title;
	public $artist;
	public $album;
	public $genre;
	public $year;
	public $trackNumber;
	public $trackCount;
	public $diskNumber;
	public $diskCount;
	public $length;
	
	public function readFromFile($filePath)
	{
		Logger::debug("Reading ID3 data from '" . $filePath . "'", "ID3Data/readFromFile");

		$getId3 = new getID3;

		$metaData = $getId3->analyze($filePath);

		if (!$metaData)
		{
			Logger::debug("No ID3 data found", "ID3Data/readFromFile");
			return false;
		}

		getid3_lib::CopyTagsToComments($metaData);

		if (!isset($metaData["comments"]))
		{
			Logger::debug("No ID3 data available", "ID3Data/readFromFile");
			return false;
		}

		$mergedMetaData = $metaData["comments"];

		$this->title = isset($mergedMetaData["title"][0]) ? $mergedMetaData["title"][0] : null;
		$this->artist = isset($mergedMetaData["artist"][0]) ? $mergedMetaData["artist"][0] : null;
		$this->album = isset($mergedMetaData["album"][0]) ? $mergedMetaData["album"][0] : null;
		$this->genre = isset($mergedMetaData["genre"][0]) ? $mergedMetaData["genre"][0] : null;
		$this->year = isset($mergedMetaData["year"][0]) ? (int) $mergedMetaData["year"][0] : null;
		$this->trackNumber = isset($mergedMetaData["track"][0]) ? (int) $mergedMetaData["track"][0] : null;
		$this->length = isset($mergedMetaData["length"][0]) ? (int) $mergedMetaData["length"][0] : null;

		Logger::debug("ID3 data loaded", "ID3Data/readFromFile");

		return true;
	}
}