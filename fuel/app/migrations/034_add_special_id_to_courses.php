<?php

namespace Fuel\Migrations;

class Add_special_id_to_courses
{
	public function up()
	{
		\DBUtil::add_fields('courses', array(
			'special_id' => array('constraint' => 256, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('courses', array(
			'special_id'

		));
	}
}