<?php

namespace Fuel\Migrations;

class Create_welcomes
{
	public function up()
	{
		\DBUtil::create_table('welcomes', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'text' => array('constraint' => 255, 'type' => 'varchar'),
			'imagepath' => array('constraint' => 255, 'type' => 'varchar'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('welcomes');
	}
}