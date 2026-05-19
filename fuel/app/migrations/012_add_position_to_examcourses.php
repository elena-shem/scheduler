<?php

namespace Fuel\Migrations;

class Add_position_to_examcourses
{
	public function up()
	{
		\DBUtil::add_fields('examcourses', array(
			'position' => array('constraint' => 4, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('examcourses', array(
			'position'

		));
	}
}