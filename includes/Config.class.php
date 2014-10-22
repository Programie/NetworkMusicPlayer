<?php
class Config
{
	private $configFile;
	private $configData;

	private function getValueByPath($dataTree, $path)
	{
		$pathParts = explode(".", $path);

		foreach ($pathParts as $name)
		{
			if (!isset($dataTree->{$name}))
			{
				return null;
			}

			$dataTree = $dataTree->{$name};
		}

		return $dataTree;
	}

	private function setValueByPath($dataTree, $path, $value, $add)
	{
		$pathParts = explode(".", $path);

		$name = $pathParts[0];

		if (!$add and !isset($dataTree->{$name}))
		{
			return false;
		}

		if (count($pathParts) == 1)
		{
			$dataTree->{$name} = $value;

			return true;
		}

		if (isset($dataTree->{$name}))
		{
			$object = $dataTree->{$name};
		}
		else
		{
			$object = new StdClass;

			$dataTree->{$name} = $object;
		}

		$this->setValueByPath($object, implode(".", array_slice($pathParts, 1)), $value, $add);

		return true;
	}

	public function __construct($filePath = null)
	{
		if ($filePath)
		{
			$this->configFile = $filePath;
		}
		else
		{
			$this->configFile = __DIR__ . "/../config/config.json";
		}

		$this->load();
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function hasValue($name)
	{
		return isset($this->configData->{$name});
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function getValue($name)
	{
		if (!$this->hasValue($name))
		{
			throw new Exception("Unknown config value: " . $name);
		}

		$itemData = $this->configData->{$name};

		if (isset($itemData->value) and $itemData->value != null)
		{
			return $itemData->value;
		}
		elseif (isset($itemData->defaultValue) and $itemData->defaultValue != null)
		{
			return $itemData->defaultValue;
		}
		else
		{
			throw new Exception("Config value '" . $name . "' does not have a default value!");
		}
	}

	public function getTemplate()
	{
		return json_decode(file_get_contents(__DIR__ . "/../config/config.template.json"));
	}

	public function getConfigData()
	{
		return $this->configData;
	}

	public function load()
	{
		$this->configData = $this->getTemplate();

		if (file_exists($this->configFile))
		{
			$configData = json_decode(file_get_contents($this->configFile));
			if ($configData)
			{
				foreach ($this->configData as $name => $itemData)
				{
					$itemData->value = $this->getValueByPath($configData, $name);
				}
			}
		}
	}

	public function save()
	{
		$data = new StdClass;

		foreach ($this->configData as $name => $itemData)
		{
			$value = null;

			if (isset($itemData->value))
			{
				$value = $itemData->value;
			}

			if (isset($itemData->defaultValue) and $value == $itemData->defaultValue)
			{
				$value = null;
			}

			$this->setValueByPath($data, $name, $value, true);
		}

		file_put_contents($this->configFile, json_encode($data, JSON_PRETTY_PRINT));
	}
}