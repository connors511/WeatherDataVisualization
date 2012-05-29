<?php

namespace Fuel\Migrations;

class Add_fields_to_files
{
	public function up()
	{
		\DBUtil::add_fields('files', array(
			'latitude' => array('type' => 'varchar', 'constraint' => 11),
			'longitude' => array('type' => 'varchar', 'constraint' => 11),
			'name' => array('type' => 'varchar', 'constraint' => 255),
		));	
	}

	public function down()
	{
		\DBUtil::drop_fields('files', array(
		    'latitude',
		    'longitude',
		    'name'
		));
	}
}