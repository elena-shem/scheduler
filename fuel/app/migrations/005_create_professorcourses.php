<?php

namespace Fuel\Migrations;

class Create_professorcourses
{
	public function up()
	{
		\DBUtil::create_table('professorcourses', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'professor_id' => array('constraint' => 11, 'type' => 'int'),
			'course_id' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('professorcourses');
	}
}