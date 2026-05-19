<?php

namespace Fuel\Migrations;

class Create_assignments_preferences
{
	public function up()
	{
		\DBUtil::create_table('assignments_preferences', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'examperiod_id' => array('constraint' => 11, 'type' => 'int'),
			'doctoral_id' => array('constraint' => 11, 'type' => 'int'),
			'hours_remaining_tmp' => array('constraint' => 11, 'type' => 'int'),
			'density' => array('constraint' => 15, 'type' => 'varchar'),
			'density_day' => array('constraint' => 11, 'type' => 'int'),
			'availabilities' => array('type' => 'text'),
			'max_assignments' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('assignments_preferences');
	}
}