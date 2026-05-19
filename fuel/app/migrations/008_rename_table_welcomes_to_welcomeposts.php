<?php

namespace Fuel\Migrations;

class Rename_table_welcomes_to_welcomeposts
{
	public function up()
	{
		\DBUtil::rename_table('welcomes', 'welcomeposts');
	}

	public function down()
	{
		\DBUtil::rename_table('welcomeposts', 'welcomes');
	}
}