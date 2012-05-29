<?php

class Model_File_Csv extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'TimeStamps',
		'PossiblePower',
		'WindSpeed',
		'RegimePossible',
		'OutputPower',
		'RegimeOutput',
		'TimeStampsR',
		'file_id'
	);
	
	protected static $_belongs_to = array(
		'file' => array(
			'key_from'		=> 'file_id',
			'model_to'		=> 'Model_File',
			'key_to'		=> 'id',
			'cascade_save'		=> true,
			'cascade_delete'	=> false,
		)
	);
        
        public static function has_key($key) {
            return in_array($key,Model_File_Csv::$_properties);
        }

}
