<?php
require_once __DIR__ . "/../includes/Utils.class.php";

class UtilsTest extends PHPUnit_Framework_TestCase
{
	public function testBuildUrlFull()
	{
		$url = "http://example.com:8443/path/to/site?q1=true&q2=abc#jump-to-top";

		$parsedUrl = parse_url($url);

		$this->assertEquals($url, Utils::buildUrl($parsedUrl));
	}

	public function testBuildUrlFullNoFragment()
	{
		$url = "http://example.com:8443/path/to/site?q1=true&q2=abc";

		$parsedUrl = parse_url($url);

		$this->assertEquals($url, Utils::buildUrl($parsedUrl));
	}

	public function testBuildUrlHost()
	{
		$url = "http://example.com";

		$parsedUrl = parse_url($url);

		$this->assertEquals($url, Utils::buildUrl($parsedUrl));
	}

	public function testBuildUrlHostPort()
	{
		$url = "http://example.com:8443";

		$parsedUrl = parse_url($url);

		$this->assertEquals($url, Utils::buildUrl($parsedUrl));
	}

	public function testBuildUrlPath()
	{
		$url = "http://example.com/path/to/site";

		$parsedUrl = parse_url($url);

		$this->assertEquals($url, Utils::buildUrl($parsedUrl));
	}

	public function testBuildUrlCustomScheme()
	{
		$url = "myscheme://example.com";

		$parsedUrl = parse_url($url);

		$this->assertEquals($url, Utils::buildUrl($parsedUrl));
	}

	public function testCastValueStringToInteger()
	{
		$value = Utils::castValue("123", "integer");

		$this->assertInternalType("integer", $value);
		$this->assertEquals(123, $value);
	}

	public function testCastValueNull()
	{
		$this->assertNull(Utils::castValue(null, "integer", true));
	}

	public function testFormatFileSize()
	{
		$this->assertEquals("1.45 TB", Utils::formatFileSize(1024 * 1024 * 1024 * 1024 * 1.45));
	}

	public function testFormatTimeDays()
	{
		$this->assertEquals("10 days 05:52:12", Utils::formatTime(885132));
	}

	public function testFormatTimeDay()
	{
		$this->assertEquals("1 day 02:11:15", Utils::formatTime(94275));
	}

	public function testFormatTime()
	{
		$this->assertEquals("12:34:56", Utils::formatTime(45296));
	}
}