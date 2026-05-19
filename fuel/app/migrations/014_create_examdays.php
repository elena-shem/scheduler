<?php

namespace Fuel\Migrations;

class Create_examdays
{
	public function up()
	{
		\DBUtil::create_table('examdays', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'examperiods_id' => array('constraint' => 11, 'type' => 'int'),
			'exam_date' => array('type' => 'datetime'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('examdays');
	}
}