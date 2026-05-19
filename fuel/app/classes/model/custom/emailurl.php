<?php

class Model_Custom_Emailurl extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'doctoral_id',
		'token',
		'sent',
		'used',
		'mail_id',
        'answer',
        'logical_1',
        'logical_2',
		'valid_until',
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
	protected static $_table_name = 'custom_emailurls';

}
