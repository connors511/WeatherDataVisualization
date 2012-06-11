<?php

namespace Fuel\Migrations;

class Create_file_csvs
{
	public function up()
	{
		\DBUtil::create_table('file_csvs', array(
			'id' => array('type' => 'bigint', 'auto_increment' => true, 'unsigned' => true),
			'TimeStamps' => array('constraint' => 12, 'type' => 'char'),
			'PossiblePower' => array('constraint' => 10, 'type' => 'varchar'),
			'WindSpeed' => array('constraint' => 10, 'type' => 'varchar'),
			'RegimePossible' => array('constraint' => 10, 'type' => 'varchar'),
			'OutputPower' => array('constraint' => 10, 'type' => 'varchar'),
			'RegimeOutput' => array('constraint' => 10, 'type' => 'varchar'),
			'TimeStampsR' => array('constraint' => 19, 'type' => 'char'),
			'file_id' => array('type' => 'bigint', 'unsigned' => true),
		), array('id'), true, 'InnoDB');
		//\DBUtil::create_index('file_csvs', 'file_id');
		\DBUtil::add_foreign_key('file_csvs', array(
		    'key' => 'file_id',
		    'reference' => array(
			'table' => 'files',
			'column' => 'id',
		    ),
		    'on_update' => 'NO ACTION',
		    'on_delete' => 'CASCADE',
		));
	}

	public function down()
	{
		\DBUtil::drop_table('file_csvs');
	}
}