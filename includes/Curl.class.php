<?php
require_once __DIR__ . "/Utils.class.php";
/**
 * Class providing OOP for cURL functions
 */
class Curl
{
	/**
	 * @var string The content returned from the requested URL (Only available if CURLOPT_RETURNTRANSFER is set to true)
	 */
	private $content;
	/**
	 * @var resource Curl resource handler
	 */
	private $handle;
	/**
	 * @var string The old URL (As specified in constructor)
	 */
	private $oldUrl;
	/**
	 * @var int The number of retries used
	 */
	private $retryCount;
	/*
	 * @var resource File handler used for verbose file writing (Temporary file)
	 */
	private $verboseFileHandle;

	/**
	 * @param string $url URL which should be called
	 */
	public function __construct($url)
	{
		$this->oldUrl = $url;
		$this->handle = curl_init($url);
		$this->retryCount = 0;
		$this->setOpt(CURLOPT_USERAGENT, Utils::getUserAgent());
	}

	public function __destruct()
	{
		$this->close();
	}

	public function close()
	{
		if (is_resource($this->handle))
		{
			curl_close($this->handle);
		}
	}

	public function getErrorNumber()
	{
		return curl_errno($this->handle);
	}

	public function getErrorString()
	{
		return curl_error($this->handle);
	}

	/**
	 * Get the initial URL passed to the constructor.
	 *
	 * @return string The same string as passed as $url of the constructor
	 */
	public function getOldUrl()
	{
		return $this->oldUrl;
	}

	/**
	 * Perform the cURL session.
	 *
	 * @return mixed The response of curl_exec
	 */
	public function exec()
	{
		$this->content = curl_exec($this->handle);

		return $this->content;
	}

	/**
	 * Get the content of the cURL session.
	 * This is the same output as of curl_exec.
	 *
	 * @return string The content of the cURL session
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Get the internal cURL handle as returned by curl_init.
	 *
	 * @return resource The cURL handle
	 */
	public function getHandle()
	{
		return $this->handle;
	}

	public function getInfo($option = null)
	{
		if ($option)
		{
			return curl_getinfo($this->handle, $option);
		}
		else
		{
			return curl_getinfo($this->handle);
		}
	}

	/**
	 * Get the content of the verbose output if enabled with setVerbose.
	 *
	 * @return array|null An array containing the lines of the verbose output or null if verbose mode was not enabled
	 */
	public function getVerboseContent()
	{
		if ($this->verboseFileHandle)
		{
			fseek($this->verboseFileHandle, 0);

			$lines = array();
			while ($line = fgets($this->verboseFileHandle))
			{
				$lines[] = $line;
			}

			return $lines;
		}

		return null;
	}

	/**
	 * Get the handle of the temporary verbose output file.
	 *
	 * @return mixed The file handle of the temporary file
	 */
	public function getVerboseFileHandle()
	{
		return $this->verboseFileHandle;
	}

	/**
	 * Set the content of the cURL session.
	 *
	 * This is normally only called by CurlMulti.
	 *
	 * @param string $content The content of the cURL session
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	public function setOpt($option, $value)
	{
		return curl_setopt($this->handle, $option, $value);
	}

	public function setOptsAsArray($options)
	{
		return curl_setopt_array($this->handle, $options);
	}

	/**
	 * Set all future requests of this instance to verbose mode.
	 */
	public function setVerbose()
	{
		$this->verboseFileHandle = tmpfile();
		$this->setOpt(CURLOPT_VERBOSE, true);
		$this->setOpt(CURLOPT_STDERR, $this->verboseFileHandle);
	}

	/**
	 * Get the total number of retries of this instance.
	 *
	 * @return int The number of retries
	 */
	public function getRetryCount()
	{
		return $this->retryCount;
	}

	/**
	 * Retry if the previous request failed (returned a status code not between 100 and 299).
	 *
	 * @return bool Whether the request has been retried (true) or not (false)
	 */
	public function retryIfFailed()
	{
		$httpCode = $this->getInfo(CURLINFO_HTTP_CODE);

		// Retry if the status code was not between 100 and 299
		if ($httpCode < 100 or $httpCode > 299)
		{
			$this->exec();
			$this->retryCount++;

			return true;
		}

		return false;
	}
}