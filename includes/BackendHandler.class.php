<?php
require_once __DIR__ . "/DBConnection.class.php";
require_once __DIR__ . "/DocCommentParser.class.php";
require_once __DIR__ . "/ServiceErrorCodes.class.php";
require_once __DIR__ . "/ServiceException.class.php";

class BackendHandler
{
	/**
	 * @var String
	 */
	private $callType;
	/**
	 * @var Config
	 */
	private $config;
	/**
	 * @var StdClass
	 */
	private $errorData;
	/**
	 * @var PDO
	 */
	private $pdo;

	public function __construct($callType, $config)
	{
		$this->config = $config;
		$this->callType = $callType;
		$this->errorData = new StdClass;

		$dbConnection = new DBConnection($this->config, false);
		if ($dbConnection)
		{
			$this->pdo = $dbConnection->getPdo();
			if (!$this->pdo)
			{
				$this->setErrorData(ServiceErrorCodes::DB_ERROR);
			}
		}
		else
		{
			$this->setErrorData(ServiceErrorCodes::DB_ERROR);
		}
	}

	public function getErrorData()
	{
		return $this->errorData;
	}

	public function execute($method, $parameters)
	{
		if ($this->config->getValue("logLevel") == LOG_DEBUG)// Prevent running the following line even if not in debug mode (Encoding JSON could be time intensive!)
		{
			LOGGER::debug("Executing method '" . $method . "' with parameters: " . json_encode($parameters), "BackendHandler/execute");
		}

		$resultData = new StdClass;

		$serviceMethod = explode(".", $method);
		$serviceName = basename($serviceMethod[0]);
		$methodName = $serviceMethod[1];
		$serviceClassFile = __DIR__ . "/services/" . $serviceName . ".class.php";
		$serviceInstance = null;

		if (file_exists($serviceClassFile))
		{
			require_once $serviceClassFile;
			$fullServiceName = "service\\" . $serviceName;
			$serviceInstance = new $fullServiceName($this->callType, $this->config, $this->pdo);
		}

		if ($serviceInstance and method_exists($serviceInstance, $methodName))
		{
			try
			{
				$reflectionInstance = new ReflectionMethod($serviceInstance, $methodName);
				$docCommentParser = new DocCommentParser($reflectionInstance->getDocComment());

				foreach ($docCommentParser->getParameters() as $parameter => $parameterData)
				{
					if ($parameterData->required and !isset($parameters->{$parameter}))
					{
						throw new \service\ServiceException(ServiceErrorCodes::JSONRPC_INVALID_PARAMETERS, $parameter);
					}
				}

				$resultData->result = $serviceInstance->$methodName($parameters);
			}
			catch (\service\ServiceException $exception)
			{
				$resultData->error = new StdClass;
				$resultData->error->code = $exception->getCode();
				$resultData->error->data = $exception->getData();
				$resultData->error->message = $exception->getMessage();
			}
			catch (PDOException $exception)
			{
				$resultData->error = new StdClass;
				$resultData->error->code = ServiceErrorCodes::DB_ERROR;
				$resultData->error->message = ServiceErrorCodes::getErrorMessageByCode(ServiceErrorCodes::DB_ERROR);

				error_log($exception->getMessage());// Log the database error to the standard log file
			}
		}
		else
		{
			$resultData->error = new StdClass;
			$resultData->error->code = ServiceErrorCodes::JSONRPC_METHOD_NOT_FOUND;
			$resultData->error->message = ServiceErrorCodes::getErrorMessageByCode(ServiceErrorCodes::JSONRPC_METHOD_NOT_FOUND);
		}

		if ($this->config->getValue("logLevel") == LOG_DEBUG)// Prevent running the following line even if not in debug mode (Encoding JSON could be time intensive!)
		{
			LOGGER::debug("Result data: " . json_encode($resultData), "BackendHandler/execute");
		}

		return $resultData;
	}

	public function formatJsonRpc($requestData, $resultData)
	{
		$jsonData = new StdClass;

		$jsonData->jsonrpc = "2.0";
		$jsonData->id = $requestData->id;

		if ($requestData->jsonrpc == "2.0" and isset($requestData->id) and isset($requestData->method))
		{
			if ($resultData->error and $resultData->error->code)
			{
				$jsonData->error = $resultData->error;
			}
			else
			{
				$jsonData->result = $resultData->result;
			}
		}
		else
		{
			$jsonData->error = new StdClass;
			$jsonData->error->code = ServiceErrorCodes::JSONRPC_INVALID_REQUEST;
			$jsonData->error->message = ServiceErrorCodes::getErrorMessageByCode(ServiceErrorCodes::JSONRPC_INVALID_REQUEST);
		}

		return $jsonData;
	}

	private function setErrorData($code)
	{
		$this->errorData->code = $code;
		$this->errorData->message = ServiceErrorCodes::getErrorMessageByCode($code);
	}
}