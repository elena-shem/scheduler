<?php

namespace Fuel\Migrations;

class Add_overridelinkexpirationdate_to_globalsettings
{
	public function up()
	{
		\DBUtil::add_fields('globalsettings', array(
			'overridelinkexpirationdate' => array('type' => 'tinyint'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('globalsettings', array(
			'overridelinkexpirationdate'

		));
	}
}