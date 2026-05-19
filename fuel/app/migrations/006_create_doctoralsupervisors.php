<?php

namespace Fuel\Migrations;

class Create_doctoralsupervisors
{
	public function up()
	{
		\DBUtil::create_table('doctoralsupervisors', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'doctoral_id' => array('constraint' => 11, 'type' => 'int'),
			'professor_id' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('doctoralsupervisors');
	}
}