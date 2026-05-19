<?php

namespace Fuel\Migrations;

class Add_emailpriority_to_globalsettings
{
	public function up()
	{
		\DBUtil::add_fields('globalsettings', array(
			'emailpriority' => array('constraint' => "'5 (Lowest)','4 (Low)','3 (Normal)','2 (High)','1 (Highest)'",'type' => 'enum'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('globalsettings', array(
			'emailpriority'

		));
	}
}