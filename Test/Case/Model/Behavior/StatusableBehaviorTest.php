<?php
App::uses('Statusable.StatusableBehavior', 'Model/Behavior');

/**
 * Description of StatusableBehaviorTest
 *
 * @author David Yell <neon1024@gmail.com>
 */

class Example extends CakeTestModel {
	public $useTable = 'users';
	public $actsAs = array('Statusable.Statusable');
}

class StatusableBehaviorTest extends CakeTestCase {
	
/**
 * Load test database fixtures
 * 
 * @var array
 */
	public $fixtures = array(
		'plugin.statusable.user',
		'plugin.statusable.status'
	);
	
/**
 * Setup the test
 */
	public function setUp() {
		parent::setUp();
		
		$this->Model = new Example;
	}

/**
 * Clear up when the test is complete
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * Check that the statuses can be pulled from the behaviour configuration
 * 
 * @return void
 */
	public function testGetStatuses() {
		$expected = array(
			'displayed' => array(
				1 => 'Live',            // Displayed on the site, everyone can see it
				3 => 'Protected live',  // Cannot be deleted, but is displayed
			),
			'adminOnly' => array(
				2 => 'Inactive',            // Only displayed to administrators
				4 => 'Protected inactive',  // Cannot be deleted, but is not displayed
				5 => 'Archived',            // No longer in use but might be needed
			),
			'deleted' => array(
				6 => 'Deleted'  // Will not display to admins or users
			)
		);
		
		$result = $this->Model->getStatuses();
		
		$this->assertEqual($result, $expected);
	}
	
/**
 * Provide data for testing the beforeFind method
 * 
 * @return array
 */
	public function providerBeforeFind() {
		return array(
			array(
				'prefix' => '',
				'expected' => array(
					'order' => 'username ASC',
					'conditions' => array(
						'status_id' => array(1,3),
					)
				),
			),
			array(
				'prefix' => 'admin',
				'expected' => array(
					'order' => 'username ASC',
					'conditions' => array(
						'status_id' => array(1,3,2,4,5,6),
					)
				),
			),
		);
	}
	
/**
 * @dataProvider providerBeforeFind
 * 
 * @return void
 */
	public function testBeforeFind($prefix, $expected) {
		$this->Model->prefix = $prefix;
		
		$query = array(
			'order' => 'username ASC'
		);
		
		$result = $this->Model->buildQuery('first', $query);
		
		$this->assertContains('status_id', $result);
		$this->assertContains('username ASC', $result);
	}
	
/**
 * Ensure that when deleting an item it will not be shown again
 * 
 * @return void
 */
	public function testDelete() {
		$this->Model->data['Example']['id'] = 1;
		$this->Model->delete(1);
		
		$result = $this->Model->find('first', [
			'conditions' => [
				'id' => 1
			]
		]);
		
		$this->assertEmpty($result);
	}
	
/**
 * Make sure that if an item is protected that it cannot be deleted
 * 
 * @return void
 */
	public function testDeleteProtected() {
		$this->Model->prefix = 'admin';
		$this->Model->data['Example']['id'] = 3;
		$result = $this->Model->delete(3);
		$expected = false;
		
		$this->assertEqual($result, $expected);
	}
}