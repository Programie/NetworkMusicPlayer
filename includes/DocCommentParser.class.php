<?php
class DocCommentParser
{
	/**
	 * @var DOMDocument
	 */
	private $document;

	/**
	 * Initialize the parser with the given comment string
	 *
	 * @param string $comment The comment string to parse
	 */
	public function __construct($comment)
	{
		$comment = explode("\n", $comment);
		foreach ($comment as &$line)
		{
			$line = preg_replace("/^(\s+)?\/\*[\*]+/", "", $line);
			$line = preg_replace("/^(\s+)?[\*]+\//", "", $line);
			$line = preg_replace("/^(\s+)?\*/", "", $line);
		}

		$document = new DOMDocument;
		if ($document->loadXML("<doc>" . implode("\n", $comment) . "</doc>"))
		{
			$this->document = $document;
		}
	}

	/**
	 * Get the description
	 *
	 * @return null|string The description
	 */
	public function getDescription()
	{
		return $this->getTagValue($this->document, "description");
	}

	/**
	 * Get a map of all parameters and their properties.
	 *
	 * @return StdClass A map of all parameters with the properties "name" (The name of the parameter), "type" (Variable type of the parameter), "required" (Whether the parameter is required), "description" (The description of the parameter).
	 */
	public function getParameters()
	{
		$parameters = new StdClass;

		/**
		 * @var $paramNode DOMElement
		 */
		foreach ($this->document->getElementsByTagName("param") as $paramNode)
		{
			$parameterData = new StdClass;

			$parameterData->type = $paramNode->getAttribute("type");
			$parameterData->required = $paramNode->getAttribute("required") == "true" ? true : false;

			$parameterData->description = $this->getNodeInnerHTML($paramNode);

			$parameters->{$paramNode->getAttribute("name")} = $parameterData;
		}

		return $parameters;
	}

	/**
	 * Get the content of the "response" element
	 *
	 * @return null|string The content
	 */
	public function getResponse()
	{
		return $this->getTagValue($this->document, "response");
	}

	/**
	 * Get the content of the element with the specified name in the given parent node
	 *
	 * @param DOMElement|DOMDocument $parentNode The parent node which contains the element
	 * @param string $name The name of the element
	 *
	 * @return null|string The content of the element
	 */
	private function getTagValue($parentNode, $name)
	{
		$node = $parentNode->getElementsByTagName($name);
		if ($node and $node->length)
		{
			return $this->getNodeInnerHTML($node->item(0));
		}

		return null;
	}

	/**
	 * Get the full text content including all elements of the specified DOM Node.
	 *
	 * @param DOMNode $node The DOM Node of which the text content should be extracted.
	 *
	 * @return string The text content of the node.
	 */
	private function getNodeInnerHTML($node)
	{
		$documentCopy = new DOMDocument();

		foreach($node->childNodes as $childNode)
		{
			$documentCopy->appendChild($documentCopy->importNode($childNode, true));
		}

		return trim($documentCopy->saveHTML());
	}
}