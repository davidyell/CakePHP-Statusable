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
	public $fixtures = array('plugin.statusable.user');
	
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

	public function testGetStatses() {
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
 * TODO: Finish writing this test
 */
	public function testDelete() {
		$this->Model->data['Example']['id'] = 1;
//		$result = $this->Model->delete($this->Model, 1, true);
		$result = $this->Model->beforeDelete($this->Model);
		var_dump($result);
	}
}