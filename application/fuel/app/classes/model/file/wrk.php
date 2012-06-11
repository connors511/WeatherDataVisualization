<?php

class Model_File_Wrk extends \Orm\Model
{
	// The order is important and should reflect the actual data file!
	protected static $_properties = array(    
		'id',
		'sig',
		'tot_bytes',
		'trailer_offset',
		'trailer_size',
		'img_type',
		'mm_predict',
		'pixel_size',
		'date_time',
		'east_uppb',
		'north_uppb',
		'hei_uppb',
		'store_slope',
		'store_icept',
		'store_offset',
		'store_quant',
		'signal2',
		'pixel_values',
		'file_id' //has to be last. is appended later in data file
	);
	
	// Relations set on SQL level
	protected static $_belongs_to = array(
		'file' => array(
			'cascade_save'		=> false,
			'cascade_delete'	=> false,
		)
	);
	
	public static function get_columns($include_id = false) {
		$properties = self::$_properties;
		if(!$include_id)
			unset($properties[array_search('id', $properties)]);
		return $properties;
	}

}
