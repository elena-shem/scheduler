<?php

namespace Fuel\Migrations;

class Add_telephone_to_doctorals
{
	public function up()
	{
		\DBUtil::add_fields('doctorals', array(
			'telephone' => array('constraint' => 255, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('doctorals', array(
			'telephone'

		));
	}
}