<?php
/**
 * Class used to connect to the database
 */
class DBConnection
{
	private $error;
	private $pdo;

	/**
	 * @param Config $config Instance of Config class
	 * @param bool $dieOnError Whether to die on error or not
	 */
	public function __construct($config, $dieOnError = true)
	{
		try
		{
			$this->pdo = new PDO($config->getValue("database.dsn"), $config->getValue("database.username"), $config->getValue("database.password"), array
			(
				PDO::ATTR_TIMEOUT => $config->getValue("database.timeout")
			));

			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

			$this->pdo->query("SET NAMES utf8");
			$this->pdo->query("SET sql_mode='ANSI,TRADITIONAL'");// Required for some queries using GROUP BY
		}
		catch (PDOException $exception)
		{
			Logger::error("Database connection failed: " . $exception->getMessage(), "DBConnection");

			if ($dieOnError)
			{
				die("Database connection failed!");
			}
			else
			{
				$this->error = $exception->getMessage();
			}
		}
	}

	public function getError()
	{
		return $this->error;
	}

	public function getPdo()
	{
		return $this->pdo;
	}
}