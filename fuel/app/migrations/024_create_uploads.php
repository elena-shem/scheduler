<?php

namespace Fuel\Migrations;

class Create_uploads
{
	public function up()
	{
		\DBUtil::create_table('uploads', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'friendly_name' => array('constraint' => 1024, 'type' => 'varchar'),
			'file_name' => array('constraint' => 256, 'type' => 'varchar'),
			'file_path' => array('constraint' => 1024, 'type' => 'varchar'),
			'used' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('uploads');
	}
}