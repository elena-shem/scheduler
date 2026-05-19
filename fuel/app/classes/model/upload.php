<?php

class Model_Upload extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'friendly_name',
		'file_name',
		'file_path',
		'used',
		'uploaderId',
		'size',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'uploads';

}
