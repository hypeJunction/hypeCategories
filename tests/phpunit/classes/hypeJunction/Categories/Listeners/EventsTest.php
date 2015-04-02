<?php

namespace hypeJunction\Categories\Listeners;

use PHPUnit_Framework_TestCase;

class EventsTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Events
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$plugin = $this->getMockBuilder('\ElggPlugin')->disableOriginalConstructor()->getMock();
		$config = new \hypeJunction\Categories\Config\Config($plugin);
		$router = new \hypeJunction\Categories\Services\Router($config);
		$model = new \hypeJunction\Categories\Models\Model($config);
		$upgrades = new \hypeJunction\Categories\Services\Upgrades($config, $model);
		$this->object = new Events($config, $router, $model, $upgrades);
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

	public function testInit() {
		$this->object->init();

		$this->assertFalse(elgg_unregister_event_handler('create', 'all', array($this->object, 'fooBar')));

		$this->assertTrue(elgg_unregister_event_handler('pagesetup', 'system', array($this->object, 'pagesetup')));
		$this->assertTrue(elgg_unregister_event_handler('upgrade', 'system', array($this->object, 'upgrade')));
		$this->assertTrue(elgg_unregister_event_handler('create', 'all', array($this->object, 'updateEntityCategories')));
		$this->assertTrue(elgg_unregister_event_handler('update', 'all', array($this->object, 'updateEntityCategories')));
	}

	public function testPagesetup() {
		$this->markTestIncomplete();
	}

	public function testUpgrade() {
		$this->markTestIncomplete();
	}

	public function testUpdateEntityCategories() {
		$this->markTestIncomplete();
	}

}
