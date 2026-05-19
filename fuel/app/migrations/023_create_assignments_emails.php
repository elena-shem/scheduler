<?php

namespace Fuel\Migrations;

class Create_assignments_emails
{
	public function up()
	{
		\DBUtil::create_table('assignments_emails', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'examperiod_id' => array('constraint' => 11, 'type' => 'int'),
			'doctoral_id' => array('constraint' => 11, 'type' => 'int'),
			'content' => array('type' => 'text'),
			'title' => array('type' => 'text'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('assignments_emails');
	}
}