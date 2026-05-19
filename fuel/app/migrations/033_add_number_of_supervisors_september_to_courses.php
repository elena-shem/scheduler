<?php

namespace Fuel\Migrations;

class Add_number_of_supervisors_september_to_courses
{
	public function up()
	{
		\DBUtil::add_fields('courses', array(
			'number_of_supervisors_september' => array('constraint' => 11, 'type' => 'int'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('courses', array(
			'number_of_supervisors_september'

		));
	}
}