<?php
/**
 * StatusFixture
 *
 * @author David Yell <neon1024@gmail.com>
 */
class StatusFixture extends CakeTestFixture {
	
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'latin1', 'collage' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
	];
	
	public $records = [
		['id' => 1, 'name' => 'Live', 'created' => '2013-07-20 00:28:12', 'modified' => '2013-07-20 00:28:12'],
		['id' => 2, 'name' => 'Inactive', 'created' => '2013-07-20 00:28:12', 'modified' => '2013-07-20 00:28:12'],
		['id' => 3, 'name' => 'Protected live', 'created' => '2013-07-20 00:28:12', 'modified' => '2013-07-20 00:28:12'],
		['id' => 4, 'name' => 'Protected inactive', 'created' => '2013-07-20 00:28:12', 'modified' => '2013-07-20 00:28:12'],
		['id' => 5, 'name' => 'Archived', 'created' => '2013-07-20 00:28:12', 'modified' => '2013-07-20 00:28:12'],
		['id' => 6, 'name' => 'Deleted', 'created' => '2013-07-20 00:28:12', 'modified' => '2013-07-20 00:28:12'],
	];
}