<?php

namespace Fuel\Migrations;

class Create_doctorals
{
	public function up()
	{
		\DBUtil::create_table('doctorals', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 128, 'type' => 'varchar'),
			'surname' => array('constraint' => 128, 'type' => 'varchar'),
			'email' => array('constraint' => 255, 'type' => 'varchar'),
			'active' => array('type' => 'boolean'),
			'hours_remaining' => array('constraint' => 11, 'type' => 'int'),
			'hours_completed' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('doctorals');
	}
}