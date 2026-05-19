<?php

namespace Fuel\Migrations;

class Add_sendemail_to_doctorals
{
	public function up()
	{
		\DBUtil::add_fields('doctorals', array(
			'sendemail' => array('type' => 'tinyint'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('doctorals', array(
			'sendemail'

		));
	}
}