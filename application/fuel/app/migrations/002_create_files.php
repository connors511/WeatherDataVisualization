<?php

namespace Fuel\Migrations;

class Create_files
{
	public function up()
	{
		\DBUtil::create_table('files', array(
			'id' => array('type' => 'bigint', 'auto_increment' => true, 'unsigned' => true),
			'latitude' => array('type' => 'varchar', 'constraint' => 11),
			'longitude' => array('type' => 'varchar', 'constraint' => 11),
			'name' => array('type' => 'varchar', 'constraint' => 100),
			'path' => array('type' => 'text'),
			'type' => array('constraint' => 10, 'type' => 'varchar'),
			'offset' => array('constraint' => 50, 'type' => 'varchar'),
			'user_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('id'), true, 'InnoDB');
		//\DBUtil::create_index('files', 'user_id');
		\DBUtil::add_foreign_key('files', array(
		    'key' => 'user_id',
		    'reference' => array(
			'table' => 'users',
			'column' => 'id',
		    ),
		    'on_update' => 'NO ACTION',
		    'on_delete' => 'NO ACTION',
		));
	}

	public function down()
	{
		\DBUtil::drop_table('files');
	}
}