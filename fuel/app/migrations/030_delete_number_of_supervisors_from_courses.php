<?php

namespace Fuel\Migrations;

class Delete_number_of_supervisors_from_courses
{
	public function up()
	{
		\DBUtil::drop_fields('courses', array(
			'number_of_supervisors'

		));
	}

	public function down()
	{
		\DBUtil::add_fields('courses', array(
			'number_of_supervisors' => array('constraint' => 11, 'type' => 'int'),

		));
	}
}