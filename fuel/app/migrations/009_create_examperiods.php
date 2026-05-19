<?php

namespace Fuel\Migrations;

class Create_examperiods
{
	public function up()
	{
		\DBUtil::create_table('examperiods', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'season' => array('constraint' => '"Xeimerinh","Earinh","Septemprh"', 'type' => 'enum'),
			'academic_year' => array('constraint' => 32, 'type' => 'varchar'),
			'start' => array('type' => 'date'),
			'end' => array('type' => 'date'),
			'comment' => array('type' => 'text'),
            'active' => array('type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('examperiods');
	}
}