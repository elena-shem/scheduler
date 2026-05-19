<?php

namespace Fuel\Migrations;

class Add_registrationdate_to_doctorals
{
	public function up()
	{
		\DBUtil::add_fields('doctorals', array(
			'registrationDate' => array('constraint' => 255, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('doctorals', array(
			'registrationDate'

		));
	}
}