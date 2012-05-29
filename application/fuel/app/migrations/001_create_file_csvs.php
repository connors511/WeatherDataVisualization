<?php

namespace Fuel\Migrations;

class Create_file_csvs
{
	public function up()
	{
		\DBUtil::create_table('file_csvs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'TimeStamps' => array('constraint' => 10, 'type' => 'varchar'),
			'PossiblePower' => array('constraint' => 45, 'type' => 'varchar'),
			'WindSpeed' => array('constraint' => 45, 'type' => 'varchar'),
			'RegimePossible' => array('constraint' => 45, 'type' => 'varchar'),
			'OutputPower' => array('constraint' => 45, 'type' => 'varchar'),
			'RegimeOutput' => array('constraint' => 45, 'type' => 'varchar'),
			'TimeStampsR' => array('constraint' => 19, 'type' => 'varchar'),
			'file_id' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('file_csvs');
	}
}