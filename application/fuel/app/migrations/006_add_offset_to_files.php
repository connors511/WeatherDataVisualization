<?php

namespace Fuel\Migrations;

class Add_offset_to_files
{
	public function up()
	{
		\DBUtil::add_fields('files', array(
			'offset' => array('constraint' => 255, 'type' => 'varchar'),

		));	
	}

	public function down()
	{
		\DBUtil::drop_fields('files', array(
			'offset'
    
		));
	}
}