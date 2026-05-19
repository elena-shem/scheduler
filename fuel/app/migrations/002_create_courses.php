<?php

namespace Fuel\Migrations;

class Create_courses
{
	public function up()
	{
		\DBUtil::create_table('courses', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'code' => array('constraint' => 64, 'type' => 'varchar'),
			'title' => array('constraint' => 255, 'type' => 'varchar'),
			'number_of_supervisors' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('courses');
	}
}