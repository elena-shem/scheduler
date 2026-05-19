<?php

namespace Fuel\Migrations;

class Add_size_to_uploads
{
	public function up()
	{
		\DBUtil::add_fields('uploads', array(
			'size' => array('constraint' => 11, 'type' => 'int'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('uploads', array(
			'size'

		));
	}
}