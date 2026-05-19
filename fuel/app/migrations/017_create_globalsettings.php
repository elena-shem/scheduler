<?php

namespace Fuel\Migrations;

class Create_globalsettings
{
	public function up()
	{
		\DBUtil::create_table('globalsettings', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'linkexpirationdate' => array('type' => 'datetime'),
			'alllinksopen' => array('type' => 'tinyint'),
			'emaildriver' => array('constraint' => 50, 'type' => 'varchar'),
			'emailhtml' => array('type' => 'tinyint'),
			'emailcharset' => array('constraint' => 20, 'type' => 'varchar'),
			'emailencoding' => array('constraint' => '"8bit","base64","quoted-printable"', 'type' => 'enum'),
			'emailfrom' => array('constraint' => 250, 'type' => 'varchar'),
			'emailname' => array('constraint' => 250, 'type' => 'varchar'),
			'emailreturn' => array('constraint' => 250, 'type' => 'varchar'),
			'emailpathtosendmail' => array('constraint' => 250, 'type' => 'varchar'),
			'emailsmtphost' => array('constraint' => 250, 'type' => 'varchar'),
			'emailsmtpport' => array('constraint' => 20, 'type' => 'varchar'),
			'emailsmtpusername' => array('constraint' => 250, 'type' => 'varchar'),
			'emailsmtppassword' => array('constraint' => 250, 'type' => 'varchar'),
			'emailsmtptimeout' => array('constraint' => 20, 'type' => 'varchar'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('globalsettings');
	}
}