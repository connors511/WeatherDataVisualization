<?php

class Model_File extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'path',
		'type',
		'timestamp',
		'user_id'
	);

	protected static $_has_many = array(
		'file_csvs' => array(
			'key_from'		=> 'id',
			'model_to'		=> 'Model_File_Csv',
			'key_to'		=> 'file_id',
			'cascade_save'		=> true,
			'cascade_delete'	=> true,
		)
	);

}
