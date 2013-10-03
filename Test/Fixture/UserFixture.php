<?php
/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'status_id' => array('type' => 'integer', 'null' => false, 'default' => '1'),
		'deleted_date' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'username' => 'Dave',
			'email' => 'dave@example.com',
			'status_id' => '1',
			'deleted_date' => '0000-00-00 00:00:00',
			'created' => '2013-07-20 00:28:12',
			'modified' => '2013-07-20 00:28:12'
		),
		array(
			'id' => '2',
			'username' => 'Stu',
			'email' => 'stu@example.com',
			'status_id' => '1',
			'deleted_date' => '0000-00-00 00:00:00',
			'created' => '2013-07-20 00:28:12',
			'modified' => '2013-07-20 00:28:12'
		),
		array(
			'id' => '3',
			'username' => 'Adam',
			'email' => 'adam@example.com',
			'status_id' => '3',
			'deleted_date' => '0000-00-00 00:00:00',
			'created' => '2013-07-20 00:28:12',
			'modified' => '2013-07-20 00:28:12'
		),
		array(
			'id' => '4',
			'username' => 'Owen',
			'email' => 'owen@example.com',
			'status_id' => '1',
			'deleted_date' => '0000-00-00 00:00:00',
			'created' => '2013-07-20 00:28:12',
			'modified' => '2013-09-19 21:47:49'
		),
		array(
			'id' => '7',
			'username' => 'George',
			'email' => 'george@example.com',
			'status_id' => '6',
			'deleted_date' => '0000-00-00 00:00:00',
			'created' => '0000-00-00 00:00:00',
			'modified' => '0000-00-00 00:00:00'
		),
	);

}
