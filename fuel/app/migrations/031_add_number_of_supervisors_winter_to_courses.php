<?php

namespace Fuel\Migrations;

class Add_number_of_supervisors_winter_to_courses
{
	public function up()
	{
		\DBUtil::add_fields('courses', array(
			'number_of_supervisors_winter' => array('constraint' => 11, 'type' => 'int'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('courses', array(
			'number_of_supervisors_winter'

		));
	}
}