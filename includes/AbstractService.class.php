<?php
namespace service;

require_once __DIR__ . "/DBConnection.class.php";
require_once __DIR__ . "/ServiceErrorCodes.class.php";
require_once __DIR__ . "/ServiceException.class.php";

/**
 * Class used to build new services.
 * This class should not be instanced directly. Instead it should be extended by another class.
 *
 * @package service
 */
abstract class AbstractService
{
	/**
	 * The service is called in RAW mode
	 * Service methods can set their own headers
	 */
	const CALLTYPE_RAW = "raw";
	/**
	 * The service is called in RPC mode
	 * Service methods should not output any data directly (e.g. header(), echo or print), instead the return value of the method should be used
	 */
	const CALLTYPE_RPC = "rpc";

	/**
	 * @var string The type of the call (e.g. raw or rpc)
	 */
	protected $callType;
	/**
	 * @var \Config Instance of Config class
	 */
	protected $config;
	/**
	 * @var \PDO Instance of PDO connected to the main database
	 */
	protected $pdo;

	/**
	 * Constructor for all services
	 *
	 * @param string $callType The type of the call (e.g. raw or rpc)
	 * @param \Config $config An instance of the Config class
	 * @param \PDO $pdo An instance of PDO connected to the main database
	 */
	public function __construct($callType, $config, $pdo)
	{
		$this->callType = $callType;
		$this->config = $config;
		$this->pdo = $pdo;
	}

	/**
	 * Create a new instance of a service class
	 *
	 * @param string $serviceName The name of the new service (Class name)
	 * @return mixed The instance of the class
	 */
	protected function newServiceInstance($serviceName)
	{
		require_once __DIR__ . "/services/" . $serviceName . ".class.php";
		$fullServiceName = "service\\" . $serviceName;
		return new $fullServiceName($this->callType, $this->config, $this->pdo);
	}

	protected function requireCallType($type)
	{
		if ($this->callType != $type)
		{
			throw new ServiceException(\ServiceErrorCodes::INVALID_CALLTYPE, $type);
		}
	}
}