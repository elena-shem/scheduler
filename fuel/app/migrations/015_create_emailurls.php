<?php

namespace Fuel\Migrations;

class Create_emailurls
{
	public function up()
	{
		\DBUtil::create_table('emailurls', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'doctoral_id' => array('constraint' => 11, 'type' => 'int'),
			'token' => array('constraint' => 128, 'type' => 'char'),
			'sent' => array('constraint' => 1, 'type' => 'tinyint'),
			'used' => array('constraint' => 1, 'type' => 'tinyint'),
			'mail_id' => array('constraint' => 11, 'type' => 'int'),
			'valid_until' => array('type' => 'datetime'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('emailurls');
	}
}