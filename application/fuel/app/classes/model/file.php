<?php

class Model_File extends \Orm\Model {

	protected static $_properties = array(
	    'id',
	    'name' => array(
		'data_type' => 'varchar',
		'label' => 'Name',
		'form' => array('type' => false),
	    ),
	    'latitude' => array(
		'data_type' => 'varchar',
		'label' => 'Latitude',
		'form' => array('type' => false),
	    ),
	    'longitude' => array(
		'data_type' => 'varchar',
		'label' => 'Longitude',
		'form' => array('type' => false),
	    ),
	    'path' => array(
		'data_type' => 'text',
		'label' => 'File',
		'form' => array('type' => 'file', 'class' => 'span6'),
	    ),
	    'type' => array(
		'data_type' => 'varchar',
		'label' => 'Type',
		'form' => array('type' => false),
	    ),
	    'offset' => array(
		'data_type' => 'varchar',
		'label' => 'Timezone',
		'form' => array('type' => 'select', 'class' => 'span3', 'options' => array(),'value'=>'','description'=>'All dates will be converted to UTC'),
	    ),
	    'user_id' => array(
		'data_type' => 'int',
		'label' => 'User ID',
		'form' => array('type' => false),
	    ),
	    'created_at' => array(
		'data_type' => 'int',
		'label' => 'Created At',
		'form' => array('type' => false),
	    ),
	    'updated_at' => array(
		'data_type' => 'int',
		'label' => 'Updated At',
		'form' => array('type' => false),
	    ),
	);
	protected static $_has_many = array(
	    'file_csvs' => array(
		'cascade_save' => false,
		'cascade_delete' => false,
	    ),
	);
	protected static $_belongs_to = array(
	    'user'
	);
	protected static $_observers = array(
	    'Orm\Observer_CreatedAt' => array(
		'events' => array('before_insert'),
		'mysql_timestamp' => false,
	    ),
	    'Orm\Observer_UpdatedAt' => array(
		'events' => array('before_save'),
		'mysql_timestamp' => false,
	    ),
	    'Observer_CreatedUser' => array(
		'events' => array('before_insert'),
	    ),
	    'Orm\\Observer_Validation' => array(
		'events' => array('before_save'),
	    ),
	);

	public static function _init() {
		self::$_properties['offset']['form']['options'] = self::timezone_options();
		self::$_properties['offset']['form']['value'] = date_default_timezone_get();
	}

	public static function timezone_options() {

		//$timezones = array('Africa','America','Antarctica','Arctic','Asia','Atlantic','Australia','Europe','Indian','Pacific','UTC');

		foreach (DateTimeZone::listIdentifiers() as $zone) {
			$zone = explode('/', $zone); // 0 => Continent, 1 => City
			//if (in_array($zone[0], $timezones)) {
			if (isset($zone[1]) != '') {
				$locations[$zone[0]][$zone[0] . '/' . $zone[1]] = str_replace('_', ' ', $zone[1]);
			} else {
				$locations[$zone[0]][$zone[0]] = $zone[0];
			}
			//}
		}
		return array();
		return $locations;
	}

}
