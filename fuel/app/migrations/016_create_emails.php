<?php

namespace Fuel\Migrations;

class Create_emails
{
	public function up()
	{
		\DBUtil::create_table('emails', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'html_content' => array('type' => 'text'),
			'text_content' => array('type' => 'text'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('emails');
	}
}