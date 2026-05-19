<?php

namespace Fuel\Migrations;

class Create_examcourses
{
	public function up()
	{
		\DBUtil::create_table('examcourses', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'examperiods_id' => array('constraint' => 11, 'type' => 'int'),
			'examdays_id' => array('constraint' => 11, 'type' => 'int'),
			'examhours_id' => array('constraint' => 11, 'type' => 'int'),
			'courses_id' => array('constraint' => 11, 'type' => 'int'),
			'position_x' => array('constraint' => 11, 'type' => 'int'),
			'position_y' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('examcourses');
	}
}