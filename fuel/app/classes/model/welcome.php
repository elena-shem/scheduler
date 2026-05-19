<?php
use Orm\Model;

class Model_Welcome extends Model
{
	protected static $_properties = array(
		'id',
		'text',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

    protected static $_table_name = 'welcomeposts';

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('text', 'Text', 'required');

		return $val;
	}

}
