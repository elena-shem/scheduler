<?php

namespace Fuel\Migrations;

class Drop_examcourses
{
	public function up()
	{
		\DBUtil::drop_table('examcourses');
	}

	public function down()
	{
		\DBUtil::create_table('examcourses', array(
			'id' => array('type' => 'int unsigned', 'null' => true, 'auto_increment' => true),
			'period_id' => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'course_id' => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'day' => array('type' => 'date', 'null' => true),
			'hour' => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'created_at' => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'updated_at' => array('type' => 'int', 'null' => true, 'constraint' => 11),
			'position' => array('type' => 'varchar', 'null' => true, 'constraint' => 4),

		), array('id'));

	}
}