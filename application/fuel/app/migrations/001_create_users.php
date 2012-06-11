<?php

namespace Fuel\Migrations;

class Create_users
{
	public function up()
	{
		\DBUtil::create_table('users', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'username' => array('constraint' => 50, 'type' => 'varchar'),
			'password' => array('constraint' => 44, 'type' => 'varchar'),
			'group' => array('constraint' => 3, 'type' => 'tinyint'),
			'email' => array('constraint' => 100, 'type' => 'varchar'),
			'last_login' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'login_hash' => array('constraint' => 40, 'type' => 'varchar'),
			'profile_fields' => array('type' => 'text'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('id'), true, 'InnoDB');
	}

	public function down()
	{
		\DBUtil::drop_table('users');
	}
}