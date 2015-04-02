<?php

namespace hypeJunction\Categories\Di;

use PHPUnit_Framework_TestCase;

class PluginContainerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var PluginContainer
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new PluginContainer;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}

	public function testConstructor() {
		$this->assertNotNull($this->object);
	}

}
