<?php
class Model_User extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'username',
		'password',
		'group',
		'email',
		'last_login',
		'login_hash',
		'profile_fields',
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

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
        if($factory == "create" || $factory == "edit_self"){
            $val->add_field('username', 'Username', 'required|max_length[50]');
            $val->add_field('password', 'New Password', 'required|max_length[255]');
        }
        if($factory == "edit_self"){
            $val->add_field('old_password', 'Old Password', 'required|max_length[255]');
        }
		$val->add_field('group', 'Group', 'required|max_length[255]');
		$val->add_field('email', 'Email', 'required|valid_email|max_length[255]');

		return $val;
	}

}
