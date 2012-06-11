<?php

class Model_File_Csv extends \Orm\Model
{
	// The order is important and should reflect the actual data file!
	protected static $_properties = array(
		'id',
		'TimeStamps',
		'PossiblePower',
		'WindSpeed',
		'RegimePossible',
		'OutputPower',
		'RegimeOutput',
		'TimeStampsR',
		'file_id' //has to be last. is appended later in data file
	);
	
	// Relations set on SQL level
	protected static $_belongs_to = array(
		'file' => array(
			'cascade_save'		=> false,
			'cascade_delete'	=> false,
		)
	);
        
        public static function has_key($key) {
            return in_array($key,self::$_properties);
        }
	
	public static function get_columns($include_id = false) {
		$properties = self::$_properties;
		if(!$include_id)
			unset($properties[array_search('id', $properties)]);
		return $properties;
	}

}
