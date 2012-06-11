<?php

namespace Fuel\Migrations;

class Create_file_wrks
{
	public function up()
	{
		\DBUtil::create_table('file_wrks', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'sig' => array('constraint' => 1, 'type' => 'char'),
			'tot_bytes' => array('constraint' => 3, 'type' => 'varchar'),
			'trailer_offset' => array('constraint' => 3, 'type' => 'varchar'),
			'trailer_size' => array('constraint' => 2, 'type' => 'varchar'),
			'img_type' => array('constraint' => 1, 'type' => 'char'),
			'mm_predict' => array('type' => 'smallint'),
			'pixel_size' => array('type' => 'smallint'),
			'date_time' => array('constraint' => 4, 'type' => 'char'),
			'rad_name' => array('constraint' => 20, 'type' => 'varchar'),
			'east_uppb' => array('type' => 'smallint'),
			'north_uppb' => array('type' => 'smallint'),
			'hei_uppb' => array('constraint' => 3, 'type' => 'varchar'),
			'store_slope' => array('constraint' => 4, 'type' => 'varchar'),
			'store_icept' => array('constraint' => 6, 'type' => 'varchar'),
			'store_offset' => array('constraint' => 6, 'type' => 'varchar'),
			'store_quant' => array('constraint' => 8, 'type' => 'varchar'),
			'signal2' => array('constraint' => 8, 'type' => 'char'),
			'pixel_values' => array('type' => 'mediumtext'),
			'file_id' => array('constraint' => 11, 'type' => 'bigint', 'unsigned' => true),

		), array('id'), true, 'InnoDB');
		//\DBUtil::create_index('file_wrks', 'file_id');
		\DBUtil::add_foreign_key('file_wrks', array(
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
		\DBUtil::drop_table('file_wrks');
	}
}